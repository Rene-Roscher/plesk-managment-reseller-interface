<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 03.09.2018
 * Time: 17:54
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webspace extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'webhost_id', 'plesk_url', 'plesk_id', 'plesk_username', 'plesk_password', 'plesk_customer_id', 'configuration'
    ];

    protected $hidden = [
        'webhost',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
//        return Service::all()->where('service_id', $this->id);
        return $this->belongsTo(Service::class, 'id', 'service_id');
    }

    public function webhost()
    {
        return $this->belongsTo(WebspaceHost::class, 'webhost_id');
    }

    public function isExpired()
    {
        return !$this->service->expired_at;
    }

    public function isInstalled()
    {
        return !$this->installed;
    }

    public static function get($key, $value) : Webspace
    {
        return Webspace::all()->where($key, $value)->first();
    }

}