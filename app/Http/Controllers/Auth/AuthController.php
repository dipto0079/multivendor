<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\SettingsEnum;
use App\Model\City;
use App\Model\Department;
use App\Model\Company;
use App\Model\Setting;
use App\User;
use App\UtilityFunction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Role;
use Session;
use Mail;
use Hash;
use Input;
use File;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;
use App\Http\Controllers\Enum\BuyerStatusEnum;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\Model\Seller;
use App\Model\Buyer;
use App\Model\ProductCategory;
use App\Http\Controllers\Enum\ProductTypeEnum;
use App\Model\Country;


class AuthController extends Controller
{
    protected $loginPath = 'auth2/login';
    protected $redirectTo = '/';
    protected $redirectAfterLogout = '/';
    protected $redirectPath = '/';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    public function captchaGenerate()
    {
        session_start();

        $alphabet = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $random_alpha = implode($pass);
        $captcha_code = substr($random_alpha, 0, 6);
        Session::put('captcha_code', $captcha_code);
        $target_layer = imagecreatetruecolor(70, 35);
        $captcha_background = imagecolorallocate($target_layer, 255, 255, 255);
        imagefill($target_layer, 0, 00, $captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        imagestring($target_layer, 5, 5, 5, $captcha_code, $captcha_text_color);
        header("Content-type: image/jpeg");
        imagejpeg($target_layer);
    }
    public function captchaGenerate1()
    {
        session_start();

        $alphabet = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $random_alpha = implode($pass);
        $captcha_code = substr($random_alpha, 0, 6);
        Session::put('captcha_code1', $captcha_code);
        $target_layer = imagecreatetruecolor(70, 35);
        $captcha_background = imagecolorallocate($target_layer, 255, 255, 255);
        imagefill($target_layer, 0, 00, $captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        imagestring($target_layer, 5, 5, 5, $captcha_code, $captcha_text_color);
        header("Content-type: image/jpeg");
        imagejpeg($target_layer);
    }

    public function safe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    public function encode($value)
    {
        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, "SuPerEncKey2010a", $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    public function hasRole($role)
    {
        return User::where('role', $role)->get();
    }

    /**************************************** ADMIN *******************************/
    public function adminUserList()
    {
        return view('frontend/index');
    }

    public function viewCreateAdminUserForm()
    {
        $allRoles = Role::orderBy('name', 'asc')->distinct()->get();
        return view('admin/User/add')->with('allRoles', $allRoles);
    }

    protected function saveAdminUser(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role_id = $request->roles;
        $user->photo = $request->text_uploaded_image;
        $user->save();

        return redirect('/admin/user/list');
    }

    public function editAdminUser($id)
    {
        $user = User::find($id);
        $allRoles = Role::orderBy('name', 'asc')->distinct()->get();
        return view('admin/user/edit')->with('user', $user)->with('allRoles', $allRoles);
    }

    protected function updateAdminUser(Request $request)
    {
        $id = $request->input('id');
        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != "") {
            $user->password = bcrypt($request->password);
        }

        $user->role_id = $request->roles;

        if ($request->text_uploaded_image != "") {
            $user->photo = $request->text_uploaded_image;
        }
        $user->save();

        return redirect('/admin/user/list');
    }

    public function deleteAdminUser($id)
    {
        User::find($id)->delete();
        return redirect('/admin/user/list');
    }

    public function getAdminLogin(Request $request)
    {
        return view('admin/auth/login');
    }

    public function postAdminLogin(Request $request)
    {
        try {
            $get_Attempted_DB_UserDetails = DB::table('users')->where('email', $request->email)
                ->first();

            if ($request->captcha == $request->session()->get('captcha_code')) {
                if (isset($get_Attempted_DB_UserDetails)) {
                    if (Hash::check($request->password, $get_Attempted_DB_UserDetails->password)) {
                        if ($get_Attempted_DB_UserDetails->confirmation_code == null) {
                            if ($get_Attempted_DB_UserDetails->status == 0) {
                                if ($get_Attempted_DB_UserDetails->user_type == UserTypeEnum::ADMIN) {

                                    $this->loginPath = env('REDIRECT_LOCATION_AFTER_SUCCESSFUL_ADMIN_LOGIN');
                                    $this->redirectPath = env('REDIRECT_LOCATION_AFTER_SUCCESSFUL_ADMIN_LOGIN');

                                    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                                        return redirect()->intended(env('REDIRECT_LOCATION_AFTER_SUCCESSFUL_ADMIN_LOGIN'));
                                    }
                                } else {
                                    return redirect()->back()->with('message', 'Invalid Username or Password');
                                }
                            } else {
                                return redirect()->back()->with('message', 'Your information not verified yet by an administrator. You will receive an email after the verification.');
                            }
                        } else {
                            return redirect()->back()->with('message', 'Please confirm your email.');
                        }
                    } else {
                        return redirect()->back()->with('message', 'Invalid user id or password');
                    }
                }
                return redirect()->back()->with('message', 'You have no account in this site.');
            }
            return redirect()->back()->with('message', 'Your captcha is wrong.');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Something Went Wrong. Please Try Again');
        }
    }

    protected function adminLogout()
    {
        $this->redirectAfterLogout = env('REDIRECT_LOCATION_AFTER_ADMIN_LOGOUT');
        Auth::logout();
        return redirect()->intended(env('REDIRECT_LOCATION_AFTER_ADMIN_LOGOUT'));
    }

    public function adminProfileUpdate(Request $request)
    {
        $profile = User::where('id', Auth::user()->id)->where('user_type', UserTypeEnum::ADMIN)->first();
        $profile->username = $request->username;
        $profile->email = $request->email;
        if (!empty($request->password) && $request->password != $request->confirm_passeord) {
            $profile->password = bcrypt($request->password);
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Please Write Confirm Password Same as Password');
        }
        $photo = $request->photo;

        if (!empty($photo)) {
            if (!empty($profile->photo) && file_exists(env('USER_PHOTO_PATH') . $profile->photo))
                unlink(env('USER_PHOTO_PATH') . $profile->photo);
            //Move Uploaded File
            $destinationPath = env('USER_PHOTO_PATH');

            $photo->move($destinationPath, time() . $photo->getClientOriginalName());
            $path = time() . $photo->getClientOriginalName();
            $profile->photo = $path;
        }

        $profile->save();

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
    }

    /**************************************** BUYER *******************************/
    public function buyerRegistration()
    {
        return view('frontend/auth/buyer-registration');
    }

    public function buyerRegistrationSave(Request $request)
    {
        if ($request->password == $request->password_confirm) {
            if (!User::where('email', $request->email)->where('user_type', UserTypeEnum::USER)->exists()) {
                DB::beginTransaction();

                $user = new User();
                $user->username = $request->name;
                $user->email = $request->email;
                $user->phone = $request->mobile;
                $user->password = bcrypt($request->password);
                $user->user_type = UserTypeEnum::USER;
                $user->confirmation_code = str_random(60);
                $user->save();

                $buyer = new Buyer();
                $buyer->user_id = $user->id;
                $buyer->save();

                $data = [
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                ];

                Mail::send('emails.buyerRegistrationConfirmation', $data, function ($message) use ($user) {
                    $message->to($user->email, $user->name)->subject('Confirm Your Email');
                });

                DB::commit();

                redirect('/')->with('registration_success_message', trans('messages.error_message.confirmation_message_sent_to_email'));
            } else {
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.account_already_exist_with_this_email'));
            }
        } else {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . trans('messages.error_message.password_not_matched'));
        }
    }

