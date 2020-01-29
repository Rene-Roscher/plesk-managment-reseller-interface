<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 17.09.2018
 * Time: 14:40
 */

namespace App\ApiV2\applications\payment\check;


use App\ApiV2\Api;
use App\ApiV2\applications\payment\gateways\PayPal;
use App\PaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

class PayPalCheck
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function getSuccess(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'paymentId' => 'required',
            'token' 	=> 'required',
            'PayerID' => 'required',
        ]);
        if($validator->fails())
            return Api::sendError(array($validator->fails()));

        $pid = $request->paymentId;
        $payment = Payment::get($pid, PayPal::paypal());
        $payer = $request->PayerID;
        $mtid = $payment->transactions[0]->invoice_number;
        $amount = $payment->transactions[0]->amount->total;

        $transaction = PaymentHandler::all()->where('mtid', $mtid)->where('amount', $amount)->where('type', 'PAYPAL')->where('state', 'PENDING')->first();

        if($transaction == null)
            return Api::sendError(array('Can not find transaction object'));

        $execution = new PaymentExecution();
        $execution->setPayerId($payer);
        $payment->execute($execution, PayPal::paypal());
        $payment = Payment::get($pid, PayPal::paypal());

        if($payment->state != "approved") {
            $transaction->state = "ERROR";
            return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_nok));
        }

        $transaction->state = "SUCCESS";
        $transaction->save();

        return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_ok));
    }

    public function getError(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'token' 	=> 'required',
        ]);
        if($validator->fails())
            return Api::sendError(array($validator->fails()));

        $transaction = PaymentHandler::all()->where('token', $request->token)->where('type', 'PAYPAL')->where('state', 'PENDING')->first();

        if($transaction == null)
            throw new \RuntimeException('Can not find transaction object.');

        $transaction->state = "ERROR";
        $transaction->save();

        return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_nok));
    }

}