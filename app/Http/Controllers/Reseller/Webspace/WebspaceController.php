<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 04.09.2018
 * Time: 22:04
 */

namespace App\Http\Controllers\Reseller\Webspace;


use App\Console\Kernel;
use App\Http\Controllers\Controller;
use App\oJobs;
use App\Jobs\Webspace\OrderWebspace;
use App\Order;
use App\PaymentHandler;
use App\Product;
use App\ProductUpgrades;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WebspaceController extends Controller
{

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $product = Product::all()->where('name', 'Webspace')->first();
        $upgrades = $product->upgrades();

        $webspaces = Webspace::all()->where('user_id', Auth::user()->id);
        return view('reseller.webspaces.index', compact('webspaces', 'product', 'upgrades'));
    }

    public function show(Webspace $webspace)
    {
//        if($webspace->service->expired_at)
//            return back()->withErrors('Das Produkt ist bereits ausgelaufen.');
//        return $webspace->webhost()->first()->reconfigurePlan(plan, disk, domains, subdom, mail(box), db, ftp);
        return view('reseller.webspaces.single.index', compact('webspace'));
    }

    public function automaticLogin(Webspace $webspace)
    {
        if($webspace->service->expired_at) {
            return back()->withErrors('Deine Webspace ist ausgelaufen.');
        }
        return redirect('https://' . $webspace->webhost->ip_address . ':8443/enterprise/rsession_init.php?PLESKSESSID=' . $webspace->webhost->autoLogin($webspace));
    }

    public function create()
    {
        $product = Product::all()->where('name', 'Webspace')->first();
        $upgrades = $product->upgrades();
        return view('reseller.order.webspace.index', compact('product', 'upgrades'));
    }

    public function step1(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'upgrade1' => 'required|integer|exists:product_upgrade_entries,id',
                'upgrade2' => 'required|integer|exists:product_upgrade_entries,id',
                'upgrade3' => 'required|integer|exists:product_upgrade_entries,id',
                'upgrade4' => 'required|integer|exists:product_upgrade_entries,id',
                'upgrade5' => 'required|integer|exists:product_upgrade_entries,id',
                'upgrade6' => 'required|integer|exists:product_upgrade_entries,id',
                'runtime' => 'required|in:30,90,180,365',
            ]);
        if ($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $user = Auth::user();

        $product = Product::all()->find(1)->first();

        $disk = $product->upgrades()->find(1)->entries()->find($request->upgrade1);
        $site = $product->upgrades()->find(2)->entries()->find($request->upgrade2);
        $subdom = $product->upgrades()->find(3)->entries()->find($request->upgrade3);
        $mail = $product->upgrades()->find(4)->entries()->find($request->upgrade4);
        $db = $product->upgrades()->find(5)->entries()->find($request->upgrade5);
        $ftp = $product->upgrades()->find(6)->entries()->find($request->upgrade6);

        $runtimePrice = null;
        $runtime = null;
        foreach (json_decode($product->data) as $entry) {
            if ($entry->runtime == $request->runtime . ' days') {
                $runtime = str_replace(' ', '', str_replace('days', '', $entry->runtime));
                $runtimePrice = $entry->price;
            }
        }

        $price = $disk->price + $site->price + $subdom->price + $mail->price + $db->price + $ftp->price;
        $price *= $runtimePrice;

        if (!$user->canOrderWithAmount($price))
            return response()->json(view('reseller.order.webspace.error')->with('message', '<span style="color: red;"><center>Ihr guthaben genügt nicht aus.</center></span>')->render());


        return response()->json(view('reseller.order.webspace.step1')
            ->with('runtime', $runtime)
            ->with('runtimePrice', $runtimePrice)
            ->with('price', $price)
            ->with('product', $product)
            ->with('disk', $disk)
            ->with('site', $site)
            ->with('subdom', $subdom)
            ->with('mail', $mail)
            ->with('db', $db)
            ->with('ftp', $ftp)
            ->with('money_before', $user->money)
            ->with('money_after', $user->money - $price)
            ->render());
    }

    public function step2(Request $request)
    {
        /*
         * Included from Step 1
         */

        $validator = Validator::make($request->all(),
            [
                'upgrade1' => 'required|integer',
                'upgrade2' => 'required|integer',
                'upgrade3' => 'required|integer',
                'upgrade4' => 'required|integer',
                'upgrade5' => 'required|integer',
                'upgrade6' => 'required|integer',
                'runtime' => 'required|in:30,90,180,365',
            ]);
        if($validator->fails())
            return response()->json(view('reseller.order.webspace.error')->with('message', '<span style="color: red;"><center>'.var_dump($validator->errors())->withInput($request->all()).'</center></span>')->render());

        $user = Auth::user();

        $product = Product::all()->find(1)->first();

        $disk = $product->upgrades()->find(1)->entries()->find($request->upgrade1);
        $site = $product->upgrades()->find(2)->entries()->find($request->upgrade2);
        $subdom = $product->upgrades()->find(3)->entries()->find($request->upgrade3);
        $mail = $product->upgrades()->find(4)->entries()->find($request->upgrade4);
        $db = $product->upgrades()->find(5)->entries()->find($request->upgrade5);
        $ftp = $product->upgrades()->find(6)->entries()->find($request->upgrade6);

        $runtimePrice = null;
        $runtime = null;
        foreach (json_decode($product->data) as $entry) {
            if ($entry->runtime == $request->runtime . ' days') {
                $runtime = str_replace(' ', '', str_replace('days', '', $entry->runtime));
                $runtimePrice = $entry->price;
            }
        }

        $price = $disk->price + $site->price + $subdom->price + $mail->price + $db->price + $ftp->price;
        $price *= $runtimePrice;

        if(!$user->canOrderWithAmount($price))
            return response()->json(view('reseller.order.webspace.error')->with('message', '<span style="color: red;"><center>Ihr guthaben genügt nicht aus.</center></span>')->render());

        $interval = CarbonInterval::instance(\DateInterval::createFromDateString($runtime.' days'));
        $interval->setLocale(App::getLocale());

        $transaction = new PaymentHandler();
        $transaction->user()->associate($user);
        $transaction->description = 'Webspace bestellung';
        $transaction->amount = $price;
        $transaction->mtid = 0;
        $transaction->state = 'PENDING';
        $transaction->type = 'INTERN';
        $transaction->typ = 'OWN';
        $transaction->save();

        $webspace = new Webspace();
        try {
            $webhost = WebspaceHost::getFreeHost();
        } catch (\ErrorException $e) {
        }
        $webspace->user()->associate(Auth::user());
        $webspace->webhost()->associate($webhost);
        $webspace->webhost_id = $webhost->id;
        $webspace->save();

        $data = ['disk' => $disk['data'], 'site' => $site['data'], 'subdom' => $subdom['data'], 'mail' => $mail['data'], 'db' => $db['data'], 'ftp' => $ftp['data'], 'transaction' => $transaction->id];

        $webspace->configuration = json_encode($data);
        $webspace->save();

        $service = new Service();
        $service->user_id = $user->id;
        $service->expire_at = Carbon::now()->addDays($interval->dayz);
        $service->expired_at = null;
        $service->product_id = $product->id;
        $service->service_id = $webspace->id;
        $service->save();

        $order = new Order();
        $order->user_id = $user->id;
        $order->service_id = $service->id;
        $order->product_id = $product->id;
        $order->interval = $runtime;
        $order->amount = $price;
        $order->state = 'PENDING';
        $order->type = 'NEW';
        $order->save();

        $jo = new oJobs();
        $jo->queue = 'webspace';
        $jo->payload = json_encode(['webspace' => $webspace->id, 'webhost' => $webhost->id, 'order' => $order->id, 'transaction' => $transaction->id, 'user' => $user->id, 'service' => $service->id]);
        $jo->save();

        return response()->json(view('reseller.order.webspace.step2')->render());
    }

}