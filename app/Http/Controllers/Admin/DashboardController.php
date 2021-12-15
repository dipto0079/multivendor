<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\ProductTypeEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Model\Order;
use App\Model\Product;
use App\Model\Question;
use App\Model\QuestionAnswer;
use App\Model\Seller;
use App\Model\SubOrder;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon;

class DashboardController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard(){
        $pending_order = Order::where('status',OrderStatusEnum::PENDING)->count();

        $total_service_seller = User::where('confirmation_code',null)->join('sellers','users.id','=','sellers.user_id')
            ->where('sellers.status',SellerStatusEnum::APPROVED)
            ->where('users.user_type',UserTypeEnum::SELLER)
            ->where('sellers.business_type',ProductTypeEnum::SERVICE)->count();
        $total_product_seller = User::where('confirmation_code',null)->join('sellers','users.id','=','sellers.user_id')
            ->where('sellers.status',SellerStatusEnum::APPROVED)
            ->where('users.user_type',UserTypeEnum::SELLER)
            ->where('sellers.business_type',ProductTypeEnum::PRODUCT)->count();

        $total_buyer = User::where('confirmation_code',null)->where('users.user_type',UserTypeEnum::USER)->count();

        $products = Product::all();
        $total_deals = 0;
        foreach($products as $product){
            $deals = $product->getProductDeals;
            $total_deals = $total_deals + (!empty($deals)) ? 1 : 0;
        }

        $sales_today = Order::whereDate('created_at',date('Y-m-d'))
            ->where('status',OrderStatusEnum::ACCEPTED)
            ->orwhere('status',OrderStatusEnum::FINALIZED)
            ->orwhere('status',OrderStatusEnum::DELIVERED)->sum('sub_total_price');

        return view('/admin/dashboard')
            ->with('pending_order',$pending_order)
            ->with('total_service_seller',$total_service_seller)
            ->with('total_product_seller',$total_product_seller)
            ->with('total_buyer',$total_buyer)
            ->with('total_deals',$total_deals)
            ->with('sales_today',$sales_today)
            ->with('product_count',count($products))
            ;
    }


    // Dashboard Ajax
    public function dashboardAjax(){
        $pending_order = Order::where('status',OrderStatusEnum::PENDING)->count();

        $total_service_seller = User::where('confirmation_code',null)->join('sellers','users.id','=','sellers.user_id')
            ->where('sellers.status',SellerStatusEnum::APPROVED)
            ->where('users.user_type',UserTypeEnum::SELLER)
            ->where('sellers.business_type',ProductTypeEnum::SERVICE)->count();

        $new_service_seller = User::where('confirmation_code',null)->join('sellers','users.id','=','sellers.user_id')
            ->where('sellers.status',SellerStatusEnum::PENDING)
            ->where('users.user_type',UserTypeEnum::SELLER)
            ->where('sellers.business_type',ProductTypeEnum::SERVICE)->count();

        $total_product_seller = User::join('sellers','users.id','=','sellers.user_id')
            ->where('users.confirmation_code',null)
            ->where('users.user_type',UserTypeEnum::SELLER)
            ->where('sellers.status',SellerStatusEnum::APPROVED)
            ->where('sellers.business_type',ProductTypeEnum::PRODUCT)->count();

        $new_product_seller = user::join('sellers','users.id','=','sellers.user_id')
            ->where('users.confirmation_code',null)
            ->where('users.user_type',UserTypeEnum::SELLER)
            ->where('sellers.status',SellerStatusEnum::PENDING)
            ->where('sellers.business_type',ProductTypeEnum::PRODUCT)->count();

        $total_buyer = User::where('confirmation_code',null)->where('users.user_type',UserTypeEnum::USER)->count();

        $products = Product::all();
        $total_deals = 0;
        foreach($products as $product){
            $deals = $product->getProductDeals;
            $total_deals = $total_deals + (!empty($deals)) ? 1 : 0;
        }

        $sales_today = Order::whereDate('created_at',date('Y-m-d'))
            ->where('status',OrderStatusEnum::ACCEPTED)
            ->orwhere('status',OrderStatusEnum::FINALIZED)
            ->orwhere('status',OrderStatusEnum::DELIVERED)->sum('sub_total_price');

        $questions = Question::orderBy('created_at','desc')->where('is_reviewed',0)->get();
        $question_answers = QuestionAnswer::orderBy('created_at','desc')->where('is_viewed',0)->get();

        $question_html = '';
        if(!empty($questions[0])) {
            foreach($questions as $question){
                    $question_html .= '<a href="'.url('/admin/settings/question/details/'.$question->id).'" class="mess-item">
                                    <span class="avatar-preview avatar-preview-32">
                                        <img ';  
                                            if(!empty($question->getUser->photo)) $question_html .= 'src="'.asset(env('USER_PHOTO_PATH').$question->getUser->photo).'"';
                                            else $question_html .= 'src="'.asset('/image/default_author.png').'"';
                                                $question_html .= ' alt="">
                                        </span>
                                        <span class="mess-item-name">'.$question->getUser->username.'</span>
                                        <span class="mess-item-txt">'.str_limit($question->title,35).'</span>
                                        <span class="mess-item-txt">'.Carbon\Carbon::createFromTimeStamp(strtotime($question->created_at))->diffForHumans().'</span>
                                    </a>';
            }
        }

        $question_answer_html = '';
        if(!empty($question_answers[0])) {
            foreach($question_answers as $question_answer){
                $question_answer_html .= '<a href="'.url('/admin/settings/question/details/'.$question_answer->getQuestion->id).'" class="mess-item">
                                    <span class="avatar-preview avatar-preview-32">
                                        <img ';
                                            if(!empty($question_answer->getUser->photo)) $question_answer_html .= 'src="'.asset(env('USER_PHOTO_PATH').$question_answer->getUser->photo).'"';
                                            else $question_answer_html .= 'src="'.asset('/image/default_author.png').'"';
                                            $question_answer_html .= ' alt="">
                                        </span>
                                        <span class="mess-item-name">'.$question_answer->getUser->username.'</span>
                                        <span class="mess-item-txt">'.str_limit($question_answer->answer,35).'</span>
                                        <span class="mess-item-txt">'.Carbon\Carbon::createFromTimeStamp(strtotime($question_answer->created_at))->diffForHumans().'</span>
                                    </a>';
            }
        }

        //dd($question_html);
        

        $question_count = count($questions);
        $question_answer_count = count($question_answers);

        $new_payment = SubOrder::where('status',OrderStatusEnum::CLAIMED)->count();

        return response()->json([
            'pending_order' => $pending_order,
            'total_service_seller' => $total_service_seller,
            'new_service_seller' => $new_service_seller,
            'total_product_seller' => $total_product_seller,
            'new_product_seller' => $new_product_seller,
            'total_buyer' => $total_buyer,
            'total_deals' => $total_deals,
            'question_count' => $question_count,
            'question_answer_count' => $question_answer_count,
             'question_html' => $question_html,
             'question_answer_html' => $question_answer_html,
            'sales_today' => number_format($sales_today,2),
            'product_count' => count($products),
            'new_payment' => $new_payment
        ]);
    }
}
