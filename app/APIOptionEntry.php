<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class APIOptionEntry extends Model
{
    protected $table = "api_options_entries";

    protected $fillable = [
        'token_id', 'option_id',
    ];

    public function api()
    {
        return $this->belongsTo(API::class);
    }

    public function option()
    {
        return $this->belongsTo(APIOptions::class, 'option_id');
    }

    public function isAccessible()
    {
        if($this->state != 'INACCESSIBLE')
            return true;
        return false;
    }

}
