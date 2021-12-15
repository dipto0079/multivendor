<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Model\Buyer;
use App\Model\City;
use App\Model\Country;
use App\Model\Seller;
use App\User;
use Illuminate\Http\Request;
use Crypt;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Facades\File;
use Mail;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{

    public function getFileUpload($path, $user_file_name, $requested_file_name)
    {

        if (!file_exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        if (isset($user_file_name) && $user_file_name != "" && file_exists($path . '/' . $user_file_name)) {
            unlink($path . '/' . $user_file_name);
        }

        $image_name = 'image_' . date('d-m-y') . '_' . time() . '.png';
        $path_mane = $path . '/image_' . date('d-m-y') . '_' . time() . '.png';
        $image = $requested_file_name;

        file_put_contents($path_mane, base64_decode($image));
        return $image_name;
    }

    public function randomString($number)
    {
        $alphabet = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $number; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $random_alpha = implode($pass);

        return $random_alpha;
    }

    /*****************************************************************************
     * User Activities
     *****************************************************************************/
    public function userLogin(Request $request)
    {
        try {
            $user = DB::table('users')->where('email', $request->email)->where('user_type', UserTypeEnum::USER)->first();
            if (!empty($request->email) && !empty($request->password)) {
                if (!empty($user) && $user->confirmation_code == null) {
                    if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => UserTypeEnum::USER, 'confirmation_code' => null])) {
                        $image = '';
                        if (!empty($user->photo)) $image = asset('/uploads/user/' . $user->photo);
                        return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
                    } else {
                        return response()->json(array('error' => true, 'error_msg' => 'email or password not valid'));
                    }
                } elseif (!empty($user) && $user->confirmation_code != null) {
                    return response()->json(array('error' => true, 'error_msg' => 'Your email is not verified. Please confirm your verification process'));
                } else {
                    return response()->json(array('error' => true, 'error_msg' => 'No account exists with this email.'));
                }
            } else {
                return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
            }
        } catch (Exception $e) {
            return response()->json(array('error' => true, 'error_msg' => $e->getMessage()));
        }
    }

    public function userRegister(Request $request)
    {
        if (!empty($request->first_name) && !empty($request->last_name) && !empty($request->email) && !empty($request->password)) {
            if (!DB::table('users')->where('email', $request->email)->exists()) {

                $confirmation_code = str_random(30);

                DB::beginTransaction();

                $user = new User();
                $user->username = $request->first_name . ' ' . $request->last_name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->phone = $request->phone_number;
                $user->confirmation_code = $confirmation_code;
                $user->user_type = UserTypeEnum::USER;
                $user->save();

                $buyer = new Buyer();
                $buyer->user_id = $user->id;
                $buyer->save();

                DB::commit();
                $data = ['username' => $user->username, 'email' => $user->email, 'password' => '', 'confirmation_code' => $confirmation_code];

                Mail::send('emails.buyerRegistrationConfirmation', $data, function ($message) use ($request, $user) {
                    $message->to($user->email, $user->first_name)->subject('Welcome!');
                });

                return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username));
            } elseif (DB::table('users')->where('email', $request->email)->exists() && DB::table('users')->where('email', $request->email)->whereNotNull('confirmation_code')->exists()) {
                $user = DB::table('users')->where('email', $request->email)->first();
                $data = ['user' => $user->username, 'email' => $user->email, 'password' => '', 'confirmation_code' => $user->confirmation_code];

                Mail::send('email.registration_confirmation_email', $data, function ($message) use ($request, $user) {
                    $message->to($user->email, $user->first_name)->subject('Welcome!');
                });
                return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username));
            } else {
                return response()->json(array('error' => true, 'error_msg' => 'email id already exists'));
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function userForgetPassword(Request $request)
    {
        if (!empty($request->email)) {
            $user = DB::table('users')->where('email', $request->email)->where('user_type', UserTypeEnum::USER)->first();
            if (!empty($user) && $user->confirmation_code == null) {

                $token = $this->randomString(10);
                DB::table('password_resets')->insert(['email' => $request->email, 'token' => bcrypt($token)]);

                $user = User::where('email', '=', $request->email)->first();

                $data = ['email' => $user->email, 'user_id' => $user->id, 'user_name' => $user->username, 'token' => $token];

                Mail::send('email.forget_password_email', $data, function ($message) use ($request) {
                    $message->to($request->email, 'something')->subject('Welcome!');
                });


                return response()->json(array('error' => false, 'error_msg' => 'A new password is sent to your email. Please check your email. '));
            } elseif (!empty($user) && $user->confirmation_code != null) {

                $data = ['user' => $user->username, 'confirmation_code' => $user->confirmation_code];

                Mail::send('email.registration_confirmation_email', $data, function ($message) use ($request, $user) {
                    $message->to($user->email, $user->username)->subject('Welcome!');
                });
                return response()->json(array('error' => false, 'error_msg' => 'Your email is not verified. Please Verify your email.'));
            } else {

                return response()->json(array('error' => false, 'error_msg' => 'Account not exists with this email'));
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function userRegisterVerification($varification_code)
    {

        if (!empty($varification_code)) {
            $user_id = User::where('confirmation_code', $varification_code)->first();
            if ($user_id) {
                $user = User::findOrFail($user_id->id);
                $user->confirmation_code = null;
                $user->save();
            } else {
                return view('/email-verified')->with('message', 'Email already verified. Please login.');
            }
            return view('/email-verified');
        } else {
            return Redirect('/email-verified')->with('message', 'Register again.');
        }
    }

    public function userFacebookLogin(Request $request)
    {
        if (!empty($request->first_name) && !empty($request->last_name) && !empty($request->email)) {
            $image = '';


            if (User::where('email', $request->email)->where('facebook_id', $request->facebook_id)->where('user_type', UserTypeEnum::USER)->exists()) {
                $user = DB::table('users')->where('email', $request->email)->first();

                if (!empty($user->photo)) $image = asset('/uploads/user/' . $user->photo);
                return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
            } else {
                if (!DB::table('users')->where('email', $request->email)->exists()) {
                    $user = new User();
                    $user->username = $request->first_name . ' ' . $request->last_name;
                    $user->email = $request->email;
                    if (!empty($request->facebook_link)) $user->facebook_link = $request->facebook_link;
                    if (!empty($request->facebook_id)) $user->facebook_id = $request->facebook_id;
                    $user->user_type = UserTypeEnum::USER;
                    $user->save();

                    $buyer = new Buyer();
                    $buyer->user_id = $user->id;
                    $buyer->save();

                    if (!empty($user->photo)) $image = asset('/uploads/user/' . $user->photo);
                    return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
                } else {
                    return response()->json(array('error' => true, 'error_msg' => 'You Are Not Authorised'));
                }
            }
        } else {
            return response()->json(array('error' => false, 'error_msg' => 'Invalid Input'));
        }
    }

    public function userGooglePlusLogin(Request $request)
    {
        if (!empty($request->first_name) && !empty($request->last_name) && !empty($request->email)) {
            $image = '';
            if (DB::table('users')->where('email', $request->email)->where('google_id', $request->google_id)->where('user_type', 1)->exists()) {
                $user = DB::table('users')->where('email', $request->email)->first();

                if (!empty($user->photo)) $image = asset('/uploads/user/' . $user->photo);
                return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
            } else {
                if (!DB::table('users')->where('email', $request->email)->exists()) {
                    $user = new User();
                    $user->username = $request->first_name . ' ' . $request->last_name;
                    $user->email = $request->email;
                    if (!empty($request->google_id)) $user->google_id = $request->google_id;
                    $user->user_type = UserTypeEnum::USER;
                    $user->save();

                    $buyer = new Buyer();
                    $buyer->user_id = $user->id;
                    $buyer->save();

                    if (!empty($user->photo)) $image = asset('/uploads/user/' . $user->photo);
                    return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
                } else {
                    return response()->json(array('error' => true, 'error_msg' => 'You Are Not Authorised'));
                }
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function getUserProfile(Request $request)
    {
        if (!empty($request->user_id)) {
            $user = User::find($request->user_id);
            $countries = Country::join('cities','cities.country_id','=','countries.id')
                ->select('countries.*')
                ->groupby('countries.id')
                ->orderBy('countries.name', 'asc')
                ->get();
            $citiesByCountry = City::orderBy('name', 'asc')->where('country_id', $user->getBuyer->country)->get();

            $username_exp = explode(' ', $user->username);

            $secondLast = '';
            $i = 0;

            foreach ($username_exp as $row) {
                if ($i >= 1) {
                    $secondLast .= $row . ' ';
                }
                $i++;
            }

            $buyer_city = (isset($user->getBuyer->getCity)) ? $user->getBuyer->getCity->name : null;
            $buyer_country = (isset($user->getBuyer->getCountryName)) ? $user->getBuyer->getCountryName->name : null;

            $user->first_name = $username_exp[0];
            $user->last_name = $secondLast;
            $user->street = $user->getBuyer->street;
            $user->state = $user->getBuyer->state;
            $user->zip = $user->getBuyer->zip;
            $user->city_name = (!empty($user->getBuyer->getDistrict)) ? $user->getBuyer->getDistrict->name : null;
            $user->country_name = (!empty($user->getBuyer->getCountryName)) ? $user->getBuyer->getCountryName->name : null;


            if (isset($user)) return response()->json(['error' => false, 'user' => $user, 'countries' => $countries, 'citiesByCountry' => $citiesByCountry]);
            else return response()->json(['error' => true]);
        }
        return response()->json(['error' => true]);
    }

    public function cityByCountry(Request $request)
    {
        try {
            if (!empty($request->country_name)) {
                $cities = City::join('countries', 'countries.id', '=', 'cities.country_id')
                    ->select('cities.name')
                    ->where('countries.name', $request->country_name)->get();

                return response()->json(['error' => false, 'cities' => $cities]);
            }
            return response()->json(['error' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    public function cityByCountryId(Request $request)
    {
        try {
            if (!empty($request->country_id)) {
                $cities = City::join('countries', 'countries.id', '=', 'cities.country_id')
                    ->select('cities.*')
                    ->where('countries.id', $request->country_id)->get();

                return response()->json(['error' => false, 'cities' => $cities]);
            }
            return response()->json(['error' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    public function countries(Request $request)
    {
        try {
            $countries = Country::orderBy('name', 'asc')->get();
            return response()->json(['error' => false, 'countries' => $countries]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    public function updateUserProfile(Request $request)
    {
        if (!empty($request->user_id)) {
            $user = User::findOrFail($request->user_id);
            if (isset($user)) {
                $user->username = $request->user_name;
                $user->email = $request->profile_email;

                if (!empty($request->profile_image)) {
                    $path = 'uploads/user';
                    $user->photo = $this->getFileUpload($path, $user->photo, $request->profile_image);
                }
                if (!empty($request->password)) $user->password = bcrypt($request->password);
                $user->phone = $request->phone_number;
                $user->user_type = UserTypeEnum::USER;
                $user->save();

//                $buyer = $user->getBuyer;
//                $buyer->street = $request->street;
//                $buyer->state = $request->state;
//                $buyer->city = $request->city;
//                $buyer->country = $request->country;
//                $buyer->save();

                if (!empty($user->photo)) $image = asset('uploads/user/' . $user->photo);

                return response()->json(array('error' => false, 'image' => $image, 'profile_email' => $user->email));

            } else {
                return response()->json(array('error' => true, 'error_msg' => 'User Not Valid'));
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    /*****************************************************************************
     * Merchant Activities
     *****************************************************************************/

    public function merchantLogin(Request $request)
    {
        $user = User::where('email', $request->email)->where('user_type', UserTypeEnum::Seller)->first();

        if (!empty($request->email) && !empty($request->password)) {
            if (!empty($user) && $user->confirmation_code == null) {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => UserTypeEnum::Seller, 'confirmation_code' => null])) {
                    $image = '';
                    if (!empty($user->photo)) $image = asset('/uploads/merchant/' . $user->merchant_id . '/user/' . $user->photo);
                    $user->merchant_name = $user->getMerchantInfo->name;
                    return response()->json(array('error' => false, 'user_id' => $user->id, 'role_user_name' => $user->merchant_name, 'store_id' => $user->store_id, 'user_role' => $user->role_id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
                } else {
                    return response()->json(array('error' => true, 'error_msg' => 'email or password not valid'));
                }
            } elseif (!empty($user) && $user->confirmation_code != null) {
                return response()->json(array('error' => true, 'error_msg' => 'Your email is not verified. Please confirm your verification process'));
            } else {
                return response()->json(array('error' => true, 'error_msg' => 'No account exists with this email.'));
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function sellerRegister(Request $request)
    {
        if (!empty($request->first_name) && !empty($request->last_name) && !empty($request->email) && !empty($request->password)) {

            if (!DB::table('users')->where('email', $request->email)->exists()) {
                $confirmation_code = str_random(30);

                $user = new User();
                $user->username = $request->first_name . ' ' . $request->last_name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->phone = $request->phone_number;
                $user->confirmation_code = $confirmation_code;
                $user->user_type = UserTypeEnum::SELLER;
                $user->save();

                $seller = new Seller();
                $seller->store_name = $request->input('store_name');
                $seller->user_id = $user->id;
                $seller->save();

                $data = ['user' => $user->username, 'email' => $user->email, 'password' => $request->password, 'confirmation_code' => $confirmation_code];
//
                Mail::send('email.registration_confirmation_email', $data, function ($message) use ($request) {
                    $message->to($request->email, $request->first_name)->subject('Welcome!');
                });

                \Log::info($user);
                return response()->json(array('error' => false, 'user_id' => $user->id, 'email' => $user->email, 'name' => $user->username, 'merchant_id' => $user->merchant_id));
            } else {
                return response()->json(array('error' => true, 'error_msg' => 'email id already exists'));
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function sellerForgetPassword(Request $request)
    {
        if (!empty($request->email)) {

            $user = DB::table('users')->where('email', $request->email)->where('user_type', UserTypeEnum::MERCHANT)->first();
            $token = $this->randomString(10);
            if (!empty($user) && $user->confirmation_code == null) {
                $token = $this->randomString(10);
                DB::table('password_resets')->insert(['email' => $request->email, 'token' => bcrypt($token)]);

                $user = User::where('email', '=', $request->email)->first();

                $data = ['email' => $user->email, 'user_id' => $user->id, 'user_name' => $user->username, 'token' => $token];

                Mail::send('email.forget_password_email', $data, function ($message) use ($request) {
                    $message->to($request->email, 'something')->subject('Welcome!');
                });

                return response()->json(array('error' => false, 'error_msg' => 'A new password is sent to your email. Please check your email. '));
            } elseif (!empty($user) && $user->confirmation_code != null) {

                $data = ['user' => $user->username, 'confirmation_code' => $user->confirmation_code];

                Mail::send('email.registration_confirmation_email', $data, function ($message) use ($request, $user) {
                    $message->to($user->email, $user->username)->subject('Welcome!');
                });
                return response()->json(array('error' => true, 'error_msg' => 'Your email is not verified. Please Verify your email '));
            } else {

                return response()->json(array('error' => true, 'error_msg' => 'Account not exists with this email'));
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function merchantFacebookLogin(Request $request)
    {
        if (!empty($request->first_name) && !empty($request->last_name) && !empty($request->email)) {
            $image = '';
            if (DB::table('users')->where('email', $request->email)->where('facebook_id', $request->facebook_id)->where('user_type', UserTypeEnum::MERCHANT)->exists()) {
                $user = User::where('email', $request->email)->first();
                $user->merchant_name = $user->getMerchantInfo->name;
                if (!empty($user->photo)) $image = asset('/uploads/merchant' . '/' . $user->merchant_id . '/user/' . $user->photo);
                return response()->json(array('error' => false, 'user_id' => $user->id, 'role_user_name' => $user->merchant_name, 'store_id' => $user->store_id, 'user_role' => $user->role_id, 'merchant_id' => $user->merchant_id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
            } else {
                if (!DB::table('users')->where('email', $request->email)->exists()) {
                    $merchant = new Merchant();
                    $merchant->name = $request->input('first_name');
                    $merchant->merchant_code = 'b' . $merchant->id;
                    $merchant->save();

                    $user = new User();
                    $user->username = $request->first_name . ' ' . $request->last_name;
                    $user->email = $request->email;
                    if (!empty($request->facebook_link)) $user->facebook_link = $request->facebook_link;
                    if (!empty($request->facebook_id)) $user->facebook_id = $request->facebook_id;
                    $user->user_type = UserTypeEnum::MERCHANT;
                    $user->role_id = RoleEnum::ADMIN;
                    $user->merchant_id = $merchant->id;
                    $user->store_id = 0;
                    $user->save();
                    if (!empty($user->photo)) $image = asset('/uploads/merchant' . '/' . $user->merchant_id . '/user/' . $user->photo);
                    $user->merchant_name = $user->getMerchantInfo->name;
                    return response()->json(array('error' => false, 'user_id' => $user->id, 'role_user_name' => $user->merchant_name, 'store_id' => $user->store_id, 'user_role' => $user->role_id, 'merchant_id' => $user->merchant_id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
                } else {
                    return response()->json(array('error' => true, 'error_msg' => 'You Are Not Authorised'));
                }
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function merchantGooglePlusLogin(Request $request)
    {
        if (!empty($request->first_name) && !empty($request->last_name) && !empty($request->email)) {

            $image = '';
            if (DB::table('users')->where('email', $request->email)->where('google_plus_id', $request->google_id)->where('user_type', 2)->exists()) {
                $user = User::where('email', $request->email)->first();

                if (!empty($user->photo)) $image = asset('/uploads/merchant' . '/' . $user->merchant_id . '/user/' . $user->photo);

                $user->merchant_name = $user->getMerchantInfo->name;
                return response()->json(array('error' => false, 'user_id' => $user->id, 'role_user_name' => $user->merchant_name, 'store_id' => $user->store_id, 'user_role' => $user->role_id, 'merchant_id' => $user->merchant_id, 'email' => $user->email, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
            } else {
                if (!DB::table('users')->where('email', $request->email)->exists()) {


                    $merchant = new Merchant();
                    $merchant->name = $request->input('first_name');
                    $merchant->merchant_code = 'd' . $merchant->id;
                    $merchant->save();

                    $user = new User();
                    $user->username = $request->first_name . ' ' . $request->last_name;
                    $user->email = $request->email;
                    if (!empty($request->google_id)) $user->google_plus_id = $request->google_id;
                    $user->user_type = UserTypeEnum::MERCHANT;
                    $user->role_id = RoleEnum::ADMIN;
                    $user->merchant_id = $merchant->id;
                    $user->store_id = 0;
                    $user->save();

                    if (!empty($user->photo)) $image = asset('/uploads/merchant' . '/' . $user->merchant_id . '/user/' . $user->photo);

                    $user->merchant_name = $user->getMerchantInfo->name;
                    return response()->json(array('error' => false, 'user_id' => $user->id, 'store_id' => $user->store_id, 'role_user_name' => $user->merchant_name, 'user_role' => $user->role_id, 'email' => $user->email, 'merchant_id' => $user->merchant_id, 'name' => $user->username, 'phone_number' => $user->phone, 'image' => $image));
                } else {

                    return response()->json(array('error' => true, 'error_msg' => 'You Are Not Authorised'));
                }
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

    public function getMerchantProfile($user_id)
    {

    }

    public function updateMerchantProfile(Request $request)
    {
        if (!empty($request->user_id)) {

            $user = User::find($request->user_id);

            if (isset($user)) {
                $image = '';
                $user->username = $request->user_name;
                $user->email = $request->profile_email;
                if (!empty($request->profile_image)) {
                    $path = 'uploads/merchant/' . $user->merchant_id . '/user/';
                    $user->photo = $this->getFileUpload($path, $user->photo, $request->profile_image);;
                }
                if (!empty($request->password)) $user->password = bcrypt($request->password);
                $user->phone = $request->phone_number;
                $user->save();

                if (!empty($user->photo)) $image = asset('uploads/merchant/' . $user->merchant_id . '/user/' . $user->photo);

                return response()->json(array('error' => false, 'image' => $image));
            } else {
                return response()->json(array('error' => true, 'error_msg' => 'User Not Valid'));
            }
        } else {
            return response()->json(array('error' => true, 'error_msg' => 'Invalid Input'));
        }
    }

}
