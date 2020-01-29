<?php

namespace App\Http\Controllers\Reseller\Accounting;

use App\PaymentHandler;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaypalController
{
	public function postRequestMoney($request)
	{
		$amount = $request->amount;
		$product_name = "Aufladung";
		$user = Auth::user();

		//Zahlung anfordern:
		$redirect = PaymentHandler::initPayment("PAYPAL", $amount, $product_name, $user);

		return redirect($redirect);
	}

	public function getSuccess(Request $request)
	{

		$trans = PaymentHandler::confirmPaypalPayment($request);
		if($trans)
		{

			$user = Auth::user();
			$user->money += $trans->amount;
            $user->save();

            $trans->state = "SUCCESS";
            $trans->save();
            DB::update('UPDATE transactions SET state = "SUCCESS" WHERE user_id = '.$user->id.' AND type = "PAYPAL" AND state = "PENDING" ORDER BY updated_at DESC LIMIT 1');

			return redirect("reseller/accounting")->withSuccess('Ihre Zahlung wurde erfolgreich abgeschlossen.');
		}
	}

	public function getError(Request $request)
	{
        DB::update('UPDATE transactions SET state = "ERROR" WHERE user_id = '.\Illuminate\Support\Facades\Auth::user()->id.' AND type = "PAYPAL" AND state = "PENDING" ORDER BY updated_at DESC LIMIT 1');
        //return var_dump($request->all());
        return redirect("reseller/accounting")->withErrors('Ihre Zahlung konnte nicht abgeschlossen werden, da ein Fehler auftrat.');
	}
}