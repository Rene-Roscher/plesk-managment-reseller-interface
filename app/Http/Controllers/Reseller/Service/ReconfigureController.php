<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 12.09.2018
 * Time: 22:04
 */

namespace App\Http\Controllers\Reseller\Service;


use App\Http\Controllers\Controller;
use App\Order;
use App\PaymentHandler;
use App\Product;
use App\ProductUpgradeEntry;
use App\ProductUpgrades;
use App\Service;
use App\Webspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReconfigureController extends Controller
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
        $product = Product::all()->where('id', $service->product_id)->first();
        $upgrades = ProductUpgrades::all()->where('product_id', $product->id);

        $data = [];

        if($product->name == 'Webspace')
        {
            $data = Webspace::all()->where('id', $service->service_id)->first()->configuration;
        }

        return view('reseller.service.reconfigure', compact('service', 'product', 'upgrades', 'data'));
    }

    public function reconfigure(Service $service, Request $request)
    {
        if($service->expired_at)
            return back()->withErrors('Ihr Service ist gerade am Auslaufen.');

        $valid = [];
        $requests = [];

        foreach ($request->all() as $item => $value) {
            if ($item != '_token') {
                $requests += [$item => $value];
                $valid += [$item => 'required|integer|exists:product_upgrade_entries,id'];
            }
        }

        $validator = Validator::make($requests, $valid);
        if ($validator->fails())
            return back()->withErrors($validator->errors())->withInput($requests);

        $price = 0;
        $upgrades = [];

        foreach ($requests as $item => $value){
            $upgrade = ProductUpgradeEntry::all()->where('id', $value)->first();
            $price += $upgrade->price;
            $upgrades += [$item => $upgrade];
        }

        if(!Auth::user()->canOrderWithAmount($price))
            return back()->withErrors('Ihr guthaben genügt nicht aus.');

        /**
         * Webspace
         */
        if($service->product_id == 1)
        {
            $webspace = Webspace::all()->where('id',$service->service_id)->first();
            /**
             * Calcul. new price..
             */
            $product = Product::all()->where('name', 'Webspace')->first();

            $data = [];

            foreach ($product->upgrades() as $upgrade) {
                $upgrade = json_decode($upgrade);
                foreach (ProductUpgradeEntry::all()->where('upgrade_id', $upgrade->id) as $entry) {
                    if (object_get(json_decode($webspace->configuration), str_replace('ftpusers', 'ftp', str_replace('box', 'mail', $upgrade->upgrade))) == $entry->data){
                        $price -= $entry->price;
                        $upgradeKey = $upgrade->upgrade;
                        $data += [str_replace('ftpusers', 'ftp', str_replace('box', 'mail', $upgrade->upgrade)) => $upgrades[$upgradeKey]->data];
                    }
                }
            }

            $data += ['transaction' => json_decode($webspace->configuration)->transaction];

            $webspace->configuration = json_encode($data);
            $webspace->save();

            $webspace->webhost()->first()->reconfigurePlan($webspace->plan, $data['disk'], $data['site'], $data['subdom'], $data['mail'], $data['db'], $data['ftp']);

        }

        $transaction = new PaymentHandler();
        $transaction->user()->associate(Auth::user());
        $transaction->description = $product->name.' Up/Downgrade';
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
        $order->interval = 0;
        $order->amount = $price;
        $order->state = 'SUCCESS';
        if($price > 0) {
            $order->type = 'UPGRADE';
        } else {
            $order->type = 'DOWNGRADE';
        }
        $order->save();

        if($price > 0){
            Auth::user()->money += $price;
        } else {
            Auth::user()->money -= $price;
        }
        Auth::user()->save();

        return back()->withSuccess('Ihr Konfiguration wird nun bearbeitet, dies kann einige Minuten dauern.');
    }

}