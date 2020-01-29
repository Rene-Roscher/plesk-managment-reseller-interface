<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

class PaymentHandler extends Model
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

    static function initPayment(string $type, $amount, string $productName, $user) : string
    {
        $amount = number_format(round($amount, 2), 2, '.', '');

        $transaction = new self;
        $transaction->amount = $amount;
        $transaction->mtid = self::generateMtid();
        $transaction->state = "PENDING";
        $transaction->type = $type;
        $transaction->user()->associate($user);
        $transaction->save();

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
                $pp_urls->setReturnUrl(route("reseller.payment.paypal.success"))->setCancelUrl(route("reseller.payment.paypal.error"));

                $pp_payment = new Payment();
                $pp_payment->setIntent("sale")->setPayer($pp_payer)->setRedirectUrls($pp_urls)->setTransactions([$pp_transaction]);

                $pp_payment->create(self::paypal());
                $url = $pp_payment->getApprovalLink();
                break;
            case "PAYSAFECARD":

                /*$paysafecard = self::paysafecard();
                $paysafecard->setCustomer($amount, "EUR", $transaction->mtid, $transaction->user->id);

                $okUrl = route('reseller.payment.paysafecard.success', ['id' => $transaction->id, 'user_id' => $transaction->user->id]);
                $nokUrl = route('reseller.payment.paysafecard.cancel', ['id' => $transaction->id, 'user_id' => $transaction->user->id]);
                $pnUrl = route('reseller.payment.paysafecard.notify', ['id' => $transaction->id, 'user_id' => $transaction->user->id]);
                $paysafecard->setUrl($okUrl, $nokUrl, $pnUrl);

                $url = $paysafecard->createDisposition();

                if($url == false) {
                    $transaction->state = "ERROR";
                    $transaction->save();
                    throw new RuntimeException($paysafecard->getLog('error'));
                }
*/

                $psc = new PaysafecardPaymentController('psc_3lD7qqZsb3tMTSXm-Bm-tWv-b32RogA', "PRODUCTION");
                $currency = "EUR";
                $customer_id = Auth::id();
                $customer_ip = $_SERVER['REMOTE_ADDR'];
                $okurl = url('reseller/accounting/payment/paysafecard/success?payment={payment_id}');
                $errurl = url('reseller/accounting/payment/paysafecard/error?payment={payment_id}');
                $notifyurl = url('reseller/accounting/payment/paysafecard/notify?payment={payment_id}');

                $response = $psc->createPayment($amount, $currency, $customer_id, $customer_ip, $okurl, $errurl, $notifyurl);

                if ($response == false) {
                    $err = $psc->getError();

                    $transaction->state = 'ERROR';
                    $transaction->save();

                    if (($err['number'] == 4003)) {
                        return back()->withErrors('<strong>Fehler: Der Aufladebetrag ist zu hoch. Bitte wähle einen kleineren Betrag.</strong>');
                    } else {
                        return back()->withErrors('Die Transaktion konnte nicht initiert werden. Bitte wende dich an unseren Support!'.$err['number']);
                    }
                } else if (isset($response["object"])) {

                    if (isset($response["redirect"])) {
                        $transaction->mtid = $response['id'];
                        $transaction->save();
                        return redirect($response["redirect"]["auth_url"]);
                    }

                }

                break;

            default:
                throw new InvalidArgumentException("Can't find payment type: " . $type);
                break;
        }

        return $url;
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