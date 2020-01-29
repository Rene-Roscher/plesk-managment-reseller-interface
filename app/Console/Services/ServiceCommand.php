<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 08.09.2018
 * Time: 20:09
 */

namespace App\Console\Services;
use App\Product;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ServiceCommand extends Command
{

    /**
     * ServiceCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $services = Service::all()->where('state', '!=', 'EXPIRED');

        foreach ($services as $service) {

            /**
             * product_id 1 : Webspace
             */
            if ($service->product_id == 1) {

                $webspace = Webspace::get('id', $service->service_id);
                $webhost = WebspaceHost::get('id', $webspace->webhost->id);

                if ($this->isExpire($service->expire_at)) {

                    /**
                     * Suspend account if expired.
                     */
                    if ($service->expired_at == null) {
                        $webhost->suspend($webspace->plesk_customer_id, 1);
                        $service->expired_at = Carbon::now();
                        $service->state = 'EXPIRE';
                        $service->save();
                    }
                }

                /**
                 * Check if 10 days over expired
                 */

                if ($service->expired_at != null && $this->expiredCheck($service->expired_at, 3)) {
                    $webhost->api()->webspace()->delete('id', $webspace->plesk_id);
                    $webhost->api()->customer()->delete('id', $webspace->plesk_customer_id);
                    $webspace->webhost->deletePlan($webspace->plan);
                    $webspace->service->state = 'EXPIRED';
                    $webspace->service->save();
                    $webspace->delete();
                }

            }

        }
    }

    /**
     * Check if x days before expired
     * @param $expire_at
     * @param $days
     * @return bool
     */
    public function expireCheck($expire_at, $days)
    {
        return Carbon::parse($expire_at)->format('d.m.Y H:i') == Carbon::now()->addDays($days)->format('d.m.Y H:i');
    }

    /**
     * Check if expired
     * @param $expire_at
     * @return bool
     */
    public function isExpire($expire_at)
    {
        return Carbon::parse($expire_at) < Carbon::now();
    }

    /**
     * Check if x days over expired
     * @param $expired_at
     * @param $days
     * @return bool
     */
    public function expiredCheck($expired_at, $days)
    {
        return Carbon::parse($expired_at) <= Carbon::now()->subDays($days);
    }

}