    // Buyer Email Confirmation
    public function buyerEmailConfirmation($confirmation_code)
    {
        if ($confirmation_code) {
            $user = User::whereConfirmationCode($confirmation_code)->first();

            if ($user) {
                $user->confirmation_code = null;
                $user->save();
            } else {
                return redirect('/buyer/login')->with('buyer_message', trans('messages.error_message.email_already_verified'));
            }
            return redirect('/buyer/login')->with('buyer_message', trans('messages.error_message.email_confirmed_successfully'));

        } else {
            return redirect('/buyer-registration')->with('message', trans('messages.error_message.please_register_account'));
        }
    }


    public function getLogin()
    {
        return view('frontend/auth/login');
    }

    public function loginBuyer()
    {
        return view('frontend/auth/buyer-login');
    }

    public function loginBuyerPost(Request $request)

    {

        if ($request->captcha == $request->session()->get('captcha_code')) {

            $get_Attempted_DB_UserDetails = User::where('email', trim($request->email))->where('user_type', UserTypeEnum::USER)->first();

            if (isset($get_Attempted_DB_UserDetails)) {
                if ($get_Attempted_DB_UserDetails->confirmation_code == null) {
                    $buyer = $get_Attempted_DB_UserDetails->getBuyer;

                    if (isset($buyer->status) && $buyer->status != BuyerStatusEnum::APPROVED) {
                        return redirect()->back()->with('buyer_message', trans('messages.error_message.account_blocked_by_admin'));
                    }

                    if (Hash::check($request->password, $get_Attempted_DB_UserDetails->password)) {
                        //role_id==0 means only public user
                        if ($get_Attempted_DB_UserDetails->user_type == UserTypeEnum::USER) {
                            $session_url = Session::get('session_url');

                            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => UserTypeEnum::USER])) {
                                if (isset($session_url)) {
                                    Session::forget('session_url');
                                    return redirect()->intended($session_url);
                                }

                                return redirect()->intended('/buyer/cart-list');
                            }
                            return redirect()->back()->with('buyer_message', trans('messages.error_message.request_not_approved_yet'));
                        }
                    }
                } else {
                    return redirect()->back()->with('buyer_message', trans('messages.error_message.confirmation_message_sent_to_email'));
                }
            }
            return redirect()->back()->with('buyer_message', trans('messages.error_message.invalid_email_or_password'));
        }
        return redirect()->back()->with('buyer_message', 'Your captcha is wrong.');

    }

    public function redirectToFacebookProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookProviderCallback()
    {
        $redirect = 'buyer/edit-profile';
        $socialUserInfo = Socialite::driver('facebook')->user();

        try {
            $email = $socialUserInfo->getEmail();
            $user = User::where('email', $email)->where('user_type', UserTypeEnum::USER)->first();

            if (isset($email)) {
                if (!empty($user) && $user->confirmation_code == null) {

                    $buyer = $user->getBuyer;
                    if (isset($buyer->status) && $buyer->status != BuyerStatusEnum::APPROVED) {
                        return redirect()->back()->with('message', trans('messages.error_message.account_blocked_by_admin'));
                    }

                    if ($user->confirmation_code != null) {
                        return redirect()->back()->with('error_msg', trans('messages.error_message.email_not_verified'));
                    }

                    // if (Auth::attempt(['email' => $email, 'password' => $socialUserInfo->getId(), 'user_type' => UserTypeEnum::USER, 'confirmation_code' => null])) {
                    //     return redirect()->intended($redirect);
                    // }

                    Auth::login($user, true);

                    return redirect('/buyer/edit-profile');

                } else {
                    $user = new User();
                    $user->username = $socialUserInfo->getName();
                    $user->email = $email;
                    $user->password = bcrypt($socialUserInfo->getId());
                    $user->facebook_id = $socialUserInfo->getId();
                    $user->user_type = UserTypeEnum::USER;
                    $user->photo = $socialUserInfo->getAvatar();

                    $user->save();

                    $buyer = new Buyer();
                    $buyer->user_id = $user->id;
                    $buyer->save();

                    Auth::login($user, true);
                }
            } else {
                return redirect()->back()->with('error_msg', 'Invalid Input');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error_msg', $e->getMessage());
        }
    }

    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleProviderCallback()
    {
        $redirect = 'buyer/edit-profile';
        $socialUserInfo = Socialite::driver('google')->stateless()->user();

        try {
            $email = $socialUserInfo->getEmail();
            $user = User::where('email', $email)->where('user_type', UserTypeEnum::USER)->first();

            if (isset($email)) {
                if (!empty($user) && $user->confirmation_code == null) {

                    $buyer = $user->getBuyer;
                    if (isset($buyer->status) && $buyer->status != BuyerStatusEnum::APPROVED) {
                        return redirect()->back()->with('message', trans('messages.error_message.account_blocked_by_admin'));
                    }

                    if ($user->confirmation_code != null) {
                        return redirect()->back()->with('error_msg', trans('messages.error_message.email_not_verified'));
                    }

                    // if (Auth::attempt(['email' => $email, 'password' => $socialUserInfo->getId(), 'user_type' => UserTypeEnum::USER, 'confirmation_code' => null])) {
                    //     return redirect()->intended($redirect);
                    // }

                    Auth::login($user, true);

                    return redirect('/buyer/edit-profile');

                } else {
                    $user = new User();
                    $user->username = $socialUserInfo->getName();
                    $user->email = $email;
                    $user->password = bcrypt($socialUserInfo->getId());
                    $user->google_id = $socialUserInfo->getId();
                    $user->user_type = UserTypeEnum::USER;
                    $user->photo = $socialUserInfo->getAvatar();

                    $user->save();

                    $buyer = new Buyer();
                    $buyer->user_id = $user->id;
                    $buyer->save();

                    Auth::login($user, true);
                }
            } else {
                return redirect()->back()->with('error_msg', 'Invalid Input');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error_msg', $e->getMessage());
        }
    }

    /**************************************** SELLER *******************************/
    public function sellerRegistration()
    {

        $product_categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::PRODUCT)
            ->where('parent_category_id', null)
            ->where('product_category_level_no', 1)
            ->get();

        $service_categories = ProductCategory::where('product_category_type_id', ProductTypeEnum::SERVICE)
            ->where('parent_category_id', null)
            ->where('product_category_level_no', 1)
            ->get();

        $countries = Country::join('cities','cities.country_id','=','countries.id')
            ->select('countries.*')
            ->groupby('countries.id')
            ->orderBy('countries.name', 'asc')
            ->get();

        return view('/frontend/auth/seller-registration')
            ->with('countries', $countries)
            ->with('product_categories', $product_categories)
            ->with('service_categories', $service_categories);
    }

    public function sellerRegistrationCityByCountry(Request $request)
    {
        $country = $request->country;
        $cities = City::where('country_id', $country)->orderby('name', 'asc')->get();
        $cities_html = '';
        foreach ($cities as $city) {
            $cities_html .= '<option value="' . $city->id . '">';
            if (\App\UtilityFunction::getLocal() == "en") $cities_html .= $city->name;
            else $cities_html .= $city->ar_name;
            $cities_html .= '</option>';
        }
        return response()->json(['cities_html' => $cities_html]);
    }

    public function sellerRegistrationSave(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = new User();
            $user->email = $request->email_id;
            $user->username = $request->name;
            $user->user_type = UserTypeEnum::SELLER;
            $user->phone = $request->phone_number;

            $path = env('USER_PHOTO_PATH');
            //now upload the photo
            $image = $request->file('profile_image');
            if (isset($image)) {
                $fileName = 'user_' . date('Y-m-d-g-i-a') . $image->getClientOriginalName();
                $image->move($path . '/', $fileName);
                $user->photo = $fileName;
            }

            $user->save();

            $user_last_id = $user->id;

            $seller = new Seller();
            $seller->user_id = $user_last_id;
            $seller->status = SellerStatusEnum::PENDING;
            $seller->about_me = $request->about_me;
            $seller->company_name = $request->business_name;
            $seller->store_name = strtolower(trim($request->store_name));
            $seller->business_email = $request->business_email;
            $seller->business_address = $request->business_address;
            $seller->business_type = $request->seller_type;
            $seller->category_id = $request->categoryName;
            $seller->contact_details = $request->phone_number;
            $seller->street = $request->street;
            $seller->city = $request->city;
            $seller->state = $request->state;
            $seller->zip = $request->zip;
            $seller->country = $request->country;
            $seller->website = $request->business_website;
            if ($seller->business_type == ProductTypeEnum::PRODUCT) $seller->commission = Setting::getValueByKey(SettingsEnum::PRODUCT_SELLER_COMMISSION);
            elseif ($seller->business_type == ProductTypeEnum::SERVICE) $seller->commission = Setting::getValueByKey(SettingsEnum::SERVICE_SELLER_COMMISSION);
            $seller->save();
            DB::commit();

            $data = [];

            Mail::send('emails.userRegistrationConfirmation', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject('Confirm Your Email');
            });

            if (Mail::failures()) {
                return redirect()->back()->with('error_message', trans('messages.error_message.cannot_send_message_try_again'));
            }

            return redirect('/')->with('registration_success_message', trans('messages.seller_registration.registration_confirmation'));

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error_message', trans('messages.error_message.cannot_send_message_try_again'));
        }
    }

    public function loginSeller()
    {
        return view('frontend/auth/seller-login');
    }

    public function loginSellerPost(Request $request)
    {
       //dd($request->all());
     //  die();
        if ($request->captcha == $request->session()->get('captcha_code1')) {


            $get_Attempted_DB_UserDetails = User::where('email', trim($request->email))->where('user_type', UserTypeEnum::SELLER)->first();

            if (isset($get_Attempted_DB_UserDetails)) {
                if (Hash::check($request->password, $get_Attempted_DB_UserDetails->password)) {

                    if ($get_Attempted_DB_UserDetails->user_type == UserTypeEnum::SELLER) {

                        if ($get_Attempted_DB_UserDetails->getSeller->status == SellerStatusEnum::PENDING) {
                            return redirect()->back()->with('message', trans('messages.error_message.request_not_approved_yet'));
                        }

                        if ($get_Attempted_DB_UserDetails->getSeller->status == SellerStatusEnum::REJECTED) {
                            return redirect()->back()->with('message', trans('messages.error_message.account_rejected_by_admin'));
                        }

                        if ($get_Attempted_DB_UserDetails->getSeller->status == SellerStatusEnum::BLOCKED) {
                            return redirect()->back()->with('message', trans('messages.error_message.account_blocked_by_admin'));
                        }

                        if ($get_Attempted_DB_UserDetails->getSeller->status == SellerStatusEnum::APPROVED) {

                            $this->loginPath = env('REDIRECT_LOCATION_AFTER_SUCCESSFUL_ADMIN_LOGIN');
                            $this->redirectPath = env('REDIRECT_LOCATION_AFTER_SUCCESSFUL_ADMIN_LOGIN');

                            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => UserTypeEnum::SELLER])) {
                                return redirect()->intended('/seller/order-list');
                            }
                        }
                    }
                }
            }
            return redirect()->back()->with('message', trans('messages.error_message.invalid_email_or_password'));

        }
        return redirect()->back()->with('message', 'Your captcha is wrong.');
    }

    /**************************************** PUBLIC *******************************/
    public function getPublicLogin(Request $request)
    {
        return view('auth/login');
    }

    public function confirm($confirmation_code)
    {
        if ($confirmation_code) {
            $user = User::whereConfirmationCode($confirmation_code)->first();
            if ($user) {
                $user->confirmation_code = null;
                //$user->status= 0;
                $user->save();
            } else {
                return Redirect('/login')->with('message', trans('messages.error_message.email_already_verified_please_login'));
            }

            return Redirect('/login');

        } else {
            return Redirect('/login')->with('message', trans('messages.error_message.register_again'));
        }
    }

    protected function publicLogout()
    {
        $this->redirectAfterLogout = env('REDIRECT_LOCATION_AFTER_PUBLIC_LOGOUT');
        Auth::logout();

        return redirect()->intended(env('REDIRECT_LOCATION_AFTER_PUBLIC_LOGOUT'));
    }
}
