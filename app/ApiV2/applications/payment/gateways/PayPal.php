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

class PayPal
{

    private $transaction;
    private $amount;

    /**
     * PayPal constructor.
     * @param $transaction
     * @param $amount
     */
    public function __construct(PaymentHandler $transaction)
    {
        $this->transaction = $transaction;
        $this->amount = $transaction->amount;
    }

    /**
     * @return string
     */
    public function create()
    {
        $pp_details = new Details();
        $pp_details->setShipping(0)->setTax(0)->setSubtotal($this->amount);

        $item1 = new Item();
        $item1->setName('Guthabenaufladung')->setCurrency('EUR')->setQuantity(1)->setSku($this->transaction->mtid)->setTax(0)->setPrice($this->amount);

        $itemList = new ItemList();
        $itemList->setItems(array($item1));

        $pp_amount = new Amount();
        $pp_amount->setCurrency('EUR')->setTotal($this->amount)->setDetails($pp_details);

        $pp_payer = new Payer();
        $pp_payer->setPaymentMethod('PAYPAL');

        $pp_transaction = new Transaction();
        $pp_transaction->setAmount($pp_amount)->setInvoiceNumber($this->transaction->mtid)->setItemList($itemList)->setDescription('Guthabenaufladung');

        $pp_urls = new RedirectUrls();
        $pp_urls->setReturnUrl(route('api.payment.paypal.success'))->setCancelUrl(route('api.payment.paypal.error'));

        $pp_payment = new Payment();
        $pp_payment->setIntent('sale')->setPayer($pp_payer)->setRedirectUrls($pp_urls)->setTransactions([$pp_transaction]);

        $pp_payment->create(self::paypal());
        $response = $pp_payment->getApprovalLink();

        $this->transaction->token = $pp_payment->getToken();
        $this->transaction->save();

        return $response;
    }

    public static function paypal() : ApiContext
    {
        $pp_api = new ApiContext(new OAuthTokenCredential(env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET')));
        $pp_api->setConfig([
            'mode' => 'live' /* 'sandbox' */,
        ]);
        return $pp_api;
    }

}