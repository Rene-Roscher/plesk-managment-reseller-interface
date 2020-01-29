<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 05.09.2018
 * Time: 17:36
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class ProductUpgradeEntry extends Model
{

    protected $table = "product_upgrade_entries";

    protected $fillable = [
        'upgrade_id', 'entry', 'data',
    ];

}