<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 03.09.2018
 * Time: 18:11
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    protected $table = "services";

    protected $fillable = [
        'user_id', 'product_id', 'expire_at', 'expired_at', 'service_id', 'state',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->morphTo(Service::class);
    }

    /**
     * @param string $type | years,months,weeks,days,hours,seconds
     * @default $type | seconds
     * @return int
     */
    public function getLeftTime($type = 'seconds') {
        switch($type)
        {
            case 'years':
                $time = Carbon::parse($this->expire_at)->diffInYears(Carbon::now());
                break;
            case 'months':
                $time = Carbon::parse($this->expire_at)->diffInMonths(Carbon::now());
                break;
            case 'weeks':
                $time = Carbon::parse($this->expire_at)->diffInWeeks(Carbon::now());
                break;
            case 'days':
                $time = Carbon::parse($this->expire_at)->diffInDays(Carbon::now());
                break;
            case 'hours':
                $time = Carbon::parse($this->expire_at)->diffInHours(Carbon::now());
                break;
            case 'minutes':
                $time = Carbon::parse($this->expire_at)->diffInMinutes(Carbon::now());
                break;
            case 'seconds':
                $time = Carbon::parse($this->expire_at)->diffInRealSeconds(Carbon::now());
                break;
        }
        return $time;
    }

    public function getExtendsPrice() {
        /**
         * Product 1 - Webspace
         * Product 2...
         */
        if ($this->product_id == 1) {
            $webspace = Webspace::get('id', $this->service_id);
            $data = json_decode($webspace->configuration);
//            return $data->disk;
//            $disk = ProductUpgrades::find(1)->entries()->where('data', $data->disk)->first()->price;
            $site = ProductUpgrades::find(2)->entries()->where('data', $data->site)->first()->price;
            $subdom = ProductUpgrades::find(3)->entries()->where('data', $data->subdom)->first()->price;
            $mail = ProductUpgrades::find(4)->entries()->where('data', $data->mail)->first()->price;
            $db = ProductUpgrades::find(5)->entries()->where('data', $data->db)->first()->price;
            $ftp = ProductUpgrades::find(6)->entries()->where('data', $data->ftp)->first()->price;
            return $site + $subdom + $mail + $db + $ftp;
        }
    }

}