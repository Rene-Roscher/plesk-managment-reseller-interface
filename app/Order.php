<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 03.09.2018
 * Time: 17:56
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = "orders";

    protected $fillable = [
        'user_id', 'service_id', 'product_id', 'amount', 'interval', 'type', 'state',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function service() {
        return $this->belongsTo(Service::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

}