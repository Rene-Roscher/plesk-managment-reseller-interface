<?php

namespace App\API;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Paysafecard\PaysafecardPaymentController;
use Paysafecard\SOPGClassicMerchantClient;
use RuntimeException;
use InvalidArgumentException;

class PaymentHandlerAPI extends Model
{
    protected $table = 'transactions';
    static $sandbox = false;

    static $mwst = 19;
    static $pp_api = null;
    static $psc_api = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    static function paypal() : ApiContext
    {
        if(!self::$pp_api) {
            self::$pp_api = new ApiContext(new OAuthTokenCredential(env("PAYPAL_CLIENT_ID"), env("PAYPAL_CLIENT_SECRET")));
            self::$pp_api->setConfig([
                'mode' => (self::$sandbox ? "sandbox" : 'live')
            ]);
        }

        return self::$pp_api;
    }

    static function paysafecard() : SOPGClassicMerchantClient
    {
        if(!self::$psc_api) {
            self::$psc_api = new SOPGClassicMerchantClient(true, "de", false, (self::$sandbox ? "test" : 'live'));
            self::$psc_api ->merchant(env('PAYSAFECARD_USER'), env('PAYSAFECARD_KEY'));
        }

        return self::$psc_api;
    }

    static function generateMtid() : int
    {
        do {
            $mtid = rand(10000,99999);
        }while(self::whereMtid($mtid)->exists());

        return $mtid;
    }

    static function initPayment(string $type, $amount, string $productName, $user, $ok, $nok, $notify, $description)
    {
        $amount = number_format(round($amount, 2), 2, '.', '');

        $transaction = new self;
        $transaction->amount = $amount;
        $transaction->mtid = self::generateMtid();
        $transaction->description = $description;
        $transaction->state = "PENDING";
        $transaction->type = $type;
        $transaction->typ = 'API';

        $transaction->user()->associate($user);
        $transaction->save();

        $errors = [];
        $response = '';

        if($amount <= 0)
            throw new InvalidArgumentException("Invalid payment amount.");

        switch($type)
        {
            case "PAYPAL":
                $pp_details = new Details();
                $pp_details->setShipping(0)->setTax(0)->setSubtotal($amount);

                $item1 = new Item();
                $item1->setName($productName)->setCurrency('EUR')->setQuantity(1)->setSku($transaction->mtid)->setTax(round($amount/(1 + 19 / 100)*(19 / 100),2))->setPrice($amount);

                $itemList = new ItemList();
                $itemList->setItems(array($item1));

                $pp_amount = new Amount();
                $pp_amount->setCurrency("EUR")->setTotal($amount)->setDetails($pp_details);

                $pp_payer = new Payer();
                $pp_payer->setPaymentMethod("paypal");

                $pp_transaction = new Transaction();
                $pp_transaction->setAmount($pp_amount)->setInvoiceNumber($transaction->mtid)->setItemList($itemList)->setDescription($productName);

                $pp_urls = new RedirectUrls();
                $pp_urls->setReturnUrl(route('api.payment.paypal.success'))->setCancelUrl(route('api.payment.paypal.error'));

                $pp_payment = new Payment();
                $pp_payment->setIntent("sale")->setPayer($pp_payer)->setRedirectUrls($pp_urls)->setTransactions([$pp_transaction]);

                $pp_payment->create(self::paypal());
                $response = $pp_payment->getApprovalLink();

                $transaction->token = $pp_payment->getToken();
                $transaction->save();

                break;
            case "PAYSAFECARD":
                $psc = new PaysafecardPaymentController(env('PSC_KEY'), "PRODUCTION");
                $currency = "EUR";
                $customer_id = $user->id;
                $customer_ip = '0.0.0.0';
                $okurl = url('api/payment/paysafecard/success?payment={payment_id}');
                $errurl = url('api/payment/paysafecard/error?payment={payment_id}');
                $notifyurl = url('api/payment/paysafecard/notify?payment={payment_id}');

                $response = $psc->createPayment($amount, $currency, $customer_id, $customer_ip, $okurl, $errurl, $notifyurl);

                if ($response == false) {
                    $err = $psc->getError();

                    $transaction->state = 'ERROR';
                    $transaction->save();

                    /*
                     * To high amount.
                     */
                    if (($err['number'] == 4003)) {
                        $errors += array(strval($err['number']) => 'To high amount of transaction.');
                    } else {
                        $errors += array(strval($err['number']) => 'Please contact the support!');
                    }
                } else if (isset($response["object"])) {

                    if (isset($response["redirect"])) {
                        $transaction->mtid = $response['id'];
                        $transaction->save();
                        $response = $response["redirect"]["auth_url"];
                    }

                }

                break;

            default:
                throw new InvalidArgumentException("Can't find payment type: " . $type);
                break;
        }

        $transaction->url_ok = str_replace('{mtid}', $transaction->mtid, $ok);
        $transaction->url_nok = str_replace('{mtid}', $transaction->mtid, $nok);
        $transaction->url_notify = str_replace('{mtid}', $transaction->mtid, $notify);
        $transaction->save();

        $trans = [
            'id' => $transaction->id,
            'mtid' => $transaction->mtid,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'token' => $transaction->token,
        ];

        return array('payment' => $response, 'transaction' => $trans, 'errors' => $errors);
    }

    static function confirmPaysafecardPayment($request) {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);

        if($validator->fails())
            return null;

        $transaction = self::where("state", "PENDING")->where("type", "PAYSAFECARD")->find($request->id);

        if(!$transaction)
            throw new RuntimeException("Can't find transaction object.");

        $psc = self::paysafecard();
        $status = $psc->getSerialNumbers($transaction->mtid, 'EUR', '');

        if ($status === 'execute') {
            $execute = $psc->executeDebit(number_format($transaction->amount, 2, '.', ''), '1');

            if ($execute == true) {
                $transaction->state = "SUCCESS";
                $transaction->save();

                return $transaction;
            }

        }

        return null;
    }

    static function confirmPaypalPayment($request)
    {

        $validator = Validator::make($request->all(),[
            'paymentId' => 'required',
            'token' 	=> 'required',
            'PayerID' => 'required',
        ]);
        if($validator->fails())
            return null;

        $pid = $request->paymentId;
        $payment = Payment::get($pid, self::paypal());
        $payer = $request->PayerID;
        $mtid = $payment->transactions[0]->invoice_number;
        $amount = $payment->transactions[0]->amount->total;

        $transaction = self::whereMtid($mtid)->where('amount', $amount)->where("type", "PAYPAL")->where("state", "PENDING")->first();

        if($transaction == null)
            throw new RuntimeException("Can't find transaction object.");

        //Execute payment:
        $execution = new PaymentExecution();
        $execution->setPayerId($payer);
        $payment->execute($execution, self::paypal());
        $payment = Payment::get($pid, self::paypal());

        if($payment->state != "approved")
            return null;

        $transaction->state = "SUCCESS";
        $transaction->save();

        return $transaction;
    }

}