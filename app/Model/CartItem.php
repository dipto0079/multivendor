<?php

namespace App\Model;

use App\Http\Controllers\Enum\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use App\Http\Controllers\Enum\ShippingTypeEnum;

class CartItem extends Model
{
    //
    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Get Shipping Amount
    public static function getShippingAmount($shipping, $total_prodcut_price)
    {
        $amount = '';

        if ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {

        } elseif ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {

        } else {
            $amount = ($total_prodcut_price * $shipping->tax) / 100 + $shipping->rate;
        }

        return $amount;
    }

    // Get Tax Amount
    public function getTaxAmount()
    {
        $tax = '';
        $seller_shipping = $this->getProduct->getSeller->getShipping;
        $product = $this->getProduct;
        $quantity = $this->quantity;
        $buyer = Auth::user()->getBuyer;

        if (!empty($seller_shipping)) {
            foreach ($seller_shipping as $shipping) {
                $cities = explode(',', $shipping->city_ids);

                if ($shipping->country_id == $buyer->country) {
                    $total_prodcut_price = $product->price * $quantity;
                    // $tax = CartItem::getShippingAmount($shipping,$total_prodcut_price);


                    if ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {

                    } elseif ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
                        $shipping_order_ranges = $shipping->getShippingRateByOrderPrice;


                    } else {
                        $tax = ($total_prodcut_price * $shipping->tax) / 100 + $shipping->rate;
                    }


                    if (in_array($buyer->city, $cities, true) || !isset($cities)) {
                        $tax = $tax;
                    }
                }
            }
        }

