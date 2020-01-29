<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class APIOptions extends Model
{
    protected $table = "api_options";

    protected $fillable = [
        'name', 'state',
    ];

}
