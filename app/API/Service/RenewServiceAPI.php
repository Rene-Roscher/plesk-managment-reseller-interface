<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 13.09.2018
 * Time: 21:10
 */

namespace App\API\Service;


use App\Order;
use App\PaymentHandler;
use App\Product;
use App\Service;
use App\User;
use App\Webspace;
use Carbon\Carbon;

class RenewServiceAPI
{

    private $data;
    private $service;
    private $user;

    /**
     * OrderWebspaceAPI constructor.
     * @param $data
     */
    public function __construct($data, User $user)
    {
        $this->data = $data;
        $this->service = $data->service_id;
        $this->user = $user;
    }

    public function call()
    {
        $service = Service::all()->where('id', $this->service)->first();
        $product = Product::all()->where('id', $service->product_id)->first();

        $runtimePrice = null;
        $runtime = null;
        foreach (json_decode($product->data) as $entry) {
            if ($entry->runtime == $this->data->runtime . ' days') {
                $runtime = str_replace(' ', '', str_replace('days', '', $entry->runtime));
                $runtimePrice = $entry->price;
            }
        }

        $price = $service->getExtendsPrice();
        $price *= $runtimePrice;

        if(!$this->user->canOrderWithAmount($price))
            return back()->withErrors('Ihr guthaben genügt nicht aus.');

        if($service->expired_at)
        {
            /**
             * if product webspace / 1
             */
            if($service->product_id == 1)
            {
                $webspace = Webspace::all()->where('id', $service->service_id)->first();
                $webspace->webhost->suspend($webspace->plesk_customer_id, 0);
                $webspace->save();
            }

            $service->expired_at = null;
            $service->save();

        }

        $service->expire_at = Carbon::parse($service->expire_at)->addDays($runtime);
        $service->save();

        $transaction = new PaymentHandler();
        $transaction->user()->associate($this->user);
        $transaction->description = $product->name.' Verlängerung';
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
        $order->service_id = $service->id;
        $order->product_id = $service->product_id;
        $order->interval = $runtime;
        $order->amount = $price;
        $order->state = 'SUCCESS';
        $order->type = 'NEW';
        $order->save();
    }

}