        return $tax;
    }

    // Get Tax Amount
    public function getProceedStatus($country, $city)
    {
        $status = false;
        $seller_shipping = $this->getProduct->getSeller->getShipping;

        if (!empty($seller_shipping)) {
            foreach ($seller_shipping as $shipping) {
                $cities = explode(',', $shipping->city_ids);

                if ($shipping->country_id == $country || $shipping->country_id == -1) {
                    $status = true;

                    if (!empty($cities[0])) {
                        if (in_array($city, $cities)) {
                            $status = true;
                            break;
                        } else {
                            $status = false;
                        }
                    }
                } else {
                    $status = false;
                }
            }
        }

        return $status;
    }

    public static function getTaxAmountBySellerID($seller_ids, $products, $country, $city)
    {
        $tax = 0;
        $total_product_price = [];
        $sub_order_info = [];
        $tax_amount = [];
        $rate = [];

        foreach ($seller_ids as $seller) {
            $product_price = 0;
            foreach ($products as $product) {
                if ($seller == $product['seller_id']) {
                    $product_price = $product_price + $product['price'];
                }
            }
            $total_product_price[] = ['seller_id' => $seller, 'total_price' => $product_price];
        }

        $shipping_list = Shipping::whereIn('seller_id', $seller_ids)->where('country_id', $country)->get();

        foreach ($shipping_list as $shipping) {
            $cities = explode(',', $shipping->city_ids);

            foreach ($total_product_price as $price) {
                $tax = 0;

                if (empty($cities[0]) && $price['seller_id'] == $shipping->seller_id) {
                    $total_price = $price['total_price'];

                    if (!empty($shipping->tax)) $tax = ($total_price * $shipping->tax) / 100;

                    if ($shipping->shipping_type == ShippingTypeEnum::FREE_SHIPPING) {
                        $tax_amount[] = $tax;
                        $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>0,'seller_id'=>$price['seller_id']];
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::FLAT_RATE) {
                        $tax_amount[] = $tax;
                        $rate[] = $shipping->rate;
                        $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>$shipping->rate,'seller_id'=>$price['seller_id']];
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {
                        $tax_amount[] = $tax;
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
                        $shipping_order_ranges = $shipping->getShippingRateByOrderPrice;

                        foreach ($shipping_order_ranges as $shipping_order_range) {
                            if ($shipping_order_range->range_start <= $price['total_price'] && $shipping_order_range->range_end >= $price['total_price']) {
                                $rate[] = $shipping_order_range->rate;
                                $tax_amount[] = $tax;
                                $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>$shipping_order_range->rate,'seller_id'=>$price['seller_id']];
                            } elseif ($shipping_order_range->range_start <= $price['total_price'] && $shipping_order_range->range_end == '') {
                                $rate[] = $shipping_order_range->rate;
                                $tax_amount[] = $tax;
                                $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>$shipping_order_range->rate,'seller_id'=>$price['seller_id']];
                            }
                        }
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY) {
                        $tax_amount[] = $tax;
                        $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>0,'seller_id'=>$price['seller_id']];
                        break;
                    }

                }
                elseif (!empty($cities[0]) && in_array($city, $cities) && $price['seller_id'] == $shipping->seller_id) {
                    $total_price = $price['total_price'];

                    if (!empty($shipping->tax)) $tax = ($total_price * $shipping->tax) / 100;

                    if ($shipping->shipping_type == ShippingTypeEnum::FREE_SHIPPING) {
                        $tax_amount[] = $tax;
                        $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>0,'seller_id'=>$price['seller_id']];
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::FLAT_RATE) {
                        $rate[] = $shipping->rate;
                        $tax_amount[] = $tax;
                        $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>$shipping->rate,'seller_id'=>$price['seller_id']];
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_WEIGHT) {
                        $tax_amount[] = $tax;
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::RATE_BY_ORDER_PRICE) {
                        $shipping_order_ranges = $shipping->getShippingRateByOrderPrice;

                        foreach ($shipping_order_ranges as $shipping_order_range) {
                            if ($shipping_order_range->range_start <= $price['total_price'] && $shipping_order_range->range_end >= $price['total_price']) {
                                $rate[] = $shipping_order_range->rate;
                                $tax_amount[] = $tax;
                                $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>$shipping_order_range->rate,'seller_id'=>$price['seller_id']];
                            } elseif ($shipping_order_range->range_start <= $price['total_price'] && $shipping_order_range->range_end == '') {
                                $rate[] = $shipping_order_range->rate;
                                $tax_amount[] = $tax;
                                $sub_order_info[] = ['vat'=>$tax,'vat_rate'=>$shipping->tax,'shipping_type'=>$shipping->shipping_type,'shipping_rate'=>$shipping_order_range->rate,'seller_id'=>$price['seller_id']];
                            }
                        }
                        break;
                    } elseif ($shipping->shipping_type == ShippingTypeEnum::ALLOW_STORE_PICKUP_ONLY) {
                        $tax_amount[] = $tax;
                        $sub_order_info[] = ['vat'=>$tax,'shipping_rate'=>0,'seller_id'=>$price['seller_id']];
                        break;
                    }
                }
            }

        }

        $vat_shipping_rate = ['tax' => array_sum($tax_amount), 'shipping_rate' => array_sum($rate), 'sub_order_info'=>$sub_order_info];

        return $vat_shipping_rate;
    }


// get product info
    static function getProductInfo($product_id)
    {
        $product = Product::where('id', $product_id)->first();
        return $product;
    }

    // get product info
    static function getSellerInfo($product_id)
    {
        $seller = Product::join('sellers', 'sellers.id', '=', 'products.seller_id')
            ->select('sellers.*')
            ->where('products.id', $product_id)->first();
        return $seller;
    }

    static function getSellCount($product_id)
    {
        $sell_count = OrderItem::join('sub_orders', 'sub_orders.id', '=', 'order_items.sub_order_id')
            ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->select(DB::raw('count(order_items.id) as sell_count'))
            ->where('order_items.product_id', $product_id)
            ->where('sub_orders.status', OrderStatusEnum::FINALIZED)
            ->first();

        return $sell_count;
    }

}
