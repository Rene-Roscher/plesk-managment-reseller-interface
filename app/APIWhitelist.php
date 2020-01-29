<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class APIWhitelist extends Model
{
    protected $table = "api_whitelist";

    protected $fillable = [
        'token_id', 'address',
    ];

    public function api()
    {
        return $this->belongsTo(API::class, 'token_id');
    }

    public function isWhitelistet($address)
    {
        if ($this->ip == $address)
            return true;
        return false;
    }

}
