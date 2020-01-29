<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 08.09.2018
 * Time: 13:39
 */

namespace App\API\Webspace\Order;

use App\oJobs;
use App\Order;
use App\PaymentHandler;
use App\Product;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class OrderWebspaceAPI
{

    private $data;
    private $user;

    /**
     * OrderWebspaceAPI constructor.
     * @param $data
     */
    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public function call()
    {
        $runtime = $this->data['runtime'] / 30;

        $user = User::all()->where('id', $this->user->id)->first();

        $product = Product::all()->find(1)->first();

        $disk = $product->upgrades()->find(1)->getSinglePrice() * ($this->data['disk'] / 1024);
        $site = $product->upgrades()->find(2)->getSinglePrice() * $this->data['site'];
        $subdom = $product->upgrades()->find(3)->getSinglePrice() * $this->data['subdom'];
        $mail = $product->upgrades()->find(4)->getSinglePrice() * $this->data['mail'];
        $db = $product->upgrades()->find(5)->getSinglePrice() * $this->data['db'];
        $ftp = $product->upgrades()->find(6)->getSinglePrice() * $this->data['ftp'];
        $domain = $this->data['domain'];

        $price = $disk + $site + $subdom + $mail + $db + $ftp;
        $price *= $runtime;

        $error = [];

        if(!$user->canOrderWithAmount($price))
            $error += 2012;

        if(!$error)
        {
            $transaction = new PaymentHandler();
            $transaction->user()->associate($user);
            $transaction->description = 'Webspace bestellung';
            $transaction->amount = $price;
            $transaction->mtid = 0;
            $transaction->state = 'PENDING';
            $transaction->type = 'INTERN';
            $transaction->typ = 'OWN';
            $transaction->save();

            $service = new Service();
            $service->user_id = $user->id;
            $service->expire_at = Carbon::now()->addDays($this->data['runtime']);
            $service->expired_at = null;
            $service->product_id = $product->id;
            $service->save();

            $order = new Order();
            $order->user_id = $user->id;
            $order->service_id = $service->id;
            $order->product_id = $product->id;
            $order->interval = $this->data['runtime'];
            $order->amount = $price;
            $order->state = 'PENDING';
            $order->type = 'NEW';
            $order->save();

            $webspace = new Webspace();
            try {
                $webhost = WebspaceHost::getFreeHost();
            } catch (\ErrorException $e) {
            }
            $webspace->user()->associate($user);
            $webspace->webhost()->associate($webhost);
            $webspace->webhost_id = $webhost->id;
            $webspace->save();

            $data = ['disk' => $this->data['disk'], 'site' => $this->data['site'], 'subdom' => $this->data['subdom'], 'mail' => $this->data['mail'], 'db' => $this->data['db'], 'ftp' => $this->data['ftp'], 'transaction' => $transaction->id, 'domain' => $domain];

            $webspace->configuration = json_encode($data);
            $webspace->save();

            $jo = new oJobs();
            $jo->queue = 'webspace';
            $jo->payload = json_encode(['webspace' => $webspace->id, 'webhost' => $webhost->id, 'order' => $order->id, 'transaction' => $transaction->id, 'user' => $user->id, 'service' => $service->id]);
            $jo->save();
        }

        return array('success' => $error =! null ? false : true, 'response' => ['id' => $webspace->id], 'errors' => [$error]);
    }

    public function callSingle($id)
    {
        $webspace = Webspace::all()->where('user_id', $this->user->id)->where('id', $id)->first();

        try {
            $pw = decrypt($webspace->plesk_password);
            return array('success' => $webspace ? false : true, 'response' => ['id' => $webspace->id, 'username' => $webspace->plesk_username, 'password' => $pw, 'url' => $webspace->plesk_url, 'installed' => $webspace->installed], 'errors' => []);
        } catch (\Exception $exception){
            try {
                return array('success' => $webspace ? false : true, 'response' => ['id' => $webspace->id, 'username' => $webspace->plesk_username, 'password' => null, 'url' => $webspace->plesk_url, 'installed' => $webspace->installed], 'errors' => []);
            } catch (\Exception $exception){

            }
        }

    }

}