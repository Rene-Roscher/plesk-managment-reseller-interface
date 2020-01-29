<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class APILogs extends Model
{
    protected $table = "api_logs";

    protected $fillable = [
        'token_id', 'client', 'ip', 'uri', 'state', 'respocode',
    ];

    public function api()
    {
        return API::all()->where('id', $this->id)->first();
        //return $this->belongsTo(API::class);
    }

}
