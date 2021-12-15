<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Enum\AdvertisementTypeEnum;
use App\Http\Controllers\Enum\CouponDiscountTypeEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Model\AdminPermission;
use App\Model\Advertisement;
use App\Model\City;
use App\Model\Location;
use App\Model\Notification;
use App\Model\PushNotification;
use App\Model\PushNotificationReceiver;
use App\Model\Question;
use App\Model\QuestionAnswer;
use App\Model\Seller;
use App\Model\Setting;
use App\Model\CompanyBusinessCountry;
use App\Model\CompanyOfficeLocation;
use App\Model\Coupon;
use App\Model\Department;
use App\Model\Designation;
use App\Model\Employee;
use App\Model\EmployeeType;
use App\Model\Holiday;
use App\Model\LeaveEntitlement;
use App\Model\LeaveType;
use App\Model\News;
use App\Model\OfficeLocation;
use App\Model\ProductCategory;
use App\Model\Provider;
use App\Model\Role;
use App\Model\Shipping;
use App\Model\StaticPage;
use App\Model\SuccessStory;
use App\Model\WorkWeek;
use App\Model\WorldCountry;
use App\Model\Country;
use App\User;
use Folklore\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Crypt;
use Cookie;
use Input;
use File;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    // City
    public function settingList()
    {
        $settings = Setting::all();
        return view('admin/settings/list')
            ->with('settings', $settings);
    }

    public function settingSave(Request $request)
    {
        try {
            $setting = new Setting();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $setting = Setting::findOrFail($_GET['edit_id']);
                }
                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Name</label>
                                <input required="" type="text" class="form-control" name="key" readonly="readonly"  value="' . $setting->key . '"></div></div>';
                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Value</label>
                                <input required="" type="text" class="form-control" name="value"  value="' . $setting->value . '"></div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $setting->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $setting_id = $request->input('edit_id');

                if (isset($setting_id)) {
                    try {
                        $setting = Setting::find($setting_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($setting)) {
                    $setting->value = $request->value;
                    $setting->save();
                }

                if (isset($setting_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }




    // Category
    public function categoryProductList()
    {
        $categories = ProductCategory::orderBy('name', 'asc')
            ->where('product_category_level_no', 1)
            ->whereNull('parent_category_id')
            ->where('product_category_type_id', 1)
            ->get();

        return view('/admin/settings/category/product/list')
            ->with('categories', $categories);
    }

    public function categoryServiceList()
    {
        $categories = ProductCategory::orderBy('name', 'asc')
            ->where('product_category_level_no', 1)
            ->whereNull('parent_category_id')
            ->where('product_category_type_id', 2)
            ->get();

        return view('/admin/settings/category/service/list')
            ->with('categories', $categories);

    }

    public function categorySave(Request $request)
    {
        try {
            $category = new ProductCategory();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                $parent_categories = ProductCategory::orderBy('name', 'asc')->where('product_category_level_no', 1)->whereNull('parent_category_id');
                if (isset($_GET['category_type']) && $_GET['category_type'] == 'product') {
                    $parent_categories = $parent_categories->where('product_category_type_id', 1);
                }
                if (isset($_GET['category_type']) && $_GET['category_type'] == 'service') $parent_categories = $parent_categories->where('product_category_type_id', 2);
                $parent_categories = $parent_categories->get();

                if (isset($_GET['edit_id']))
                    $category = ProductCategory::where('id', $_GET['edit_id'])->first();

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="required">Name</label>
                                    <input required="" type="text" class="form-control" name="name" maxlength="60" value="' . $category->name . '"></div>
                            </div>';

                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="required">Arabic Name</label>
                                    <input required="" type="text" class="form-control" name="ar_name"  maxlength="60" value="' . $category->ar_name . '"></div>
                            </div>';
                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="required">Image</label>
                                    <input type="file" accept="image/*" class="form-control" name="image" value="' . $category->image . '"></div>
                            </div>';

                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="required">Banner Image</label>
                                    <input  type="file" accept="image/*" class="form-control" name="banner_image" value="' . $category->banner_image . '"></div>
                            </div>';

                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="required">Image For App</label>
                                    <input  type="file" accept="image/*" class="form-control" name="app_image" value="' . $category->image_for_app . '"></div>
                            </div>';
                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="required">Parent Category</label>';
                $data_generate .= '<select name="parent_category_id" class="form-control">';
                $data_generate .= '<option value=""> -- Select --</option>';

                foreach ($parent_categories as $parent) {
                    $first_child = ProductCategory::orderBy('name', 'asc')->where('parent_category_id', $parent->id)->get();
                    $data_generate .= '<option ';
                    if ($category->parent_category_id == $parent->id) $data_generate .= ' selected ';
                    $data_generate .= 'value="' . $parent->id . '">' . $parent->name . '</option>';
                    foreach ($first_child as $first) {
                        $data_generate .= '<option ';
                        if ($category->parent_category_id == $first->id) $data_generate .= ' selected ';
                        $data_generate .= 'value="' . $first->id . '">&nbsp- - ' . $first->name . '</option>';
                    }
                }
                $data_generate .= '</select>';
                $data_generate .= '</div></div>';

                $data_generate .= '<div class="clearfix"></div>';


                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" id="edit_id" class="form-control" name="edit_id" value="' . Crypt::encrypt($category->id) . '">';


                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $encrypted_id = $request->input('edit_id');

                if (isset($encrypted_id)) {
                    try {
                        $category = ProductCategory::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($category)) {
                    DB::beginTransaction();

                    $category->name = $request->name;
                    $category->ar_name = $request->ar_name;
                    if (!empty($request->product_category_type_id)) $category->product_category_type_id = $request->product_category_type_id;
                    $category->parent_category_id = $request->parent_category_id;
                    $deal_type_level = ProductCategory::find($request->parent_category_id);
                    if (!empty($request->parent_category_id)) $category->product_category_level_no = $deal_type_level->product_category_level_no + 1;

                    $path = env('CATEGORY_PHOTO_PATH');

                    if (!file_exists($path)) {
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }
                    //now upload the photo
                    $image = $request->file("image");

                    if (isset($image)) {

                        if (isset($category->image) && $category->image != "" && file_exists($path . '/' . $category->image)) {
                            unlink($path . '/' . $category->image);
                        }
                        $fileName = time() . $image->getClientOriginalName();
                        $image->move($path . '/', $fileName);
                        $category->image = $fileName;
                    }

                    $app_image = $request->file("app_image");
                    if (isset($app_image)) {

                        if (isset($category->image_for_app) && $category->image_for_app != "" && file_exists($path . '/' . $category->image_for_app)) {
                            unlink($path . '/' . $category->image_for_app);
                        }
                        $fileName = time() . $app_image->getClientOriginalName();
                        $app_image->move($path . '/', $fileName);
                        $category->image_for_app = $fileName;
                    }

                    $banner_image = $request->file("banner_image");
                    if (isset($banner_image)) {

                        if (isset($category->banner_image) && $category->banner_image != "" && file_exists($path . '/' . $category->banner_image)) {
                            unlink($path . '/' . $category->banner_image);
                        }
                        $fileName = time() . $banner_image->getClientOriginalName();
                        $banner_image->move($path . '/', $fileName);
                        $category->banner_image = $fileName;
                    }

                    $category->save();


                    DB::commit();
                }

                if (isset($encrypted_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
//            dd($e);
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }

    }

    public function showInPublicMenu(Request $request)
    {

        if (!empty($request->category_id)) {
            $product_category = ProductCategory::find($request->category_id);
            if ($product_category->show_in_public_menu == 1) {
                $product_category->show_in_public_menu = 0;
            } elseif ($product_category->show_in_public_menu == 0) {
                $product_category->show_in_public_menu = 1;
            }
            $product_category->save();
        }
        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
    }

    public function categoryDelete($id)
    {
        try {
            if (((int)$id) > 0) {

                $product_category = ProductCategory::find($id);
                $path =
                    '/uploads/category';
                if (isset($product_category->image) && $product_category->image != "" && file_exists($path . '/' . $product_category->image)) {
                    unlink($path . '/' . $product_category->image);
                }
                if (isset($product_category->image_for_app) && $product_category->image_for_app != "" && file_exists($path . '/' . $product_category->image_for_app)) {
                    unlink($path . '/' . $product_category->image_for_app);
                }
                $product_category->delete();
            } else
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // City
    public function cityList()
    {
        $country = 'SA';
        if(!empty($_GET['country'])) $country = $_GET['country'];

        $cities = City::join('countries','countries.id','=','cities.country_id')
            ->select('cities.*')
            ->OrderBy('countries.name', 'asc')
            ->OrderBy('cities.name', 'asc');
       // $cities = $cities->where('country_id',$country);
        $cities = $cities->paginate(100);

        return view('admin/settings/city/list')
            ->with('country', $country)
            ->with('cities', $cities);
    }

    public function citySave(Request $request)
    {
        try {
            $city = new City();
            $countries = Country::orderBy('name')->get();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $city = City::findOrFail($_GET['edit_id']);
                }
                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                 $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Name</label>
                 <select name="country_id" id="" required class="form-control"><option value="">Select</option>';
                 foreach($countries as $country){
                    $data_generate .= '<option value="'.$country->id.'"';
                    if($city->country_id == $country->id) $data_generate .= ' selected '; 
                    $data_generate .= '>'.$country->name.'</option>';
                 }
                $data_generate .= '</select></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Name</label>
                                <input required="" type="text" class="form-control" name="name"  value="' . $city->name . '"></div></div>';
                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Arabic Name</label>
                                <input required="" type="text" class="form-control" name="ar_name"  value="' . $city->ar_name . '"></div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $city->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $city_id = $request->input('edit_id');

                if (isset($city_id)) {
                    try {
                        $city = City::find($city_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($city)) {
                    $city->country_id = $request->country_id;
                    $city->name = $request->name;
                    $city->ar_name = $request->ar_name;
                    $city->save();
                }

                if (isset($city_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function cityDelete($id)
    {
        // remove from shipping city id and seller and buyer city null
        if (((int)$id) > 0) {
            $city = City::find($id);

            DB::beginTransaction();
            $shippings = Shipping::where('city_ids','LIKE','%'.$id.'%')->get();

            foreach($shippings as $shipping){
                $city_ids = explode(',',$shipping->city_ids);

                while(($i = array_search($id, $city_ids)) !== false) {
                    unset($city_ids[$i]);
                }

                $shipping->city_ids = implode(',', $city_ids);
                $shipping->save();
            }

            DB::table('sellers')->where('city',$id)->update(['city' => null]);
            DB::table('buyers')->where('city',$id)->update(['city' => null]);
            DB::table('orders')->where('delivery_city',$id)->update(['delivery_city' => null]);

            if (isset($city) && isset($city->id)) {
                $city->delete();
            }

            DB::commit();

        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
    }

    // cityStatus
    // shipping status enable = 1; disable = 0;
    public function cityStatus(Request $request){
        try{
            DB::beginTransaction();
            $id = $request->city_id;

            $city = City::where('id',$request->city_id)->first();
            $city->shipping_status = ($city->shipping_status == 0) ? 1 : 0;
            $city->save();

            if($city->shipping_status == 0) {
                $shippings = Shipping::where('city_ids','LIKE','%'.$id.'%')->get();

                foreach($shippings as $shipping){
                    $city_ids = explode(',',$shipping->city_ids);

                    while(($i = array_search($id, $city_ids)) !== false) {
                        unset($city_ids[$i]);
                    }

                    $shipping->city_ids = implode(',', $city_ids);
                    $shipping->save();
                }
            }

            DB::commit();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
        }catch (\Exception $e){
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));
        }
    }

    // Location
    public function locationList()
    {
        $cities = Location::join('cities', 'cities.id', '=', 'locations.city_id')
            ->select(
                'cities.id as city_id'
                , 'cities.name'
                , 'locations.id'
                , 'locations.name as location_name'
                , 'locations.ar_name as location_ar_name'
            )
            ->OrderBy('cities.name', 'asc')
            ->OrderBy('locations.name', 'asc')
            ->paginate(100);

        return view('admin/settings/location/list')
            ->with('cities', $cities);
    }

    public function locationSave(Request $request)
    {
        try {
            $location = new Location();
            $cities = City::OrderBy('name', 'asc')->get();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $location = Location::findOrFail($_GET['edit_id']);
                }
                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Name</label>
                                <input required="" type="text" class="form-control" name="name"  value="' . $location->name . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Arabic Name</label>
                                <input required="" type="text" class="form-control" name="ar_name"  value="' . $location->ar_name . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">City</label>
                                <select  class="form-control" name="city_id" required><option value="">Select</option>';

                if (isset($cities[0])) {
                    foreach ($cities as $city) {
                        $data_generate .= '<option ';
                        if ($location->city_id == $city->id) $data_generate .= ' selected ';
                        $data_generate .= 'value="' . $city->id . '">' . $city->name . ' - ' . ($city->ar_name) . '</option>';
                    }
                }

                $data_generate .= '</div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $location->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $location_id = $request->input('edit_id');

                if (isset($location_id)) {
                    try {
                        $location = Location::find($location_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($location)) {
                    $location->name = $request->name;
                    $location->ar_name = $request->ar_name;
                    $location->city_id = $request->city_id;
                    $location->save();
                }

                if (isset($location_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function locationDelete($id)
    {
        if (((int)$id) > 0) {
            $location = Location::find($id);
            if (isset($location) && isset($location->id)) {
                $location->delete();
            }
        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));


        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
    }

    // staticPageList
    public function staticPageList()
    {
        $static_pages = StaticPage::orderBy('title', 'asc')->get();

        return view('/admin/settings/staticpage/list')->with('static_pages', $static_pages);
    }

    public function staticPageSave(Request $request)
    {
        try {
            $static_page = new StaticPage();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $static_page = StaticPage::findOrFail($_GET['edit_id']);
                }
                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Title</label>
                                <input required="" type="text" class="form-control" name="title"  value="' . $static_page->title . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Arabic Title</label>
                                <input required="" type="text" class="form-control" name="ar_title"  value="' . $static_page->ar_title . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Description</label>
                               <div class="summernote-theme-1"><textarea name="description" class="form-control editor_s" id="" cols="30" rows="10">' . $static_page->description . '</textarea>
                               </div></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Arabic Description</label>
                               <div class="summernote-theme-1"><textarea name="ar_description" class="form-control editor_s" id="" cols="30" rows="10">' . $static_page->ar_description . '</textarea>
                               </div></div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $static_page->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $static_page_id = $request->input('edit_id');

                if (isset($static_page_id)) {
                    try {
                        $static_page = StaticPage::find($static_page_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($static_page)) {
                    $static_page->title = $request->title;
                    $static_page->ar_title = $request->ar_title;
                    $static_page->description = $request->description;
                    $static_page->ar_description = $request->ar_description;
                    $static_page->save();
                }

                if (isset($static_page_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function staticPageDelete($id)
    {
        if (((int)$id) > 0) {
            $static_page = StaticPage::find($id);
            if (isset($static_page) && isset($static_page->id)) {
                $static_page->delete();
            }
        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));


        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
    }

    // Admin User
    public function adminUserList()
    {
        $admin_users = User::where('user_type', UserTypeEnum::ADMIN)->get();

        return view('/admin/settings/adminuser/list')->with('admin_users', $admin_users);
    }

    public function adminUserSave(Request $request)
    {
        try {
            $admin_user = new User();
            $admin_roles = Role::all();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $admin_user = User::findOrFail($_GET['edit_id']);
                }
                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';
                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="required">Title</label>
                                <input required="" type="text" class="form-control" name="username"  value="' . $admin_user->username . '"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="required">Email</label>
                                <input required="" type="email" class="form-control" name="email"  value="' . $admin_user->email . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><span class="label label-success">(use blank to keep the current password)</span><div class="clearfix"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label ';
                if (isset($_GET['add_id'])) {
                    $data_generate .= ' class="required" ';
                }
                $data_generate .= '>Password</label>
                                    <input type="password" name="password" class="form-control"';
                if (isset($_GET['add_id'])) {
                    $data_generate .= ' required ';
                }
                $data_generate .= '>
                               </div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label ';
                if (isset($_GET['add_id'])) {
                    $data_generate .= ' class="required" ';
                }
                $data_generate .= '>Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control"';
                if (isset($_GET['add_id'])) {
                    $data_generate .= ' required ';
                }
                $data_generate .= '>
                               </div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="required">Admin Role</label>
                                <select  class="form-control" name="admin_role_id" required><option value="">Select</option>';

                foreach ($admin_roles as $admin_role) {
                    $data_generate .= '<option ';
                    if ($admin_user->admin_role_id == $admin_role->id) $data_generate .= ' selected ';
                    $data_generate .= 'value="' . $admin_role->id . '">' . $admin_role->name . '</option>';
                }

                $data_generate .= '</select></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="required">User Photo</label>
                                    <input type="file" name="profile_image" class="form-control">
                               </div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $admin_user->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $admin_user_id = $request->input('edit_id');

                if (isset($admin_user_id)) {
                    try {
                        $admin_user = User::find($admin_user_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($admin_user)) {

                    $admin_user->username = $request->username;
                    $admin_user->email = $request->email;
                    if (!empty($request->password)) {
                        if ($request->password == $request->confirm_password) {
                            $admin_user->password = bcrypt($request->password);
                        } else {
                            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . 'Password not match.');
                        }
                    }
                    $admin_user->admin_role_id = $request->admin_role_id;

                    $path = env('USER_PHOTO_PATH');
                    //now upload the photo
                    $image = $request->file('profile_image');
                    if (isset($image)) {
                        $fileName = 'user_' . date('Y-m-d-g-i-a') . $image->getClientOriginalName();
                        $image->move($path . '/', $fileName);
                        $admin_user->photo = $fileName;
                    }

                    $admin_user->save();
                }

                if (isset($admin_user_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // Admin User
    public function adminRoleList()
    {
        $admin_roles = Role::all();
        $adminPermissions = DB::table('admin_permissions')->orderBy('name', 'asc')->get();

        return view('/admin/settings/adminuser/role-list')->with('admin_roles', $admin_roles)->with('adminPermissions', $adminPermissions);
    }

    public function adminRoleSave(Request $request)
    {
        try {
            $admin_roles = new Role();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $admin_roles = Role::find($_GET['edit_id']);
                }
                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';
                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Name</label>
                                <input required="" type="text" class="form-control" name="username"  value="' . $admin_roles->name . '"></div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $admin_roles->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $admin_role_id = $request->input('edit_id');

                if (isset($admin_role_id)) {
                    try {
                        $admin_roles = Role::find($admin_role_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($admin_roles)) {
                    $admin_roles->name = $request->name;
                    $admin_roles->save();
                }

                if (isset($admin_role_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function adminRolePermission(Request $request)
    {
        $roleID = $_GET['role_id'];

        $roleDetails = Role::find($roleID);

        $allPermissions = AdminPermission::orderBy('name', 'asc')->get();


        $assignedPermissions = DB::table('roles')
            ->join('admin_role_permissions', 'roles.id', '=', 'admin_role_permissions.admin_role_id')
            ->join('admin_permissions', 'admin_permissions.id', '=', 'admin_role_permissions.admin_permission_id')
            ->select(
                'admin_permissions.id as permission_id'
                , 'admin_permissions.name as permission_name'
            )
            ->where('roles.id', '=', $roleID)
            ->orWhere('roles.id', '=', null)
            ->orderBy('admin_permissions.name', 'asc')->get();

        $unassignedPermissions = "";
        $pos = 0;
        foreach ($allPermissions as $allPermission) {
            for ($i = 0; $i < count($assignedPermissions); $i++) {
                if ($assignedPermissions[$i]->permission_id == $allPermission->id) break;
            }
            if ($i == count($assignedPermissions)) $unassignedPermissions[$pos++] = $allPermission;
        }

        $returnData[0] = $roleDetails;
        $returnData[1] = $unassignedPermissions;
        $returnData[2] = $assignedPermissions;

        $returnData = json_encode($returnData);


        return response()->json(['rawdata' => "" . $returnData]);
    }

    public function adminRolePermissionSave(Request $request)
    {

        $userRole = Role::find($request->input('id'));
        $userRole->name = $request->input('name');
        $userRole->save();

        $assigned_permissions = $request->input('assigned_permissions');

        $dataSet = [];
        for ($i = 0; $i < count($assigned_permissions); $i++) {
            $dataSet[] = [
                'admin_role_id' => $request->input('id'),
                'admin_permission_id' => $assigned_permissions[$i],
            ];
        }

        DB::table('admin_role_permissions')->insert($dataSet);

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Role Save Successfully.');
    }

    // Coupon
    public function couponList()
    {
        $coupons = Coupon::OrderBy('discount_type', 'asc')->paginate(env('PAGINATION_LARGE'));

        return view('admin/settings/coupon/list')
            ->with('coupons', $coupons);
    }

    public function couponSave(Request $request)
    {
        try {
            $coupon_code = '';
            $coupon = new Coupon();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $coupon = Coupon::findOrFail($_GET['edit_id']);
                    $coupon_code = $coupon->coupon;
                }else{
                    $coupon_code = str_random(6);
                }

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Coupon</label>
                                <input required="" type="text" class="form-control" name="coupon"  value="' . $coupon_code . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Discount</label>
                                <input required="" type="number" step="any" class="form-control" name="discount"  value="' . $coupon->discount . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Discount Type</label>
                                <select  class="form-control" name="discount_type" required><option value="">Select</option>';

                        $data_generate .= '<option '; if($coupon->discount_type == CouponDiscountTypeEnum::FIXED) $data_generate .= ' selected ';
                        $data_generate .= 'value="' . CouponDiscountTypeEnum::FIXED . '">Fixed</option>';
                        $data_generate .= '<option '; if($coupon->discount_type == CouponDiscountTypeEnum::PERCENTAGE) $data_generate .= ' selected ';
                        $data_generate .= 'value="' . CouponDiscountTypeEnum::PERCENTAGE . '">Percentage</option>';

                $data_generate .= '</div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $coupon->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $coupon_id = $request->input('edit_id');

                if (isset($coupon_id)) {
                    try {
                        $coupon = Coupon::find($coupon_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($coupon)) {
                    $coupon->coupon = $request->coupon;
                    $coupon->discount = $request->discount;
                    $coupon->discount_type = $request->discount_type;
                    $coupon->save();
                }

                if (isset($city_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function couponDelete($id)
    {
        if (((int)$id) > 0) {
            $coupon = Coupon::find($id);
            if (isset($coupon) && isset($coupon->id)) {
                $coupon->delete();
            }
        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
    }

    // Advertisement List
    public function advertisementList()
    {
        $advertisements = Advertisement::OrderBy('main_title', 'asc')->paginate(env('PAGINATION_SMALL'));

        return view('admin/settings/advertisement/list')
            ->with('advertisements', $advertisements);
    }

    public function advertisementSave(Request $request)
    {
        try {
            $ad_position = [];
            $advertisement = new Advertisement();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $advertisement = Advertisement::findOrFail($_GET['edit_id']);
                    $ad_position = explode(',',$advertisement->position);
                }

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label>Main Title (English)</label>
                                <input type="text" class="form-control" name="main_title"  value="' . $advertisement->main_title . '"></div></div>';
                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label>Main Title (Arabic)</label>
                                <input type="text" class="form-control" name="ar_main_title"  value="' . $advertisement->ar_main_title . '"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="">Sub Title One (English)</label>
                                <input type="text" class="form-control" name="sub_title_one"  value="' . $advertisement->sub_title_one . '"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="">Sub Title One (Arabic)</label>
                                <input type="text" class="form-control" name="ar_sub_title_one"  value="' . $advertisement->ar_sub_title_one . '"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="">Sub Title Two (English)</label>
                                <input type="text" class="form-control" name="sub_title_two"  value="' . $advertisement->sub_title_two . '"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="">Sub Title Two (Arabic)</label>
                                <input type="text" class="form-control" name="ar_sub_title_two"  value="' . $advertisement->ar_sub_title_two . '"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="">Link</label>
                                <input type="text" class="form-control" name="link"  value="' . $advertisement->link . '"></div></div>';

                $data_generate .= '<div class="col-md-6">
                        <div class="form-group">
                        <label for="">Date Range</label>
							<div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control" name="start"';
                                    if(!empty($advertisement->start)){
                                        $data_generate .= 'value="'.date('d-m-Y',strtotime($advertisement->start)).'"';
                                    } else{
                                        $data_generate .= 'value="'.date('d-m-Y').'"';
                                    }
                                    $data_generate .= ';/>
                                <span class="input-group-addon">to</span>
                                <input type="text" class="input-sm form-control" name="end"';
                                    if(!empty($advertisement->start)){
                                        $data_generate .= 'value="'.date('d-m-Y',strtotime($advertisement->end)).'"';
                                    } else{
                                        $data_generate .= 'value="'.date('d-m-Y', strtotime("+1 year")).'"';
                                    }
                                    $data_generate .= ';/>
                            </div></div>
						</div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="required">Image</label>
                                <input type="file" accept="image/*" class="form-control" name="image"  value=""';
                if (isset($_GET['add_id'])) $data_generate .= ' required '; $data_generate .= '></div></div>';


                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Position</label>
                                <select name="position[]" multiple id="position" class="form-control position" required><option value="">Select</option>';

                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::PRODUCT_PAGE_TOP, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::PRODUCT_PAGE_TOP . '">Product Page Top</option>';
                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::PRODUCT_PAGE_MIDDLE, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::PRODUCT_PAGE_MIDDLE . '">Product Page Middle</option>';
                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::SERVICE_PAGE_TOP, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::SERVICE_PAGE_TOP . '">Service Page Top</option>';
                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::SERVICE_PAGE_MIDDLE, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::SERVICE_PAGE_MIDDLE . '">Service Page Middle</option>';
                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::DEAL_PAGE_TOP, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::DEAL_PAGE_TOP . '">Deal Page Top</option>';
                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::DEAL_PAGE_MIDDLE, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::DEAL_PAGE_MIDDLE . '">Deal Page Middle</option>';
                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::HOME_PAGE_TOP, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::HOME_PAGE_TOP . '">Home Page Top</option>';
                $data_generate .= '<option '; if(in_array(AdvertisementTypeEnum::HOME_PAGE_MIDDLE, $ad_position)) $data_generate .= ' selected '; $data_generate .= 'value="' . AdvertisementTypeEnum::HOME_PAGE_MIDDLE . '">Home Page Middle</option>';

                $data_generate .= '</div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $advertisement->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $advertisement_id = $request->input('edit_id');

                if (isset($advertisement_id)) {
                    try {
                        $advertisement = Advertisement::find($advertisement_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($advertisement)) {
                    $advertisement->start = date('Y-m-d',strtotime($request->start));
                    $advertisement->end = date('Y-m-d',strtotime($request->end));
                    $advertisement->main_title = $request->main_title;
                    $advertisement->sub_title_one = $request->sub_title_one;
                    $advertisement->sub_title_two = $request->sub_title_two;

                    $advertisement->ar_main_title = $request->ar_main_title;
                    $advertisement->ar_sub_title_one = $request->ar_sub_title_one;
                    $advertisement->ar_sub_title_two = $request->ar_sub_title_two;

                    $advertisement->link = $request->link;

                    $position = $request->position;
                    $first = 0;
                    $advertisement->position = '';
                    foreach($position as $pos){
                        if($first == 0){
                            $advertisement->position .= $pos;
                            $first = 1;
                        }else{
                            $advertisement->position .= ','.$pos;
                        }
                    }



                    $path = env('ADVERTISEMENT_UPLOAD_PATH');

                    if (!file_exists($path)) {
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }
                    //now upload the photo
                    $image = $request->file("image");

                    if (isset($image)) {

                        if (isset($advertisement->image) && $advertisement->image != "" && file_exists($path . '/' . $advertisement->image)) {
                            unlink($path . '/' . $advertisement->image);
                        }
                        $fileName = time() . $image->getClientOriginalName();
                        $image->move($path . '/', $fileName);
                        $advertisement->image = $fileName;
                    }



                    $advertisement->save();


                }

                if (isset($advertisement_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function advertisementDelete($id)
    {
        if (((int)$id) > 0) {
            $advertisement = Advertisement::find($id);
            $path = 'uploads/advertisement';
            if (isset($advertisement->image) && $advertisement->image != "" && file_exists($path . '/' . $advertisement->image)) {
                unlink($path . '/' . $advertisement->image);
            }
            if (isset($advertisement) && isset($advertisement->id)) {
                $advertisement->delete();
            }
        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
    }

    // Success Story List
    public function successStoryList()
    {
        $success_stories = SuccessStory::OrderBy('created_at', 'desc')->paginate(env('PAGINATION_LARGE'));

        return view('admin/settings/successStory/list')
            ->with('success_stories', $success_stories);
    }

    public function successStorySave(Request $request)
    {
        try {
            $success_story = new SuccessStory();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $success_story = SuccessStory::findOrFail($_GET['edit_id']);
                }

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="">Name</label>
                                <input type="text" class="form-control" name="name"  value="' . $success_story->name . '"></div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group"><label class="">Arabic Name</label>
                                  <input type="text" class="form-control" name="ar_name"  value="' . $success_story->ar_name . '"></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Description</label>
                               <div class="summernote-theme-1"><textarea name="details" class="form-control editor_s" id="" cols="30" rows="10">' . $success_story->details . '</textarea>
                               </div></div></div>';

               $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Arabic Description</label>
                              <div class="summernote-theme-1"><textarea name="ar_details" class="form-control editor_s" id="" cols="30" rows="10">' . $success_story->ar_details . '</textarea>
                              </div></div></div>';

                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="required">Image</label>
                                <input type="file" accept="image/*" class="form-control" name="image"  value=""';
                if (isset($_GET['add_id'])) $data_generate .= ' required '; $data_generate .= '></div></div>';

                $data_generate .= '</div></div>';

                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" class="form-control" name="edit_id" value="' . $success_story->id . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));
            } else {

                $success_story_id = $request->input('edit_id');

                if (isset($success_story_id)) {
                    try {
                        $success_story = SuccessStory::find($success_story_id);
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($success_story)) {
                    $success_story->name = $request->name;
                    $success_story->details = $request->details;
                    $success_story->ar_name = $request->ar_name;
                    $success_story->ar_details = $request->ar_details;

                    $path = env('SUCCESS_STORY_UPLOAD_PATH');

                    if (!file_exists($path)) {
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }
                    //now upload the photo
                    $image = $request->file("image");

                    if (isset($image)) {

                        if (isset($success_story->image) && $success_story->image != "" && file_exists($path . '/' . $success_story->image)) {
                            unlink($path . '/' . $success_story->image);
                        }
                        $fileName = time() . $image->getClientOriginalName();
                        $image->move($path . '/', $fileName);
                        $success_story->image = $fileName;
                    }

                    $success_story->save();


                }

                if (isset($success_story_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function successStoryDelete($id)
    {
        if (((int)$id) > 0) {
            $success_story = SuccessStory::find($id);
            $path = env('SUCCESS_STORY_UPLOAD_PATH');
            if (isset($success_story->image) && $success_story->image != "" && file_exists($path . '/' . $success_story->image)) {
                unlink($path . '/' . $success_story->image);
            }
            if (isset($success_story) && isset($success_story->id)) {
                $success_story->delete();
            }
        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
    }


    //Question
    public function questionList(){
        $questions = Question::orderBy('created_at','desc')->get();

        return view('admin/settings/question/list')->with('questions',$questions);
    }

    public function questionReview(Request $request){
        if (isset($request->question_id)) {
            $question = Question::findOrfail($request->question_id);
            if (isset($request->is_reviewed)) $question->is_reviewed = 1;
            else $question->is_reviewed = 0;
            $question->save();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
        }
    }
    public function questionDetails($question_id){
        $question = Question::find($question_id);
        $question->is_reviewed = 1;
        $question->save();

        $question->getQuestionAnswer()->where('is_viewed', 0)->update(['is_viewed' => 1]);

        return view('admin/settings/question/details')
            ->with('question',$question);
    }
    public function questionReplySave(Request $request){

        try{

            DB::beginTransaction();

            $answer = new QuestionAnswer();
            $answer->answer = $request->answer;
            $answer->user_id = Auth::user()->id;
            $answer->question_id = $request->question_id;
            $answer->is_viewed = 0;
            $answer->save();

            $question_info = $answer->getQuestion()->first();

            if($answer){
                $notification = new PushNotification();
                $notification->notification_by = Auth::user()->id;
                $notification->description = 'You have question reply form admin. <a href="'.url('/buyer/question/details/'.$question_info->id).'">Click Here</a>';
                $notification->notification_repeat = PushNotificationRepeatEnum::ONCE;
                $notification->save();

                if (isset($notification)) {
                    $push_notification_receiver = new PushNotificationReceiver();
                    $push_notification_receiver->push_notification_id = $notification->id;
                    $push_notification_receiver->is_viewed = 0;
                    $push_notification_receiver->receiver_id = $question_info->getUser->id;
                    if ($question_info->getUser->user_type == UserTypeEnum::SELLER) {
                        $push_notification_receiver->receiver_type = PushNotificationRepeatTypeEnum::SELLER;
                    } elseif ($question_info->getUser->user_type == UserTypeEnum::USER) {
                        $push_notification_receiver->receiver_type = PushNotificationRepeatTypeEnum::BUYER;
                    }
                    $push_notification_receiver->save();
                }
            }

            DB::commit();
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function questionDelete($id){
        try{
            DB::beginTransaction();
            $question = Question::find($id);
            $question->getQuestionAnswer()->delete();
            $question->delete();

            DB::commit();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        }catch(\Exception $e){
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }
}
