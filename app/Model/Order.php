<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function getBuyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function getCountryName()
    {
        return $this->belongsTo(Country::class, 'delivery_country');
    }

    public function getSubOrders()
    {
        return $this->hasMany(SubOrder::class, 'order_id');
    }

    public function getSubOrdersArray()
    {
        $subOrders = $this->getSubOrders;
        $subOrdersArray = [];
        foreach ($subOrders as $so)
            array_push($subOrdersArray, $so->id);
        return $subOrdersArray;
    }


    public static function getSubOrderStatus($order_id='')
    {
      $status_html = '';

      return $status_html;
    }

    static function getOrderItemsByOrderId($order_id){
        $order_item = '';

        if(!empty($order_id)){
            $order_item = SubOrder::join('order_items','sub_orders.id','=','order_items.sub_order_id')
                ->join('products','products.id','=','order_items.product_id')
                ->join('sellers','sellers.id','=','products.seller_id')
                ->select('sellers.business_address as store_address','sellers.store_name','products.name','products.quantity')
                ->where('order_id',$order_id)->get();
        }
        return $order_item;
    }

    public function getOrderCity(){
        return $this->belongsTo(City::class,'delivery_city');
    }

    public static function getOrderSummaryById($order_id) {


        $details = [];
        $admin_commission =0;
        $total=0;
        $order = Order::where('id',$order_id)->first();

        $sub_orders = $order->getSubOrders;

        foreach ($sub_orders as $so)
        {
            $s = SubOrder::getSubOrderSummaryById($so->id);
            $total = $total + $s["sub_total"];
            $admin_commission = $admin_commission + $s["admin_commission"];
        }

        $details['sub_total'] = $total;
        $details['admin_commission'] = $admin_commission;


        return $details;
    }
}
