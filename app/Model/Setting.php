<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function getValueByKey($key)
    {
        $setting = Setting::where('key',$key)->first();
        if(isset($setting)) return $setting->value;

        return "";
    }
}
