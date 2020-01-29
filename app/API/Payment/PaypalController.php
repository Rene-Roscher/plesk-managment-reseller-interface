<?php

namespace App\API\Payment;

use App\PaymentHandler;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaypalController
{
	public function postRequestMoney($request)
	{
		$amount = $request->amount;
		$product_name = "Aufladung";
		$user = Auth::user();

		$redirect = PaymentHandler::initPayment("PAYPAL", $amount, $product_name, $user);

		return redirect($redirect);
	}

	public function getSuccess(Request $request)
	{

		$trans = PaymentHandler::confirmPaypalPayment($request);
		if($trans)
		{
			$user = User::all()->where('id', $trans->user_id)->first();
			$user->money += ($trans->amount * 0.98) - 0.35;
            $user->save();
            $trans->state = "SUCCESS";
            $trans->save();
            DB::update('UPDATE transactions SET state = "SUCCESS" WHERE user_id = '.$user->id.' AND type = "PAYPAL" AND state = "PENDING" ORDER BY updated_at DESC LIMIT 1');
			return redirect($trans->url_ok);
		}
	}

	public function getError(Request $request)
	{
	    $trans = PaymentHandler::all()->where('token', $request->token)->where('type', 'PAYPAL')->where('state', 'PENDING')->first();
        $trans->state = 'ERROR';
        $trans->save();
	    //DB::update('UPDATE transactions SET state = "ERROR" WHERE token = '.$request->token.' AND type = "PAYPAL" AND state = "PENDING" ORDER BY updated_at DESC LIMIT 1');
		return redirect($trans->url_nok);
	}
}