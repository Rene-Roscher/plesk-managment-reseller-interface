<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 15.09.2018
 * Time: 15:37
 */

namespace App\ApiV2\applications\payment\gateways;

use App\PaymentHandler;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Paysafecard\PaysafecardPaymentController;
use Paysafecard\SOPGClassicMerchantClient;

class PaySafeCard
{

    private $transaction;
    private $amount;
    private $user;

    /**
     * PayPal constructor.
     * @param $transaction
     * @param $amount
     */
    public function __construct(PaymentHandler $transaction)
    {
        $this->transaction = $transaction;
        $this->amount = $transaction->amount;
        $this->user = $transaction->user();
    }

    /**
     * @return string
     */
    public function create()
    {
        $psc = new PaysafecardPaymentController(env('PSC_KEY'), "PRODUCTION");
        $currency = 'EUR';
        $customer_id = $this->transaction->user->id;
        $customer_ip = '0.0.0.0';
        $okurl = url('api/payment/paysafecard/success?payment={payment_id}');
        $errurl = url('api/payment/paysafecard/error?payment={payment_id}');
        $notifyurl = url('api/payment/paysafecard/notify?payment={payment_id}');

        $response = $psc->createPayment($this->amount, $currency, $customer_id, $customer_ip, $okurl, $errurl, $notifyurl);

        if ($response == false) {
            $this->transaction->state = 'ERROR';
            $this->transaction->save();
        } else if (isset($response['object'])) {

            if (isset($response['redirect'])) {
                $this->transaction->mtid = $response['id'];
                $this->transaction->save();
                return $response['redirect']['auth_url'];
            }

        }
        return $response;
    }

    public static function paysafecard() : SOPGClassicMerchantClient
    {
        $psc_api = new SOPGClassicMerchantClient(true, "de", false, (/*"test" */ 'live'));
        $psc_api ->merchant(env('PAYSAFECARD_USER'), env('PAYSAFECARD_KEY'));

        return $psc_api;
    }

}