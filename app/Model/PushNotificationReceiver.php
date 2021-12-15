<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PushNotificationReceiver extends Model
{
    //
    public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }
}
