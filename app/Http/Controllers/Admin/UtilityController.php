<?php

namespace App\Http\Controllers\Admin;

use App\Model\City;
use App\Model\Department;
use App\Model\Designation;
use App\Model\Holiday;
use App\Model\News;
use App\Model\Provider;
use App\Model\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Crypt;
use Cookie;
use Illuminate\Support\Facades\Auth;

class UtilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // storeExists
    public function storeExists(Request $request)
    {
        $store_name = '';
        $seller_id = '';
        if (!empty($_GET['store_name'])) $store_name = $_GET['store_name'];
        if (!empty($_GET['seller_id'])) $seller_id = Crypt::decrypt($_GET['seller_id']);

        $store_exist = Seller::where('store_name', 'LIKE', '%' . trim($store_name) . '%');
        if (!empty($seller_id)) $store_exist = $store_exist->where('id', '!=', $seller_id);
        $store_exist = $store_exist->exists();

        return response()->json(['exists' => $store_exist]);
    }

    // cityByCountry
    public function cityByCountry(){
        $country = $_GET['country'];

        $cities = City::where('country_id',$country)->get();

        $cities_html = '';
        $cities_html .= '<option value="">Select City</option>';
        foreach($cities as $city){
            $cities_html .= '<option value="'.$city->id.'">';
            if(\App\UtilityFunction::getLocal()== "en") $cities_html .= $city->name;
            else $cities_html .= $city->ar_name;
            $cities_html .= '</option>';
        }
        return response()->json(['cities_html'=>$cities_html]);
    }
}
