<?php

namespace App\Model;

use App\Http\Controllers\Enum\StaticPageEnum;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    //
    public static function staticPageEnumType($value = ''){
        $array = [
            StaticPageEnum::ABOUT_US => 'About Us',
            StaticPageEnum::PRESS => 'Press',
            StaticPageEnum::SUPPORT => 'Support',
            StaticPageEnum::PRIVACY_POLICY => 'Privacy Policy',
            StaticPageEnum::TERM_CONDITION => 'Term And Condition',
        ];

        if(!empty($value)){
            return $array[$value];
        }
        return $array;
    }
}
