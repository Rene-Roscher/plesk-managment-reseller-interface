<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 18.09.2018
 * Time: 14:05
 */

namespace App\ApiV2\applications\webspace\order;


use App\ApiV2\Api;
use App\oJobs;
use App\Order;
use App\PaymentHandler;
use App\Product;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Carbon\Carbon;

class OrderWebspace extends Api
{


    /**
     * @param string $api
     * @param array $data
     * @return mixed
     */
    public static function call(string $api, array $data)
    {
        if(isset($data['webspace_id']))
            return self::single($data);

        $runtime = $data['runtime'] / 30;

        $user = User::all()->where('id', $data['user']->id)->first();

        $product = Product::all()->find(1)->first();

        $disk = $product->upgrades()->find(1)->getSinglePrice() * ($data['disk'] / 1024);
        $site = $product->upgrades()->find(2)->getSinglePrice() * $data['site'];
        $subdom = $product->upgrades()->find(3)->getSinglePrice() * $data['subdom'];
        $mail = $product->upgrades()->find(4)->getSinglePrice() * $data['mail'];
        $db = $product->upgrades()->find(5)->getSinglePrice() * $data['db'];
        $ftp = $product->upgrades()->find(6)->getSinglePrice() * $data['ftp'];
        $domain = $data['domain'];

        $price = $disk + $site + $subdom + $mail + $db + $ftp;
        $price *= $runtime;

        $error = [];

        if(!$user->canOrderWithAmount($price))
            return self::sendError(array('Too little credit to their account'));

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
            $service->expire_at = Carbon::now()->addDays($data['runtime']);
            $service->expired_at = null;
            $service->product_id = $product->id;
            $service->save();

            $order = new Order();
            $order->user_id = $user->id;
            $order->service_id = $service->id;
            $order->product_id = $product->id;
            $order->interval = $data['runtime'];
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

            $data = [
                'disk' => $data['disk'],
                'site' => $data['site'],
                'subdom' => $data['subdom'],
                'mail' => $data['mail'],
                'db' => $data['db'],
                'ftp' => $data['ftp'],
                'transaction' => $transaction->id,
                'domain' => $domain
            ];

            $webspace->configuration = json_encode($data);
            $webspace->save();

            $jo = new oJobs();
            $jo->queue = 'webspace';
            $jo->payload = json_encode(['webspace' => $webspace->id, 'webhost' => $webhost->id, 'order' => $order->id, 'transaction' => $transaction->id, 'user' => $user->id, 'service' => $service->id]);
            $jo->save();
        }

        return self::sendSuccess(['id' => $webspace->id]);
    }

    private static function single(array $data)
    {
//        return 235;
        $webspace = Webspace::all()->where('user_id', $data['user']->id)->where('id', $data['webspace_id'])->first();


        if($webspace) {
            if($webspace->plesk_password)
                return self::sendSuccess(['installed' => boolval($webspace->installed), 'username' => $webspace->plesk_username, 'password' => decrypt($webspace->plesk_password), 'url' => $webspace->plesk_url]);
            return self::sendSuccess(['installed' => boolval($webspace->installed), 'username' => $webspace->plesk_username, 'password' => null, 'url' => $webspace->plesk_url]);
        }

        return self::sendError(array('no webspace found.'));
    }

}