<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 17.09.2018
 * Time: 20:09
 */

namespace App\ApiV2\applications\payment\check;


use App\ApiV2\Api;
use App\ApiV2\applications\payment\gateways\PaySafeCard;
use App\PaymentHandler;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Paysafecard\PaysafecardPaymentController;
use Paysafecard\PaysafeLogger;

class PaySafeCardCheck
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function getSuccess(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'payment' => 'required',
        ]);

        if($validator->fails())
            return Api::sendError(array($validator->fails()));

        $transaction = PaymentHandler::all()->where('mtid', $request->payment)->where('type', 'PAYSAFECARD')->where('state', 'PENDING')->first();

        if($transaction == null)
            return Api::sendError(array('Can not find transaction object'));



        $pscpayment = new PaysafecardPaymentController(env('PSC_KEY'), "PRODUCTION");
        $logger = new PaysafeLogger();

        if (!empty(($request->payment))) {
            $id = $request->payment;
            $response = $pscpayment->retrievePayment($id);

            $logger->log($pscpayment->getRequest(), $pscpayment->getCurl(), $pscpayment->getResponse());

            if ($response == false) {

                $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                $transaction->state = 'ERROR';
                $transaction->save();
                return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_nok));

            } else if (isset($response["object"])) {

                if ($response["status"] == "SUCCESS") {

                    $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                    $transaction->state = 'SUCCESS';
                    $transaction->save();
                    return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_ok));

                } else if ($response["status"] == "AUTHORIZED") {
                    // capture payment
                    $response = $pscpayment->capturePayment($id);

                    $logger->log($pscpayment->getRequest(), $pscpayment->getCurl(), $pscpayment->getResponse());

                    if ($response == false) {

                        $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                        $transaction->state = 'ERROR';
                        $transaction->save();
                        return redirect($transaction->url_nok);

                    } else if (isset($response["object"])) {

                        if ($response["status"] == "SUCCESS") {
                            $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                            $transaction->state = 'SUCCESS';
                            $transaction->save();

                            /** @var User $user */
                            $user = $transaction->user;
                            $user->money += ($transaction->amount * 0.85) - 0.10;
                            $user->save();

                            return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_ok));

                        } else {

                            $error = $pscpayment->getError();

                            if ($error["number"] == 2017) {
                                $response = $pscpayment->retrievePayment($id);
                                $logger->log($pscpayment->getRequest(), $pscpayment->getCurl(), $pscpayment->getResponse());

                                if (isset($response["status"])) {
                                    if ($response["status"] == "SUCCESS") {
                                        $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                        $transaction->state = 'SUCCESS';
                                        $transaction->save();
                                        return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_ok));
                                    } else {
                                        $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                        $transaction->state = 'ERROR';
                                        $transaction->save();
                                        return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_nok));
                                    }
                                } else {
                                    $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                    $transaction->state = 'ERROR';
                                    $transaction->save();
                                    return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_nok));
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    public function getError(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'payment' => 'required',
        ]);
        if($validator->fails())
            return Api::sendError(array($validator->fails()));

        $transaction = PaymentHandler::all()->where('mtid', $request->payment)->where('type', 'PAYSAFECARD')->where('state', 'PENDING')->first();

        if($transaction == null)
            return Api::sendError(array('Can not find transaction object'));

        $transaction->state = "ERROR";
        $transaction->save();

        return redirect(str_replace('{mtid}', $transaction->mtid, $transaction->url_nok));
    }

}