<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Model\Order;
use App\Model\PushNotification;
use App\Model\Buyer;
use App\Model\PushNotificationReceiver;
use App\Model\Question;
use App\Model\SubOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Enum\BuyerStatusEnum;
use Mail;

class BuyerController extends Controller
{
    public function buyerList(Request $request)
    {
        $status = BuyerStatusEnum::APPROVED;
        $search_token = $request->buyer_search;
        if(!empty($request->status)) $status = $request->status;

        $buyers = User::join('buyers', 'buyers.user_id', 'users.id')
            ->select('users.id', 'users.photo', 'users.username', 'users.email', 'users.phone', 'buyers.id as buyer_id', 'buyers.status', 'users.created_at')
            ->where('users.user_type', UserTypeEnum::USER)
            ->where(function ($q) use ($search_token) {
                $q->where('username', 'LIKE', '%' . $search_token . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search_token . '%')
                    ->orWhere('email', 'LIKE', '%' . $search_token . '%');
            })
            ->where('buyers.status',$status)
            ->OrderBy('username', 'asc')
            ->paginate(env('PAGE_PAGINATE'));

        return view('admin/buyer/list')
            ->with('search_token', $search_token)
            ->with('status', $status)
            ->with('buyers', $buyers)
            ;
    }
    public function buyerDelete($id)
    {
        try {
            $buyer = Buyer::find($id);
            $buyer->status = BuyerStatusEnum::ARCHIVE;
            $buyer->save();
//            dd($buyer);
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }
    public function buyerBlock(Request $request)
    {
        try {
            if (!empty($request->buyer_id_for_featured)) {

                $buyer = Buyer::where('user_id', $request->buyer_id_for_featured)->first();

                if(isset($buyer->status))
                {
                    if ($buyer->status == BuyerStatusEnum::APPROVED) $buyer->status = BuyerStatusEnum::BLOCKED;
                    elseif ($buyer->status == BuyerStatusEnum::BLOCKED) $buyer->status = BuyerStatusEnum::APPROVED;

                    $buyer->save();

                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                }
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));
            } else {
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }
    public function buyerEditProfile(Request $request)
    {
        try {

            if (isset($_GET['edit_id'])) {

                if (isset($_GET['edit_id']))
                    $user = User::where('id', $_GET['edit_id'])->first();

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="">User Name</label>
                                    <input type="text" class="form-control" name="username" value="' . $user->username . '">
                                </div>
                            </div>';
                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="">Email</label>
                                    <span class="form-control">' . $user->email . '</span>
                                </div>
                            </div>';
                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="">Password</label>
                                    <input type="password" class="form-control" name="password" value="">
                                </div>
                            </div>';
                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" value="">
                                </div>
                            </div>';

                $data_generate .= '<div class="clearfix"></div>';


                if (isset($_GET['edit_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . Crypt::encrypt($user->id) . '">';


                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $encrypted_id = $request->input('edit_id');
                if (isset($encrypted_id)) {
                    try {
                        $user = User::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($user)) {
                    if ($request->password == $request->confirm_password) {
                        if (!empty($request->password)) $user->password = bcrypt($request->password);
                    } else {
                        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . 'Password not matched.');
                    }

                    $user->username = $request->username;

                    $user->save();


                }

                if (isset($encrypted_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }

    }

    public function buyerNotificationList($buyer_id)
    {
        $buyer = Buyer::where('id',$buyer_id)->first();
        $user_id = $buyer->getUser->id;
        $notifications = PushNotification::join('push_notification_receivers', 'push_notification_receivers.push_notification_id', '=', 'push_notifications.id')
            ->where('push_notification_receivers.receiver_id', $user_id)
            ->select(
                'push_notifications.id'
                , 'push_notifications.notification_by'
                , 'push_notifications.description'
                , 'push_notifications.notification_repeat'
                , 'push_notifications.receiver_type'

            )
            ->OrderBy('push_notifications.updated_at', 'desc')
            ->paginate(env('PAGINATION_SMALL'));

        return view('admin/buyer/notification/list')
            ->with('user_id',$user_id)
            ->with('buyer_id', $buyer_id)
            ->with('notifications', $notifications);
    }
    public function buyerNotificationSave(Request $request)
    {
        try {
            $notification = new PushNotification();
            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {

                if (isset($_GET['edit_id']))
                    $notification = PushNotification::where('id', $_GET['edit_id'])->first();

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="">Repeat Type</label>
                                    <select name="notification_repeat" class="form-control">';
                $data_generate .= '<option ';
                if ($notification->notification_repeat == 1) $data_generate .= ' selected ';
                $data_generate .= 'value="' . PushNotificationRepeatEnum::ONCE . '">Once</option>';
                $data_generate .= '<option ';
                if ($notification->notification_repeat == 2) $data_generate .= ' selected ';
                $data_generate .= 'value="' . PushNotificationRepeatEnum::DAILY . '">Daily</option>';
                $data_generate .= '<option ';
                if ($notification->notification_repeat == 3) $data_generate .= ' selected ';
                $data_generate .= 'value="' . PushNotificationRepeatEnum::WEEKLY . '">Weekly</option>';
                $data_generate .= '<option ';
                if ($notification->notification_repeat == 4) $data_generate .= ' selected ';
                $data_generate .= 'value="' . PushNotificationRepeatEnum::MONTHLY . '">Monthly</option>';
                $data_generate .= '<option ';
                if ($notification->notification_repeat == 5) $data_generate .= ' selected ';
                $data_generate .= 'value="' . PushNotificationRepeatEnum::YEARLY . '">Yearly</option>';

                $data_generate .= '</select>
                                </div>
                            </div>';
                $data_generate .= '<div class="col-sm-12">
                                <div class="form-group">
                                    <label class="">Message</label>
                                    <textarea name="description" class="form-control" rows="6">' . $notification->description . '</textarea>
                                </div>
                            </div>';


                $data_generate .= '<div class="clearfix"></div>';


                if (isset($_GET['add_id'])) $data_generate .= '<input type="hidden" name="seller_id" value="' . $_GET['seller_id'] . '">';
                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" id="edit_id" class="form-control" name="edit_id" value="' . Crypt::encrypt($notification->id) . '">';


                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $encrypted_id = $request->input('edit_id');
                if (!empty($request->seller_id)) $seller_id = Crypt::decrypt($request->seller_id);
                if (isset($encrypted_id)) {
                    try {
                        $notification = PushNotification::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($notification)) {
                    DB::beginTransaction();

                    if (!isset($encrypted_id)){
                        $buyer = Buyer::where('id',$seller_id)->first();
                        $user_id = $buyer->getUser->id;
                    }


                    $notification->notification_by = Auth::user()->id;
                    $notification->description = $request->description;
                    $notification->notification_repeat = $request->notification_repeat;
                    $notification->save();

                    if (isset($notification)) {
                        if (isset($encrypted_id)) $push_notification_receiver = PushNotificationReceiver::where('push_notification_id',$notification->id)->first();
                        else $push_notification_receiver = new PushNotificationReceiver();
                        $push_notification_receiver->push_notification_id = $notification->id;
                        $push_notification_receiver->is_viewed = 0;
                        if (!isset($encrypted_id)) $push_notification_receiver->receiver_id = $user_id;
                        $push_notification_receiver->receiver_type = PushNotificationRepeatTypeEnum::BUYER;

                        $push_notification_receiver->save();
                    }

                    if (!isset($encrypted_id)){
                        $buyer = Buyer::find($seller_id);
                        $user = User::find($buyer->user_id);
                    }else{
                        $user = User::find($push_notification_receiver->receiver_id);
                    }



                    DB::commit();

                    //dd($notification->description);

                    $data = ['username' => $user->username, 'description' => $notification->description];

                    Mail::send('emails.notification', $data, function ($message) use ($user) {
                        $message->to($user->email, $user->username)
                            ->subject('New Notification');
                    });

                }

                if (isset($encrypted_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }
    public function buyerNotificationDelete($id)
    {

        try {
            $notification = PushNotification::find($id);
            PushNotificationReceiver::where('push_notification_id', $notification->id)->delete();
            $notification->delete();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function buyerOrderList(Request $request,$buyer_id){

        $status = OrderStatusEnum::PENDING;
        $buyer = Buyer::find($buyer_id);
        $user_id = $buyer->user_id;

        $from = '';
        $to = '';

        if(!empty($request->from)) $from = date('Y-m-d H:i:s',strtotime($request->from.' 00:00:01'));
        if(!empty($request->to)) $to = date('Y-m-d H:i:s',strtotime($request->to.' 23:59:59'));

        if(isset($request->status)) $status = $request->status;

        $orders = Order::join('sub_orders','orders.id','=','sub_orders.order_id')
            ->select('orders.*')
            ->orderBy('orders.created_at','desc')
            ->where('orders.buyer_id',$buyer_id)
            ->groupby('orders.id');

        if($status == OrderStatusEnum::DELIVERED || $status == OrderStatusEnum::FINALIZED){
            $orders = $orders->where('sub_orders.status', $status);
        }else{
            $orders = $orders->where('orders.status', $status);
        }

        if(!empty($from) && !empty($to)){
            $orders = $orders->whereBetween('orders.created_at', [$from, $to]);
        }

        $orders = $orders->paginate(env('PAGE_PAGINATE'));

        return view('/admin/buyer/order/list')
            ->with('user_id', $user_id)
            ->with('buyer_id', $buyer_id)
            ->with('status', $status)
            ->with('orders', $orders);
    }

}
