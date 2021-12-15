<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ProductCategory;
use App\Model\Product;
use App\Http\Controllers\Enum\ProductTypeEnum;
use Carbon\Carbon;

class MobileController extends Controller
{
    // Category List
    public function categoryList()
    {
        $categories = ProductCategory::whereNull('parent_category_id')->where('show_in_public_menu', 1)->OrderBy('name', 'asc')->get();

        foreach ($categories as $category) {
            $category->image_for_app = asset(env('CATEGORY_PHOTO_PATH') . $category->image);
        }

        if (!empty($categories)) return response()->json(['error' => false, 'categories' => $categories]);
        else return response()->json(['error' => true]);
    }

    // Category Product List
    public function categoryProductList(Request $request)
    {
        $category_id = $request->category_id;

        $categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::PRODUCT)->OrderBy('name', 'asc')->where('id', $category_id)->get();

        if (isset($category_id)) {
            $products = Product::orderBy('name')->whereIn('category_id', $categories)->get();
        }

        if (!empty($products)) return response()->json(['error' => false, 'products' => $products]);
        else return response()->json(['error' => true]);
    }

    // Product Details
    public function productDetails(Request $request)
    {
        $product_id = $request->product_id;

        if (isset($product_id)) {
            $product = Product::find($product_id);
        }

        if (!empty($product)) return response()->json(['error' => false, 'product' => $product]);
        else return response()->json(['error' => true]);
    }

    // Category Product List
    public function productsByCategory($category_id)
    {
        $view = '';
        if(!empty($_GET['view'])) $view = $_GET['view'];

        $paginate = 10;
        $category = ProductCategory::find($category_id);
        if (isset($category)) {

            $allChild = ProductCategory::getAllChildCategory([$category]);

            if ($category->product_category_type_id == ProductTypeEnum::PRODUCT) {
                $products = Product::wherein('products.category_id', $allChild)->where('quantity', '>', 0);
            } elseif ($category->product_category_type_id == ProductTypeEnum::SERVICE) {
                $now = Carbon::now()->format('Y-m-d 00:00:00');
                $products = Product::wherein('products.category_id', $allChild)->join('subscriptions', 'subscriptions.seller_id', '=', 'products.seller_id')->where('subscriptions.to_date', '>=', $now);
            }

            $products = $products->orderby('created_at', 'desc');

//                ->get()
// ->paginate($paginate);
            $product_count = $products->count();

            if($view == 'all'){
              $products = $products->get();
            }else{
              $products = $products->paginate($paginate);
            }

            foreach ($products as $product) {
                $media = $product->getMedia;
                if(isset($media[0])){
                    $product->image = asset(env('MEDIA_PHOTO_PATH') . $media[0]->file_in_disk);
                }
                else{
                    $product->image = asset('image/no-media.jpg');
                }
            }



            if (!empty($products)) return response()->json(['error' => false,'product_count' => $product_count, 'products' => $products]);
            else return response()->json(['error' => true]);
        }
    }
}
