<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    public function getOrder()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getSeller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function getPayment(){
        return $this->belongsTo(SellerPayment::class,'seller_payment_id');
    }

    public function getSubOrderItems(){
        return $this->hasMany(OrderItem::class,'sub_order_id');
    }

    public function getProducts(){
        return $this->hasMany(Product::class,'product_id');
    }

    public static function getSubOrderTotalPrice($suborder_id) {
        if(!empty($suborder_id)){
            $order_items = OrderItem::where('sub_order_id',$suborder_id)->get();

            $sub_total = 0;
            foreach($order_items as $order_item){

                if(isset($order_item->deal_price))
                {
                    $sub_total = $sub_total + ($order_item->deal_price * $order_item->quantity);
                }
                else
                {
                    $sub_total = $sub_total + ($order_item->product_price * $order_item->quantity);
                }

            }

            return $sub_total;
        }

        return 0;
    }

    public static function getSubOrderSummaryById($suborder_id) {

        $details = [];
        $sub_order = SubOrder::where('id',$suborder_id)->first();

        $order_items = OrderItem::where('sub_order_id',$suborder_id)->get();

        $sub_total = 0;
        foreach($order_items as $order_item){

            if(isset($order_item->deal_price))
            {
                $sub_total = $sub_total + ($order_item->deal_price * $order_item->quantity);
            }
            else
            {
                $sub_total = $sub_total + ($order_item->product_price * $order_item->quantity);
            }
        }

        $admin_commission = $sub_total * $sub_order->admin_commission /100;

        $details['sub_total'] = $sub_total;
        $details['admin_commission'] = $admin_commission;

        return $details;
    }
}
