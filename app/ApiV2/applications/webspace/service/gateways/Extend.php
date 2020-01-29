<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 19.09.2018
 * Time: 17:45
 */

namespace App\ApiV2\applications\webspace\service\gateways;


use App\ApiV2\Api;
use App\Order;
use App\PaymentHandler;
use App\Service;
use App\User;
use App\Webspace;
use Carbon\Carbon;

class Extend
{

    private $user;
    private $service;
    private $days;

    public function __construct(User $user, Service $service, int $days)
    {
        $this->user = $user;
        $this->service = $service;
        $this->days = $days;
    }

    public function extend()
    {
        if($this->service) {

            try {
                return $this->service->getExtendsPrice();
                $price = $this->service->getExtendsPrice() * $this->days;



                if(!$this->user->canOrderWithAmount($price))
                    return back()->withErrors('Ihr guthaben genügt nicht aus.');

                if($this->service->expired_at)
                {

                    $webspace = Webspace::all()->where('id', $this->service->service_id)->first();
                    $webspace->webhost->suspend($webspace->plesk_customer_id, 0);
                    $webspace->save();

                    $this->service->expired_at = null;
                    $this->service->save();

                }

                $this->service->expire_at = Carbon::parse($this->service->expire_at)->addDays($this->days);
                $this->service->save();

                $transaction = new PaymentHandler();
                $transaction->user()->associate($this->user);
                $transaction->description = 'Webspace Verlängerung';
                $transaction->amount = $price;
                $transaction->mtid = 0;
                $transaction->state = 'SUCCESS';
                $transaction->type = 'INTERN';
                $transaction->typ = 'OWN';
                $transaction->save();

                $this->user->money -= $price;
                $this->user->save();

                $order = new Order();
                $order->user_id = $this->user->id;
                $order->service_id = $this->service->id;
                $order->product_id = $this->service->product_id;
                $order->interval = $this->days;
                $order->amount = $price;
                $order->state = 'SUCCESS';
                $order->type = 'NEW';
                $order->save();
            } catch (\Exception $exception){
                return Api::sendError($exception->getMessage());
            }

        }

        return Api::sendError(['error']);
    }

}