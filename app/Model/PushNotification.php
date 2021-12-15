<?php

namespace App\Model;

use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\User;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    public function getUser(){
        return $this->belongsTo(User::class,'notification_by');
    }

    public function getPushNotificationReceiver(){
        return $this->hasMany(PushNotificationReceiver::class,'push_notification_id');
    }

    public static function getNotificationCount($id,$receiver_type){
        $count = PushNotification::join('push_notification_receivers','push_notification_receivers.push_notification_id','=','push_notifications.id')
            ->where('push_notification_receivers.receiver_type',$receiver_type)
            ->where('push_notification_receivers.receiver_id',$id)
            ->where('push_notification_receivers.is_viewed',0)
            ->count();
        return $count;
    }
}
