<?php

namespace App\API\Payment;

use App\API\PaymentHandlerAPI;
use App\User;

class PaymentAPI
{

    public $type;
    public $amount;
    public $user;
    public $description;
    public $okurl;
    public $nokurl;
    public $notifyurl;

    public function __construct($type,$amount, $user, $description, $okurl, $nokurl, $notifyurl)
    {
        $this->type = $type;
        $this->amount = $amount;
        $this->user = $user;
        $this->description = $description;
        $this->okurl = $okurl;
        $this->nokurl = $nokurl;
        $this->notifyurl = $notifyurl;
    }

    public function create()
    {
        $response = PaymentHandlerAPI::initPayment($this->type, $this->amount, 'Beschreibung', User::all()->find($this->user), $this->okurl, $this->nokurl, $this->notifyurl, $this->description);
        return response($response, 200);
    }

}
