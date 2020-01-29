<?php

namespace App\Http\Controllers\Reseller\Accounting;

use App\API;
use App\Helper\FormatHelper;
use App\PaymentHandler;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Paysafecard\PaysafecardPaymentController;

class AccountingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = PaymentHandler::all()->where('user_id', Auth::user()->id);
        return view('reseller.accounting.index', compact('transactions'));
    }

    public function single($year, $month)
    {

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $year.'-'.$month.'-01 00:00:00');
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $year.'-'.$month.'-31 00:00:00');

        $transactions = PaymentHandler::all()->
        where('user_id', Auth::user()->id)->
        where('created_at', '>=', $start)->
        where('created_at', '<=', $end);

        return view('reseller.accounting.single.index', compact('transactions'));
    }

    public function export($year, $month)
    {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $year.'-'.$month.'-01 00:00:00');
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $year.'-'.$month.'-31 00:00:00');

        $transactions = PaymentHandler::all()->
        where('created_at', '>=', $start)->
        where('created_at', '<=', $end);

        $pdf = Pdf::loadView('pdf.invoice');
        return $pdf->stream('pdf.invoice');
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'type' => 'required|in:PAYPAL,PAYSAFECARD,SOFORT',
                'amount' => 'required|numeric'
            ]);

        $validator->setAttributeNames(
            [
                'type' => 'Zahlungsmethode',
                'amount' => 'Betrag'
            ]);

        if ($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        if($request->type == 'SOFORT')
            return back()->withErrors('Die ausgewählte Zahlungsmethode ist zurzeit nicht verfügbar.');

        $amount = FormatHelper::money($request->amount);

        $user = Auth::user();

        if ($request->type == "PAYPAL") {
            $response = PaymentHandler::initPayment($request->type, $amount, 'Guthabenaufladung von '.$amount.' €', $user);
            return redirect($response);
        } else if ($request->type == "PAYSAFECARD") {
            $transaction = new PaymentHandler();
            $transaction->user_id = Auth::id();
            $transaction->type = $request->type;
            $transaction->amount = $amount;
            $transaction->description = 'Guthabenaufladung von '.$amount.' €';
            $transaction->mtid = str_random(32);
            $transaction->state = 'PENDING';
            $transaction->save();

            $psc = new PaysafecardPaymentController(env('PSC_KEY'), "PRODUCTION");
            $currency = "EUR";
            $customer_id = Auth::id();
            $customer_ip = $request->ip();
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
        }

        $transaction->state = 'ERROR';
        $transaction->save();

        return back()->withErrors('Es ist ein Fehler aufgetreten.');
    }

}
