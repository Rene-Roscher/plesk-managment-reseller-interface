<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function transactions()
    {
        return $this->belongsTo(PaymentHandler::class, 'user_id');
    }

    public function is($role){
        return $this->role == $role;
    }

    public function canOrder()
    {
        if($this->money <= (-$this->credit))
            return false;
        return true;
    }

    public function canOrderWithAmount($amount)
    {
        if(!$this->canOrder())
            return false;

        if( ($this->money - $amount) < (-$this->credit))
            return false;

        return true;
    }

    public function doesntOwn( $model, $foreignKey = null, $strict = false )
    {
        return !$this->owns($model, $foreignKey, $strict);
    }

    public function owns($model, $foreignKey = null, $strict = false)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        if ($strict)
            return $this->getKey() === $model->{ $foreignKey };

        return $this->getKey() == $model->{ $foreignKey };
    }

}
