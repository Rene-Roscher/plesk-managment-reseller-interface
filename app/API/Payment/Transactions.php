<?php

namespace App\API\Payment;

use App\API;
use App\PaymentHandler;
use App\User;

class Transactions
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function call()
    {
        $api = API::all()->where('token', $this->token)->first();
        $payment = PaymentHandler::all()->where('user_id', $api->user->id);
        return response(['success' => true, 'errors' => [], 'response' => $payment]);
    }

    public function callSingle($mtid)
    {
        $api = API::where('token', $this->token)->first();
        $payment = PaymentHandler::where('user_id', $api->user->id)->where('mtid', $mtid)->first();
        if($payment)
            return response(['success' => true, 'errors' => [], 'response' => $payment], 200);

        return response(['success' => false, 'errors' => ['There is no payment to this mtid.'], 'response' => []], 500);
    }

}
