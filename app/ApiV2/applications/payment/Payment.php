<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 14.09.2018
 * Time: 14:48
 */

namespace App\ApiV2\applications\payment;


use App\ApiV2\Api;
use App\ApiV2\applications\payment\gateways\PayPal;
use App\ApiV2\applications\payment\gateways\PaySafeCard;
use App\PaymentHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Payment extends Api
{

    /**
     * @param string $api
     * @param array $data
     * @return mixed
     */
    public static function call(string $api, array $data)
    {
        if(isset($data['mtid']))
            return self::single($data);

        $transaction = new PaymentHandler();
        $transaction->user_id = $data['user']->id;
        $transaction->amount = $data['amount'];
        $transaction->description = $data['description'];
        $transaction->state = "PENDING";
        $transaction->type = $data['type'];
        $transaction->mtid = $transaction::generateMtid();
        $transaction->typ = 'API';
        $transaction->url_ok = $data['url_ok'];
        $transaction->url_nok = $data['url_nok'];
        $transaction->url_notify = $data['url_notify'];
        $transaction->save();

        switch ($data['type'])
        {
            case 'PAYPAL':
                $payment = new PayPal($transaction);
                $response = $payment->create();
                return self::sendSuccess(['payment_id' => $transaction->mtid, 'url' => $response]);
                break;
            case 'PAYSAFECARD':
                $payment = new PaySafeCard($transaction);
                $response = $payment->create();
                return self::sendSuccess(['payment_id' => $transaction->mtid, 'url' => $response]);
                break;
            default:
                return self::sendError(array('type was not found.'));
        }

    }

    private static function single(array $data)
    {
        $transaction = PaymentHandler::all()->where('user_id', $data['user']->id)->where('mtid', $data['mtid'])->first();
        if($transaction)
            return self::sendSuccess(['state' => $transaction->state]);
        return self::sendError(['no transaction found.']);
    }

}