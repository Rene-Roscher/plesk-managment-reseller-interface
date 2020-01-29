<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 07.09.2018
 * Time: 20:42
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class oJobs extends Model
{
    protected $table = "oJobs";

    protected $fillable = [
        'queue', 'payload',
    ];

}