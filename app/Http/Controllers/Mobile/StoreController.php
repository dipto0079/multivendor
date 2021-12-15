<?php

namespace App\Http\Controllers\Mobile;


use App\Http\Controllers\Controller;
use App\Model\City;
use App\Model\Location;
use App\Model\Store;
use App\Model\StoreType;
use App\User;
use Illuminate\Http\Request;
use DB;
use Auth;



class StoreController extends Controller
{
    public function addStoreInfo(){
        $store_types = StoreType::OrderBy('name','asc')->get();
        $locations = Location::orderBy('name','asc')->get();
        $cities = City::orderBy('name','asc')->get();
        foreach($cities as $city){
            if($city->getLocationByCity->count()>0)
            $city->location_name = $city->getLocationByCity;
        }
//        dd($cities[29]->location_name);
        return response()->json(array('error'=>false,'store_types'=>$store_types,'locations'=>$locations,'cities'=>$cities));
    }
    public function addStore(Request $request){

        if(!empty($request->merchant_id) && !empty($request->brach_name)){

            $stores = new Store();
            $stores->name = $request->brach_name;
            $stores->longitude = $request->longitude;
            $stores->latitude = $request->latitude;
            $stores->address = $request->address;
            $stores->description = $request->description;
            $stores->store_type_id = $request->store_type;
//            $location_details = Location::where('id', $request->location_id)->first();


//            $stores->city_name = $location_details->getCityName->name;
//            $stores->location_id = $location_details->id;

            $stores->merchant_id = $request->merchant_id;
            $stores->save();
            return response()->json(array('error'=>false,'merchant_id'=>$request->merchant_id,'branch_id'=>$stores->id,'branch_name'=>$request->brach_name,'longitude'=>$request->longitude,'latitude'=>$request->latitude,'address'=>$request->address));
        }
        return response()->json(array('error'=>true,'error_msg'=>'Invalid Input'));

    }
    public function DeleteStoreByMerchantId(Request $request){
        try {
            if(!empty($request->merchant_id) && !empty($request->brach_id)){
                Store::where('merchant_id',$request->merchant_id)->where('id',$request->brach_id)->delete();
                return response()->json(array('error'=>false,'merchant_id'=>$request->merchant_id,'branch_name'=>$request->brach_id));
            }
            return response()->json(['error'=>true,'error_msg'=>'Invalid Input']);
        } catch (\Exception $e) {
            return response()->json(['error'=>true,'error_msg'=>'Something went wrong.']);
        }
    }
    public function updateStore(Request $request){
        if(!empty($request->merchant_id) && !empty($request->branch_id)){
            $stores = Store::findOrfail($request->branch_id);
            if(isset($stores)) {
                $stores->name = $request->brach_name;
                $stores->longitude = $request->longitude;
                $stores->latitude = $request->latitude;
                $stores->address = $request->address;

                if (!empty($request->description)) $stores->description = $request->description;

                $stores->merchant_id = $request->merchant_id;
                $stores->save();
            }else{
                return response()->json(array('error'=>true,'error_msg'=>'Store Id Not Found'));
            }
            return response()->json(array('error'=>false,'merchant_id'=>$request->merchant_id,'branch_id'=>$stores->id,'branch_name'=>$request->brach_name,'longitude'=>$request->longitude,'latitude'=>$request->latitude,'address'=>$request->address));
        }
        return response()->json(array('error'=>true,'error_msg'=>'Invalid Input'));
    }

    public function getAllStoresByMerchantId(Request $request){
        $merchant_id = $request->merchant_id;

        $stores_by_merchantId = DB::table('stores')
            ->where('stores.merchant_id',$merchant_id)
//            ->where('status',1)
            ->orderBy('created_at','desc')
            ->get();

        if(isset($stores_by_merchantId[0]))
            return response()->json(array('error'=>false,'stores'=>$stores_by_merchantId));

        return response()->json(array('error'=>true,'error_msg'=>'No Strore Create Yet'));
    }

    public function getStoreDetails($store_id){

    }

    public function getStoresByAddress(Request $request){

        if(!empty($request->stores_area)){
            $stores_by_user_address = DB::table('stores')
                ->where('address','LIKE','%'.$request->stores_area.'%')
                ->get();
            if(isset($stores_by_user_address[0]))
                return response()->json(array('error'=>false,'stores'=>$stores_by_user_address));

            return response()->json(array('error'=>true,'error_msg'=>'No Store Found'));
        }
        return response()->json(array('error'=>true,'error_msg'=>'Invalid Input'));
    }
}
