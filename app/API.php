<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class API extends Model
{

    protected $table = "api";

    protected $fillable = [
        'user_id', 'token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function options2()
    {
        return APIOptionEntry::all()->where('token_id', $this->id);
    }

    public function whitelist()
    {
        return APIWhitelist::all()->where('token_id', $this->id);
        //return $this->hasMany(APIWhitelist::class, 'token_id');
    }

    public function logs()
    {
        return $this->hasMany(APILogs::class, 'token_id');
    }

    public function hasOption($option)
    {
        $exist = false;
        foreach ($this->options2() as $value){
            if($value->option_id === $option){
                $exist = true;
            }
        }
        return $exist;
    }

    public function isWhitelisted($ip) : bool
    {
        foreach ($this->whitelist() as $item) {
            if($item->address == request()->ip()) {
                return true;
            }
        }
    }

}
