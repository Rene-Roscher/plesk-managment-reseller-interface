<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 10.09.2018
 * Time: 22:59
 */

namespace App\Http\Controllers\Reseller\Service;


use App\Http\Controllers\Controller;
use App\Order;
use App\PaymentHandler;
use App\Product;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExtendController extends Controller
{

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Service $service)
    {
        if($service->deleted_at)
            return back()->withErrors('Ihr Service kann nicht mehr Verlängert werden.');
        $product = Product::all()->where('id', $service->product_id)->first();
        return view('reseller.service.extend', compact('service', 'product'));
    }

    public function extend(Service $service, Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'runtime' => 'required|integer'
            ]);

        $validator->setAttributeNames(
            [
                'runtime' => 'Laufzeit'
            ]);

        if ($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $user = User::all()->where('id', Auth::user()->id)->first();

        $product = Product::all()->where('id', $service->product_id)->first();

        $runtimePrice = null;
        $runtime = null;
        foreach (json_decode($product->data) as $entry) {
            if ($entry->runtime == $request->runtime . ' days') {
                $runtime = str_replace(' ', '', str_replace('days', '', $entry->runtime));
                $runtimePrice = $entry->price;
            }
        }

        $price = $service->getExtendsPrice();
        $price *= $runtimePrice;

        if(!$user->canOrderWithAmount($price))
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
        $transaction->user()->associate(Auth::user());
        $transaction->description = $product->name.' Verlängerung';
        $transaction->amount = $price;
        $transaction->mtid = 0;
        $transaction->state = 'SUCCESS';
        $transaction->type = 'INTERN';
        $transaction->typ = 'OWN';
        $transaction->save();

        Auth::user()->money -= $price;
        Auth::user()->save();

        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->service_id = $service->id;
        $order->product_id = $service->product_id;
        $order->interval = $runtime;
        $order->amount = $price;
        $order->state = 'SUCCESS';
        $order->type = 'NEW';
        $order->save();

        return back()->withSuccess('Ihr Produkt wurde erfolgreich Verlängert.');
    }

}