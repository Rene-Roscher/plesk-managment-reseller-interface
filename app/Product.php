<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 03.09.2018
 * Time: 18:11
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = "products";

    protected $fillable = [
        'name', 'price', 'data', 'state',
    ];

    public function upgrades() {
        return ProductUpgrades::all()->where('product_id', $this->id);
    }

    public function getConfigurationAttribute($value) {
        return json_decode($value);
    }

    public function setConfigurationAttribute($value) {
        $this->attributes['configuration'] = json_encode($value);
    }

}