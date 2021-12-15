<?php
/**
 * Created by PhpStorm.
 * User: Faysal
 * Date: 06-04-2017
 * Time: 11:13 AM
 */
namespace App;

use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\ShippingTypeEnum;
use Cookie;
use Session;

class UtilityFunction
{
    public static function getToastrMessage($message)
    {
        $a = explode("::", $message);
        if (sizeof($a) < 2 && $a[0] != "") {
            $a[1] = $a[0];
            $a[0] = MessageTypeEnum::SUCCESS; //default message type
        }

        $toastr = "";
        $a[0] = $a[0] . MessageTypeEnum::SEPARATOR;


        if ($a[0] == MessageTypeEnum::SUCCESS)
            $toastr = '$.notify({icon: "font-icon font-icon-check-circle",message: "' . $a[1] . '"},{type: "success"});';
        elseif ($a[0] == MessageTypeEnum::ERROR)
            $toastr = '$.notify({icon: "font-icon font-icon-check-circle",message: "' . $a[1] . '"},{type: "danger"});';
        elseif ($a[0] == MessageTypeEnum::WARNING)
            $toastr = '$.notify({icon: "font-icon font-icon-check-circle",message: "' . $a[1] . '"},{type: "warning"});';
        elseif ($a[0] == MessageTypeEnum::INFO)
            $toastr = '$.notify({icon: "font-icon font-icon-check-circle",message: "' . $a[1] . '"},{type: "purple"});';

        return "<script>" . $toastr . "</script>";

    }

    public static function safe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public static function decode($value)
    {

        if (!$value) {
            return false;
        }
        $crypttext = UtilityFunction::safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, "SuPerEncKey2010a", $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    public static function safe_b64encode($string)
    {

        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    public static function encode($value)
    {

        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, "SuPerEncKey2010a", $text, MCRYPT_MODE_ECB, $iv);
        return trim(UtilityFunction::safe_b64encode($crypttext));
    }

    public static function getLocal()
    {
        $locale = Cookie::get('locale');
        if (is_null($locale) || empty($locale)) $locale = "en";
        if ($locale != "en" && $locale != "ar")
            $locale = decrypt($locale);

        app()->setLocale($locale);

        return $locale;
    }

    public static function switchLocal()
    {
        $locale = UtilityFunction::getLocal();
        if ($locale == 'en') $locale = 'ar';
        elseif ($locale == 'ar') $locale = 'en';
        Cookie::queue('locale', $locale);
        app()->setLocale(UtilityFunction::getLocal());
    }


    public static function setLocal()
    {
        $locale = UtilityFunction::getLocal();
        Cookie::queue('locale', $locale);
        app()->setLocale(UtilityFunction::getLocal());
    }

    public static function createReviewRateHtml($review_rate){
        $html = '';

            $html .= '<ul class="rating">';
            for($i=1;$i<=5;$i++){
                $html .= '<li><span aria-hidden="true" class="fa ';
                if(round($review_rate) >= $i){
                    if($review_rate < $i && round($review_rate) == $i) $html .= ' fa-star-half-o ';
                    else $html .= ' fa-star ';
                    $html .= ' customColor ';
                }else{
                    $html .= ' fa-star-o color-additional ';
                }
                $html .= '"></span></li>';
            }

            $html .= '</ul>';

        return $html;
    }

    public static function getShippingType($value = '')
    {
        $shipping_types = [
            ShippingTypeEnum::FREE_SHIPPING => ['name'=>trans('messages.shipping_tax.free_shipping'),'icon'=>'<i class="fa fa-truck" aria-hidden="true"></i>'],
            ShippingTypeEnum::FLAT_RATE => ['name'=>trans('messages.shipping_tax.flat_rate'),'icon'=>'<i class="fa fa-align-justify" aria-hidden="true"></i>'],
//            ShippingTypeEnum::RATE_BY_WEIGHT => ['name'=>trans('messages.shipping_tax.rate_by_weight'),'icon'=>'<i class="fa fa-tachometer" aria-hidden="true"></i>'],
            ShippingTypeEnum::RATE_BY_ORDER_PRICE => ['name'=>trans('messages.shipping_tax.rate_by_order_price'),'icon'=>'<i class="fa fa-money" aria-hidden="true"></i>'],
            ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY => ['name'=>trans('messages.shipping_tax.allow_store_pickup_only'),'icon'=>'<i class="fa fa-building" aria-hidden="true"></i>'],
        ];

        if(!empty($value)){
            return $shipping_types[$value]['name'];
        }
        return $shipping_types;
    }
}
