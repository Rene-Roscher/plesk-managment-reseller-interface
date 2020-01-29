<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 05.09.2018
 * Time: 17:34
 */

namespace App;


use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class ProductUpgrades extends Model
{

    protected $table = "product_upgrades";

    protected $fillable = [
        'product_id', 'title', 'upgrade',
    ];

    public function entries() {
        return ProductUpgradeEntry::all()->where('upgrade_id', $this->id);
    }

    public function getSinglePrice()
    {
        $data = intval($this->entries()->first()->entry);
        if($data > 0){
            return $this->entries()->first()->price / $data;
        }
        return 0;
    }

}