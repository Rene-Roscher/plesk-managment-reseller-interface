<?php

namespace App\API\Payment;

use App\PaymentHandler;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Paysafecard\PaysafecardPaymentController;
use Paysafecard\PaysafeLogger;

class PaysafecardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct() {}

    public function postNotify(Request $request) {


        // create new Payment Controller
        $pscpayment = new PaysafecardPaymentController(env('PSC_KEY'), "PRODUCTION");
        $logger = new PaysafeLogger();

        // checking for actual action
        if (isset($_GET["payment"])) {
            $id = $_GET["payment"];
            // get payment status with retrieve Payment details
            $response = $pscpayment->retrievePayment($id);
            $logger->log($pscpayment->getRequest(), $pscpayment->getCurl(), $pscpayment->getResponse());
            if ($response == true) {
                if (isset($response["object"])) {
                    if ($response["status"] == "AUTHORIZED") {
                        // capture payment
                        $response = $pscpayment->capturePayment($id);
                        $logger->log($pscpayment->getRequest(), $pscpayment->getCurl(), $pscpayment->getResponse());
                        if ($response == true) {

                            if (isset($response["object"])) {

                                if ($response["status"] == "SUCCESS") {

                                    /** @var Transaction $transaction */
                                    $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                    $transaction->state = 'SUCCESS';
                                    $transaction->save();

                                    /** @var User $user */
                                    $user = User::all()->where('id', $transaction->user->id)->first();
                                    $user->money += $transaction->amount;
                                    $user->save();

                                } else {

                                    $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                    $transaction->state = 'ERROR';
                                    $transaction->save();

                                }
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getSuccess(Request $request)
    {

        $pscpayment = new PaysafecardPaymentController($_ENV['PSC_KEY'], "PRODUCTION");
        $logger = new PaysafeLogger();

        if (!empty(($request->payment))) {
            $id = $request->payment;
            // get the current payment informations
            $response = $pscpayment->retrievePayment($id);

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
                    return redirect($transaction->url_ok);

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

                            return redirect($transaction->url_ok);

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
                                        return redirect($transaction->url_ok);
                                    } else {
                                        $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                        $transaction->state = 'ERROR';
                                        $transaction->save();
                                        return redirect($transaction->url_nok);
                                    }
                                } else {
                                    $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                    $transaction->state = 'ERROR';
                                    $transaction->save();
                                    return redirect($transaction->url_nok);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getError()
    {
        $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
        $transaction->state = 'ERROR';
        $transaction->save();
        return redirect($transaction->url_nok);
    }
}