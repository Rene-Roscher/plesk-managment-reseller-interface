<?php

namespace App\Http\Controllers\Reseller\Accounting;

use App\PaymentHandler;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaction;
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
        $pscpayment = new PaysafecardPaymentController($_ENV['PSC_KEY'], "PRODUCTION");
        $logger     = new PaysafeLogger();

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
                                    $transaction = Transaction::where('mtid', $_GET['payment'])->firstOrFail();
                                    $transaction->state = 'SUCCESS';
                                    $transaction->save();

                                    /** @var User $user */
                                    $user = $transaction->user;
                                    $user->money += $transaction->amount;
                                    $user->save();

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
                return redirect('reseller/accounting')->withErrors('Ihre Zahlung konnte nicht ordnungsgemäß durchgeführt werden.');

            } else if (isset($response["object"])) {

                if ($response["status"] == "SUCCESS") {

                    return redirect('reseller/accounting')->withSuccess('Ihre Zahlung wurde erfolgreich abgeschlossen.');

                } else if ($response["status"] == "AUTHORIZED") {
                    // capture payment
                    $response = $pscpayment->capturePayment($id);

                    $logger->log($pscpayment->getRequest(), $pscpayment->getCurl(), $pscpayment->getResponse());

                    if ($response == false) {

                        $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                        $transaction->state = 'ERROR';
                        $transaction->save();
                        return redirect('reseller/accounting')->withErrors('Ihre Zahlung konnte nicht ordnungsgemäß durchgeführt werden.');

                    } else if (isset($response["object"])) {

                        if ($response["status"] == "SUCCESS") {
                            $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                            $transaction->state = 'SUCCESS';
                            $transaction->save();

                            /** @var User $user */
                            $user = $transaction->user;
                            $user->money += $transaction->amount;
                            $user->save();

                            return redirect('reseller/accounting')->withSuccess('Ihre Zahlung wurde erfolgreich abgeschlossen.');

                        } else {

                            $error = $pscpayment->getError();

                            if ($error["number"] == 2017) {
                                $response = $pscpayment->retrievePayment($id);
                                $logger->log($pscpayment->getRequest(), $pscpayment->getCurl(), $pscpayment->getResponse());

                                if (isset($response["status"])) {
                                    if ($response["status"] == "SUCCESS") {
                                        return redirect('reseller/accounting')->withSuccess('Ihre Zahlung wurde erfolgreich abgeschlossen.');
                                    } else {
                                        $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                        $transaction->state = 'ERROR';
                                        $transaction->save();
                                        return redirect('reseller/accounting')->withErrors('Ihre Zahlung konnte nicht ordnungsgemäß durchgeführt werden.');
                                    }
                                } else {
                                    $transaction = PaymentHandler::where('mtid', $_GET['payment'])->firstOrFail();
                                    $transaction->state = 'ERROR';
                                    $transaction->save();
                                    return redirect('reseller/accounting')->withErrors('Ihre Zahlung konnte nicht ordnungsgemäß durchgeführt werden.');
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
        return redirect('reseller/accounting')->withErrors('Transaktion durch Benutzer abgebrochen.');
    }
}