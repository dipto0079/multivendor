<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Enum\CouponDiscountTypeEnum;
use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\ProductTypeEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatEnum;
use App\Http\Controllers\Enum\PushNotificationRepeatTypeEnum;
use App\Http\Controllers\Enum\SellerStatusEnum;
use App\Http\Controllers\Enum\ShippingTypeEnum;
use App\Http\Controllers\Enum\StaticPageEnum;
use App\Http\Controllers\Enum\UserTypeEnum;
use App\Http\Controllers\Enum\DiscountTypeEnum;
use App\Http\Controllers\Enum\ProductStatusEnum;
use App\Model\City;
use App\Model\Country;
use App\Model\Coupon;
use App\Model\Location;
use App\Model\Media;
use App\Model\Order;
use App\Model\Deal;
use App\Model\OrderItem;
use App\Model\Product;
use App\Model\ProductCategory;
use App\Model\PushNotification;
use App\Model\PushNotificationReceiver;
use App\Model\Seller;
use App\Model\StaticPage;
use App\Model\Store;
use App\Model\SubOrder;
use App\Model\Subscription;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Crypt;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Mail;
use Illuminate\Support\Facades\Input;
use App\Model\Setting;
use App\Http\Controllers\Enum\SettingsEnum;
use App\Http\Controllers\Enum\DealStatusEnum;
use Image;

class ProductSellerController extends Controller
{
    public function productSellerList(Request $request)
    {
        $search = '';
        $status = SellerStatusEnum::APPROVED;
        if ($request->search != null) $search = $request->search;
        if ($request->status != null) $status = $request->status;

        $sellers = Seller::join('product_categories', 'product_categories.id', '=', 'sellers.category_id')
            ->join('users', 'users.id', '=', 'sellers.user_id')->orderby('company_name', 'asc')
            ->select('sellers.id', 'sellers.category_id', 'sellers.company_name', 'sellers.store_name', 'sellers.commission', 'sellers.status', 'sellers.created_at');

        if (!empty($search)) {
            $sellers = $sellers->where(function ($q) use ($search) {
                $q->where('users.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('product_categories.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('sellers.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('sellers.store_name', 'LIKE', '%' . $search . '%');
            });
        }

        $sellers = $sellers->where('business_type', ProductTypeEnum::PRODUCT)
            ->where('sellers.status', $status)->paginate(env('PAGE_PAGINATE'));


        return view('/admin/seller/product/list')
            ->with('search', $search)
            ->with('status', $status)
            ->with('sellers', $sellers);
    }

    public function productSellerSave(Request $request)
    {
        try {
            $seller = new Seller();

            $countries = Country::join('cities','cities.country_id','=','countries.id')
                ->select('countries.*')
                ->groupby('countries.id')
                ->orderBy('countries.name', 'asc')
                ->get();


            $product_categories = ProductCategory::where('product_category_level_no', 1)->where('product_category_type_id', 1)->get();

            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                if (isset($_GET['edit_id'])) {
                    $seller = Seller::where('id', $_GET['edit_id'])->first();
                    $cities = City::where('country_id', $seller->country)->where('shipping_status', 1)->get();
                }


                $data_generate = '';

                $data_generate .= '

                      <section class="tabs-section">
				<div class="tabs-section-nav tabs-section-nav-icons">
					<div class="tbl">
						<ul class="nav" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										Basic Information
									</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										About Product Seller
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div><!--.tabs-section-nav-->

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1">
';

                $data_generate .= '<div class="form-group"><div class="row">';


                if (isset($_GET['add_id'])) {
                    $data_generate .= '<div class="col-sm-6" id="product_category"';
                    $data_generate .= '><div class="form-group"><label class="required">Product Category</label>';
                    $data_generate .= '<select name="product_seller_category" class="form-control" required >';
                    $data_generate .= '<option value=""> -- Select --</option>';

                    foreach ($product_categories as $parent) {
                        $data_generate .= '<option ';
                        if ($seller->category_id == $parent->id) $data_generate .= ' selected ';
                        $data_generate .= 'value="' . $parent->id . '">' . $parent->name . ' </option>';
                    }
                    $data_generate .= '</select>';
                    $data_generate .= '</div></div>';
                }

                $data_generate .= '<div class="col-sm-4">
                                <div class="form-group">
                                    <label class="">Store Name</label>
                                    <input required type="text" maxlength=""  class="form-control" name="store_name" id="store_name" placeholder="" value="' . $seller->store_name . '">
                                    <div class="store_name_msg" style="color: #ff0000;"></div>
                                </div>
                            </div>';

                $commission = Setting::getValueByKey(SettingsEnum::PRODUCT_SELLER_COMMISSION);
                if (!empty($seller->commission)) $commission = $seller->commission;

                $data_generate .= '<div class="col-sm-2">
                                <div class="form-group">
                                    <label class="">Commission</label>
                                    <input required type="number" step="any" class="form-control" name="commission" placeholder="" value="' . $commission . '">
                                </div>
                            </div>';

                $data_generate .= '<div class="clearfix"></div><hr>';

//                $data_generate .= '<div class="col-md-12"><h4>Business Information</h4></div>';

                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                        <label class="required">Name of Seller Business</label>
                                        <input type="text" class="form-control" name="company_name" placeholder="" value="' . $seller->company_name . '" required></div>
                                </div>';
                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="required">Business Email</label>
                                    <input type="email" class="form-control" name="business_email" id="business_email" placeholder="" value="' . $seller->business_email . '" autocomplete="off" required>
                                    <div class="business_email_msg" style="color: #ff0000;"></div>
                                    </div></div>';
                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="required">Business Address</label>
                                    <input type="text" class="form-control" name="business_address" placeholder="" value="' . $seller->business_address . '" required></div>
                                </div>';
                $data_generate .= '<div class="col-sm-6">
                                <div class="form-group">
                                    <label class="">Business Website</label>
                                    <input type="url" class="form-control" name="website" placeholder="" value="' . $seller->website . '"></div>
                                </div>';


                $data_generate .= '<div class="clearfix"></div><hr>';

//                $data_generate .= '<div class="col-md-12"><h4>Contact Information</h4></div>';

                $data_generate .= '<div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">Full Name</label>
                                    <input type="text" class="form-control" name="name" placeholder="" required maxlength="60"';
                if (!empty($seller->getUser)) $data_generate .= 'value="' . $seller->getUser->username . '"';
                $data_generate .= '></div>
                            </div>';

                $data_generate .= '<div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">Phone</label>
                                    <input type="text" class="form-control" name="phone" required placeholder=""';
                if (!empty($seller->getUser)) $data_generate .= 'value="' . $seller->getUser->phone . '"';
                $data_generate .= '></div>
                            </div>';


                $data_generate .= '<div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">User Email</label>
                                    <input  type="email" class="form-control" name="email" id="email_id" required placeholder=""';
                if (!empty($seller->getUser)) $data_generate .= 'value="' . $seller->getUser->email . '"';
                $data_generate .= ' autocomplete="off"><div class="email_id_msg" style="color: #ff0000;"></div></div>
                            </div>';

                $data_generate .= '<div class="col-sm-4"><div class="form-group"><label class="required">Country</label>
                                <select  class="form-control" name="country" id="country_id" required><option value="">Select</option>';

                if (isset($countries[0])) {
                    foreach ($countries as $country) {
                        $data_generate .= '<option ';
                        if ($seller->country == $country->id) $data_generate .= ' selected ';
                        $data_generate .= 'value="' . $country->id . '">' . $country->name . '</option>';
                    }
                }

                $data_generate .= '</select></div></div>';

                $data_generate .= '<div class="col-sm-4">
                            <div class="form-group">
                                <label class="required">City</label>
                                <select required name="city" class="form-control" id="city_id"><option value="">Select City</option>';
                if (isset($_GET['edit_id'])) {
                    if (isset($cities[0])) {
                        foreach ($cities as $city) {
                            $data_generate .= '<option value="' . $city->id . '" ';
                            if ($seller->city == $city->id) $data_generate .= ' selected ';
                            $data_generate .= '>' . $city->name . '</option>';
                        }
                    }
                }
                $data_generate .= '</select></div></div>';

                $data_generate .= '<div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">Street</label>
                                    <input  type="text" class="form-control" name="street" required placeholder="" value="' . $seller->street . '"></div>
                            </div><div class="clearfix"></div>';

                $data_generate .= '<div class="col-sm-4">
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" class="form-control" name="state" placeholder="" value="' . $seller->state . '"></div>
                        </div>';

                $data_generate .= '<div class="col-sm-4">
                            <div class="form-group">
                                <label class="">Zip</label>
                                <input type="text" class="form-control" name="zip"  placeholder="" value="' . $seller->street . '"></div>
                        </div>';

                $data_generate .= '<div class="clearfix"></div><hr>';


                $data_generate .= '<div class="col-sm-4">
                                <label for="">Photo</label>
                                <div class="image_files">
                                    <input type="file" name="photo" class="form-control">
                                </div>
                            </div>';
                $data_generate .= '<div class="col-sm-4">
                                <div class="form-group">
                                    <label class="">Password</label>
                                    <input  type="password" class="form-control" name="password" autocomplete="off" placeholder=""></div>
                            </div>';


                $data_generate .= '</div></div></div>';

                $data_generate .= '<div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2">';
                $data_generate .= '<div class="row"></divclass><div class="col-sm-12">
                                <div class="form-group">
                                    <label class="">About Product Seller</label>
                                    <textarea name="about_me" class="form-control" rows="25">' . $seller->about_me . '</textarea>
                                </div>
                                </div>
                            </div>';

                $data_generate .= '<div class="clearfix"></div></div></div></section>';


                $data_generate .= '<div class="clearfix"></div>';


                if (!isset($_GET['add_id']))
                    $data_generate .= '<div id="edit_id_div"><input type="hidden" id="edit_id" class="form-control" name="edit_id" value="' . Crypt::encrypt($seller->id) . '"></div>';


                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $encrypted_id = $request->input('edit_id');

                if (isset($encrypted_id)) {
                    try {
                        $seller = Seller::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($seller)) {
                    DB::beginTransaction();
                    if (!isset($encrypted_id))
                        $user = new User();
                    else
                        $user = User::where('id', $seller->user_id)->first();

                    $user->username = $request->name;
                    $user->email = $request->email;

                    if (!empty($request->password))
                        $user->password = bcrypt($request->password);

                    $user->user_type = UserTypeEnum::SELLER;
                    $user->phone = $request->phone;
                    $photo = $request->photo;

                    if (!empty($photo)) {
                        $destinationPath = env('USER_PHOTO_PATH');

                        if (!empty($user->photo) && file_exists($destinationPath . '/' . $user->photo))
                            unlink($destinationPath . $user->photo);
                        //Move Uploaded File
                        $name = 'user_' . date('d-m-Y-G-i-s') . '.png';
                        $photo->move($destinationPath, $name);
                        $user->photo = $name;
                    }

                    $user->save();
                    $user_last_id = $user->id;

                    $seller->user_id = $user_last_id;

                    if (!isset($encrypted_id)) {
                        $seller->status = SellerStatusEnum::APPROVED;
                        $seller->business_type = ProductTypeEnum::PRODUCT;
                        $seller->category_id = $request->product_seller_category;
                    }

                    $seller->about_me = $request->about_me;
                    $seller->company_name = $request->company_name;
                    $seller->business_email = $request->business_email;
                    $seller->business_address = $request->business_address;

                    $seller->contact_details = $request->phone;
                    $seller->street = $request->street;
                    $seller->city = $request->city;
                    $seller->state = $request->state;
                    $seller->zip = $request->zip;
                    $seller->country = $request->country;
                    $seller->website = $request->website;
                    $seller->store_name = $request->store_name;
                    $seller->commission = ($request->commission == null) ? 0 : $request->commission;
                    $seller->save();

                    if (!isset($encrypted_id)) {
                        $data = [];

                        Mail::send('emails.userRegistrationConfirmation', $data, function ($message) use ($user) {
                            $message->to($user->email, $user->name)->subject('Confirm Your Email');
                        });
                    }

                    DB::commit();
                }

                if (isset($encrypted_id))
                    return redirect('/admin/product/seller?status=' . $seller->status)->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect('/admin/product/seller?status=' . $seller->status)->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }


    public function productSellerDelete($id)
    {
        try {
            $seller = Seller::where('id', $id)->first();

            $seller_products = $seller->getProducts;
            DB::beginTransaction();

            foreach ($seller_products as $seller_product) {
                //$medias = $seller_product->getMedia;
                // foreach($medias as $media){
                //     if (!empty($media->file_in_disk) && file_exists(env('MEDIA_PHOTO_PATH') . $media->file_in_disk)) {
                //         unlink(env('MEDIA_PHOTO_PATH') . $media->file_in_disk);
                //     }
                //     $media->delete();
                // }archive

                $seller_product->status = ProductStatusEnum::ARCHIVE;
                $seller_product->save();
            }

            // if (!empty($seller->getUser->photo) && file_exists(env('USER_PHOTO_PATH') . $seller->getUser->photo)) {
            //     unlink(env('USER_PHOTO_PATH') . $seller->getUser->photo);
            // }
            //User::where('id', $seller->user_id)->delete();
            $seller->status = SellerStatusEnum::ARCHIVE;
            $seller->save();

            DB::commit();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }


    public function productSellerStatusChange(Request $request)
    {
        $password = '';

        $seller = Seller::find($request->seller_id);
        $current_status = $seller->status;
        $seller->status = $request->status;
        $seller->save();

        if ($seller->status == SellerStatusEnum::APPROVED) {
            $user = User::find($seller->user_id);
            if (empty($user->password)) {
                $password = User::generateStrongPassword(8);
                $user->password = bcrypt($password);
                $user->save();
            }
        }

        $email = $seller->getUser->email;
        $name = $seller->getUser->username;
        $status = $seller->status;

        $data = [
            'name' => $name,
            'email' => $email,
            'status' => $status,
            'password' => $password,
        ];

        if ($seller->status != SellerStatusEnum::PENDING) {
            Mail::send('emails.sellerStatusChange', $data, function ($message) use ($email) {
                $message->to($email)->subject('Confirmation Message');
            });
        }

        if ($seller->business_type == ProductTypeEnum::PRODUCT) {
            $url = '/admin/product/seller?status=' . $current_status;
        } elseif ($seller->business_type == ProductTypeEnum::SERVICE) {
            $url = '/admin/service/seller?status=' . $current_status;
        }

        return redirect($url)->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Updated Successfully.');
    }

    public function productSellerDetails($seller_id)
    {
        $seller = Seller::find($seller_id);

        return view('/admin/seller/product/details')
            ->with('seller_id', $seller_id)
            ->with('seller', $seller);
    }

    public function productSellerProductList(Request $request, $seller_id)
    {
        $search = '';
        if (!empty($request->search)) $search = $request->search;

        $products = Product::orderBy('created_at', 'desc')
            ->where('seller_id', $seller_id)->where('status', ProductStatusEnum::SHOWN);

        if (!empty($search)) {
            $products = $products->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('ar_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('ar_description', 'LIKE', '%' . $search . '%')
                    ->orWhere('price', 'LIKE', '%' . $search . '%');
            });
        }
        $products = $products->paginate(env('PAGE_PAGINATE'));


        $seller = Seller::find($seller_id);
        $user_id = $seller->user_id;

        return view('/admin/seller/product/product/list')
            ->with('user_id', $user_id)
            ->with('seller_id', $seller_id)
            ->with('search', $search)
            ->with('products', $products);
    }

    public function productSellerProductSave(Request $request)
    {
        try {
            $product_selected_categories = [];
            $product = new Product();
            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {
                $seller = Seller::find(Crypt::decrypt($_GET['seller_id']));

                $categories = ProductCategory::OrderBy('name', 'asc')
                    ->where('parent_category_id', $seller->category_id)
                    ->where('product_category_type_id', ProductTypeEnum::PRODUCT)
                    ->get();


                if (isset($_GET['edit_id'])) {
                    $product = Product::where('id', $_GET['edit_id'])->first();
                }

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';


                $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                    <label class="required">Product Title(English)</label>
                                    <input required="" type="text" class="form-control" name="name" value="' . $product->name . '">';
                $data_generate .= '</div></div>';

                $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                    <label class="">Product Title(Arabic)</label>
                                    <input required="" type="text" class="form-control" name="ar_name" value="' . $product->ar_name . '">';
                $data_generate .= '</div></div>';

                $data_generate .= '<div class="col-sm-3"><div class="form-group">
                                    <label class="">Price</label>
                                    <input required="" type="number" class="form-control numaric" step="any" min="0" name="price" value="' . $product->price . '">';
                $data_generate .= '</div></div>';

                $data_generate .= '<div class="col-sm-3"><div class="form-group">
                                    <label class="">Payment Required</label>
                                    <select required="" name="payment_required" class="form-control" >';
                $data_generate .= '<option value="0">No Initial Payment</option>';
                $data_generate .= '<option value="1">10%</option>';
                $data_generate .= '<option value="2">20%</option>';
                $data_generate .= '<option value="3">30%</option>';
                $data_generate .= '<option value="4">40%</option>';
                $data_generate .= '<option value="5">50%</option>';
                $data_generate .= '<option value="6">60%</option>';
                $data_generate .= '<option value="7">70%</option>';
                $data_generate .= '<option value="8">80%</option>';
                $data_generate .= '<option value="9">90%</option>';
                $data_generate .= '<option selected value="10">100%</option>';
                $data_generate .= '</select></div></div>';

                $data_generate .= '<div class="col-sm-3"><label class="">Quantity</label><div class="form-group">
                  <input id="quantity" type="text" value="' . $product->quantity . '" name="quantity">
                </div></div>';

                $data_generate .= '<div class="col-sm-3"><label class="">Weight</label><div class="form-group">
                  <input type="number" min="1" class="form-control" value="' . $product->unit . '" name="unit">
                </div></div>';

                $data_generate .= '<div class="col-sm-12" id="product_type"><div class="form-group">
                            <label class="">Product Category</label>
                            <select name="product_type_id" id="product_type_id" class="form-control category_type" required>';
                $data_generate .= '<option value="">Select Category</option>';
                foreach ($categories as $category) {
                    $data_generate .= '<optgroup label="' . $category->name . '">';
                    $second_categories = $category->getSubCategory;
                    foreach ($second_categories as $second_category) {
                        $data_generate .= '<option value="' . $second_category->id . '"';
                        if ($product->category_id == $second_category->id) $data_generate .= ' selected ';
                        $data_generate .= '>' . $second_category->name . '</option>';
                    }
                    $data_generate .= '</optgroup>';
                }

                $data_generate .= '</select></div></div>';


                $data_generate .= '<div class="col-sm-12">
                                <div class="form-group">
                                    <label class="">Description(English)</label>
                                    <textarea name="description" class="form-control maxlength-simple" maxlength="512" rows="6">' . $product->description . '</textarea>
                                </div>
                            </div>';
                $data_generate .= '<div class="col-sm-12">
                                <div class="form-group">
                                    <label class="">Description(Arabic)</label>
                                    <textarea name="ar_description" class="form-control maxlength-simple" maxlength="512" rows="6">' . $product->ar_description . '</textarea>
                                </div>
                            </div>';


                $data_generate .= '<div class="clearfix"></div>';


                if (isset($_GET['add_id'])) $data_generate .= '<input type="hidden" name="seller_id" value="' . $_GET['seller_id'] . '">';
                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" id="edit_id" class="form-control" name="edit_id" value="' . Crypt::encrypt($product->id) . '">';


                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $product_categories = [];
                $encrypted_id = $request->input('edit_id');
                if (!empty($request->seller_id)) $seller_id = Crypt::decrypt($request->seller_id);
                if (isset($encrypted_id)) {
                    try {
                        $product = Product::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }
            }

            if (isset($product)) {
                DB::beginTransaction();

                $product->name = $request->name;
                $product->ar_name = $request->ar_name;
                $product->price = $request->price;
                $product->quantity = (!empty($request->quantity)) ? $request->quantity : 0;
                $product->unit = (!empty($request->unit)) ? $request->unit : 0;
                $product->category_id = $request->product_type_id;
                $product->payment_required = $request->payment_required;
                if (!empty($request->seller_id))
                    $product->seller_id = $seller_id;
                $product->description = $request->description;
                $product->ar_description = $request->ar_description;
//                    $product->product_type_id = $request->category_type_id;

                $product->save();

                DB::commit();
            }

            if (isset($encrypted_id))
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
            else
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }

    }

    public function productSellerProductIsFeatured(Request $request)
    {
        try {
            if (!empty($request->product_id_for_featured)) {
                $product = Product::find($request->product_id_for_featured);
                if ($product->is_featured == 1) {
                    $product->is_featured = 0;
                } elseif ($product->is_featured == 0) {
                    $product->is_featured = 1;
                }
                $product->save();

                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
            } else {
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function productSellerProductIsEditorsChoice(Request $request)
    {
        try {
            if (!empty($request->product_id_for_editor_choice)) {
                $product = Product::find($request->product_id_for_editor_choice);
                if ($product->is_editors_choice == 1) {
                    $product->is_editors_choice = 0;
                } elseif ($product->is_editors_choice == 0) {
                    $product->is_editors_choice = 1;
                }
                $product->save();

                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
            } else {
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function productSellerProductDelete($id)
    {
        try {
            if (((int)$id) > 0) {
                $product = Product::find($id);
                $product->status = ProductStatusEnum::ARCHIVE;
                $product->save();
            } else
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    public function productSellerProductMediaSave(Request $request)
    {
        $product_id = $request->product_id;
        $product_media_image = '';
        if (isset($_GET['edit_id'])) {
            $product_media_image = Media::where('product_id', $_GET['product_id'])->get();
            $data_generate = '';
            if (isset($product_media_image[0])) {
                $data_generate .= '<input type="hidden" name="skip" id="skip" value="1">';
                foreach ($product_media_image as $product_image) {
                    $data_generate .= '<div class="gallery-col media_' . $product_image->id . '">
                                        <article class="gallery-item">
                                            <img class="gallery-picture" src="' . asset(env('MEDIA_PHOTO_PATH') . $product_image->file_in_disk) . '" alt="" height="158">
                                            <div class="gallery-hover-layout">
                                                <div class="gallery-hover-layout-in">
                                                    <p class="gallery-item-title">' . $product_image->title . '</p>
                                                    <div class="btn-group">
                                                        <button data-id="' . $product_image->id . '" type="button" class="delete_image btn">
                                                            <i class="font-icon font-icon-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                        <input class="form-control" type="text" style="margin-top: 5px;" name="image_title[]" value="' . $product_image->title . '">
                                        <input type="hidden" value="' . $product_image->id . '">
                                        <input type="hidden" name="media_id[]" value="' . $product_image->id . '">
                                    </div><!--.gallery-col-->';
                }

            } else {
                $data_generate .= '<span class="text-center text-danger">No Media File Exist!!!</span><input type="hidden" name="skip" id="skip" value="0">';
            }
            return response()->json(['success' => true, 'data_generate' => $data_generate]);
        }

        if ($request->skip == 0) {
            // File upload
            $images = $request->file('photo');
            $path = 'uploads/media';

            if (!file_exists($path)) {
                File::makeDirectory($path, $mode = 0777, true, true);
            }
            if (isset($images)) {
                foreach ($images as $image) {
                    $media = new Media();
                    $media->title = $image->getClientOriginalName();
                    $media->file_name = $image->getClientOriginalName();
                    if (isset($media->file_in_disk) && $media->file_in_disk != "" && file_exists($path . '/' . $media->file_in_disk)) {
                        unlink($path . '/' . $media->file_in_disk);
                    }
                    $fileName = time() . $image->getClientOriginalName();
                    $image->move($path . '/', $fileName);
                    $media->file_in_disk = $fileName;
                    $media->product_id = $product_id;
                    $media->save();
                }
                $data_generate = '';
                if (isset($_GET['add_id'])) {
                    $product_media_image = Media::where('product_id', $product_id)->get();

                    if (isset($product_media_image[0])) {
                        $data_generate .= '<input type="hidden" name="skip" id="skip" value="1">';
                        foreach ($product_media_image as $product_image) {
                            $data_generate .= '<div class="gallery-col media_' . $product_image->id . '">
                                        <article class="gallery-item">
                                            <img class="gallery-picture" src="' . asset('uploads/media/' . $product_image->file_in_disk) . '" alt="" height="150">
                                            <div class="gallery-hover-layout">
                                                <div class="gallery-hover-layout-in">
                                                    <p class="gallery-item-title">' . $product_image->title . '</p>
                                                    <div class="btn-group">
                                                        <button data-id="' . $product_image->id . '" type="button" class="delete_image btn">
                                                            <i class="font-icon font-icon-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                        <input class="form-control" type="text" style="margin-top: 5px;" name="image_title[]" value="' . $product_image->title . '">
                                        <input type="hidden" value="' . $product_image->id . '">
                                        <input type="hidden" name="media_id[]" value="' . $product_image->id . '">
                                    </div><!--.gallery-col-->';

                        }

                    }
                }
                return response()->json(['success' => true, 'data_generate' => $data_generate, 'product_id' => $product_id, 'media_count' => $product_media_image->count()]);
            }
        } else {
            if (!empty($request->media_id)) {
                for ($i = 0; $i < count($request->media_id); $i++) {
                    $media_image = Media::find($request->media_id[$i]);
                    $media_image->title = $request->image_title[$i];
                    $media_image->save();
                }
                return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
            }
        }

    }

    public function productSellerProductMediaDelete()
    {
        try {
            if (isset($_GET['media_image_id'])) $id = $_GET['media_image_id'];
            if (((int)$id) > 0) {

                $media = Media::find($id);
                $product_id = $media->product_id;
                $path = 'uploads/media';
                if (isset($media->file_in_disk) && $media->file_in_disk != "" && file_exists($path . '/' . $media->file_in_disk)) {
                    unlink($path . '/' . $media->file_in_disk);
                }
                $media->delete();

                $product_media_image = Media::where('product_id', $product_id)->get();
            } else
                return response()->json(['success' => true, 'message' => 'Invalid Media']);

            return response()->json(['success' => true, 'message' => env('MSG_DELETED_SUCCESSFULLY'), 'product_id' => $product_id, 'media_count' => $product_media_image->count()]);
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'message', MessageTypeEnum::ERROR . $e->getMessage()]);
        }
    }

    public function productSellerNotificationList($seller_id)
    {
        $seller = Seller::find($seller_id);
        $notifications = PushNotification::join('push_notification_receivers', 'push_notification_receivers.push_notification_id', '=', 'push_notifications.id')
            ->where('push_notification_receivers.receiver_id', $seller->getUser->id)
            ->select(
                'push_notifications.id'
                , 'push_notifications.notification_by'
                , 'push_notifications.description'
                , 'push_notifications.notification_repeat'
                , 'push_notifications.receiver_type'

            )
            ->OrderBy('push_notifications.updated_at', 'desc')
            ->paginate(env('PAGINATION_SMALL'));

        $seller = Seller::find($seller_id);
        $user_id = $seller->user_id;

        return view('admin/seller/product/notification/list')
            ->with('user_id', $user_id)
            ->with('seller_id', $seller_id)
            ->with('notifications', $notifications);

    }

    public function productSellerNotificationSave(Request $request)
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
                if (!empty($request->seller_id)) {
                    $seller_id = Crypt::decrypt($request->seller_id);
                    $seller = Seller::where('id', $seller_id)->first();
                }
                if (isset($encrypted_id)) {
                    try {
                        $notification = PushNotification::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                if (isset($notification)) {
                    DB::beginTransaction();

                    $notification->notification_by = Auth::user()->id;
                    $notification->description = $request->description;
                    $notification->notification_repeat = $request->notification_repeat;
                    $notification->save();

                    if (isset($seller_id)) {
                        $push_notification_receiver = new PushNotificationReceiver();
                        $push_notification_receiver->push_notification_id = $notification->id;
                        $push_notification_receiver->is_viewed = 0;
                        $push_notification_receiver->receiver_id = $seller->getUser->id;
                        $push_notification_receiver->receiver_type = PushNotificationRepeatTypeEnum::SELLER;
                        $push_notification_receiver->save();
                    } else {
                        $push_notification_receiver = PushNotificationReceiver::where('push_notification_id', $notification->id)->first();
                        $push_notification_receiver->is_viewed = 0;
                        $push_notification_receiver->receiver_type = PushNotificationRepeatTypeEnum::SELLER;
                        $push_notification_receiver->save();
                    }

                    if (isset($seller_id)) $user = $seller->getUser;
                    else $user = User::find($push_notification_receiver->receiver_id);

                    $data = ['username' => $user->username, 'description' => $notification->description];
                    Mail::send('emails.notification', $data, function ($message) use ($user) {
                        $message->to($user->email, $user->username)
                            ->subject('New Notification');
                    });

                    DB::commit();
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

    public function productSellerNotificationDelete($id)
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

    // Product Seller Order List
    public function productSellerOrderList(Request $request, $seller_id)
    {
        $status = OrderStatusEnum::PENDING;
        if (!empty($request->status)) $status = $request->status;

        $from = '';
        $to = '';

        if (!empty($request->from)) $from = date('Y-m-d H:i:s', strtotime($request->from . ' 00:00:01'));
        if (!empty($request->to)) $to = date('Y-m-d H:i:s', strtotime($request->to . ' 23:59:59'));

        if (isset($request->status)) $status = $request->status;

        $subOrders = SubOrder::join('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->select(
                'orders.status', 'sub_orders.*'
            )
            ->where('sub_orders.seller_id', $seller_id);


        if ($status == OrderStatusEnum::DELIVERED || $status == OrderStatusEnum::FINALIZED || $status == OrderStatusEnum::REJECTED) {
            $subOrders = $subOrders->where('sub_orders.status', $status);
        } else {
            $subOrders = $subOrders->where('orders.status', $status);
        }

        $subOrders = $subOrders->orderBy('orders.created_at', 'desc')
            ->groupby('orders.id');

        if (!empty($from) && !empty($to)) {
            $subOrders = $subOrders->whereBetween('orders.created_at', [$from, $to]);
        }
        $subOrders = $subOrders->paginate(env('PAGE_PAGINATE'));



        $seller = Seller::find($seller_id);
        $user_id = $seller->user_id;

        return view('/admin/seller/product/order/list')
            ->with('user_id', $user_id)
            ->with('seller_id', $seller_id)
            ->with('status', $status)
            ->with('subOrders', $subOrders);
    }



    // Product Seller Deal List
    public function productSellerDealList(Request $request, $seller_id)
    {
        $status = DealStatusEnum::APPROVED;

        $from = '';
        $to = '';

        if (!empty($request->from)) $from = date('Y-m-d H:i:s', strtotime($request->from . ' 00:00:00'));
        if (!empty($request->to)) $to = date('Y-m-d H:i:s', strtotime($request->to . ' 23:59:59'));

        if (isset($request->status)) $status = $request->status;

        $deals = Deal::join('products', 'products.id', '=', 'deals.product_id')
            ->select(
                'deals.id',
                'deals.product_id',
                'products.name as product_name',
                'deals.title',
                'deals.description',
                'deals.discount',
                'deals.discount_type',
                'deals.from_date',
                'deals.to_date',
                'deals.status',
                'deals.created_at'
            )
            ->where('products.seller_id', $seller_id)
            ->where('deals.status', $status);

        if (!empty($from)) {
            $deals = $deals->whereBetween('deals.from_date', [$from, $to]);
        }

        if (!empty($to)) {
            $deals = $deals->whereBetween('deals.to_date', [$from, $to]);
        }

        $deals = $deals->paginate(env('PAGE_PAGINATE'));

        $seller = Seller::find($seller_id);
        $user_id = $seller->user_id;

        return view('/admin/seller/product/deal/list')
            ->with('user_id', $user_id)
            ->with('seller_id', $seller_id)
            ->with('status', $status)
            ->with('deals', $deals);
    }

    // Product Seller Deal Save
    public function productSellerDealSave(Request $request)
    {

        try {
            $deal = new Deal();
            if (isset($_GET['edit_id']) || isset($_GET['add_id'])) {

                if (isset($_GET['edit_id']))
                    $deal = Deal::where('id', $_GET['edit_id'])->first();

                $data_generate = '';
                $data_generate .= '<div class="form-group"><div class="row">';


                $data_generate .= '<div class="col-sm-12"><div class="form-group">
                                    <label class="required">Deal Title</label>
                                    <input autocomplete="off" required="" type="text" class="form-control" name="deal_title" value="' . $deal->title . '">';
                $data_generate .= '</div></div>';


                $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                    <label class="required">Discount</label>
                                    <input  autocomplete="off"  required="" type="number" class="form-control" name="discount" value="' . $deal->discount . '">';
                $data_generate .= '</div></div>';


                $data_generate .= '<div class="col-sm-6"><div class="form-group">
                                    <label class="">Discount Type</label>
                                    <select required="" name="discount_type" class="form-control" >';
                $data_generate .= '<option value="' . DiscountTypeEnum::FIXED . '">Fixed</option>';
                $data_generate .= '<option value="' . DiscountTypeEnum::PERCENTAGE . '">Percentage</option>';
                $data_generate .= '</select></div></div>';


                $data_generate .= '<div class="col-sm-12"><div class="form-group"><label class="">Deal for the Product</label><select required="" name="product_id" class="form-control" >';

                $products = Product::where('seller_id', Crypt::decrypt($request->seller_id))->orderby('name', 'asc')->get();
                if (isset($products)) {
                    foreach ($products as $p) {
                        $data_generate .= '<option value="' . $p->id . '"';
                        if ($deal->product_id == $p->id) $data_generate .= ' selected ';
                        $data_generate .= '>' . $p->name . '</option>';
                    }
                }
                $data_generate .= '</select></div></div>';


                $data_generate .= '<div class="col-md-6">
                        <div class="form-group">
                        <label for="">Valid For</label>
							<div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control" name="start"';
                if (!empty($deal->from_date)) {
                    $data_generate .= 'value="' . date('d-m-Y', strtotime($deal->from_date)) . '"';
                } else {
                    $data_generate .= 'value="' . date('d-m-Y') . '"';
                }
                $data_generate .= ';/>
                                <span class="input-group-addon">to</span>
                                <input type="text" class="input-sm form-control" name="end"';
                if (!empty($deal->from_date)) {
                    $data_generate .= 'value="' . date('d-m-Y', strtotime($deal->to_date)) . '"';
                } else {
                    $data_generate .= 'value="' . date('d-m-Y', strtotime("+1 month")) . '"';
                }
                $data_generate .= ';/>
                            </div></div>
						</div>';

                $data_generate .= '<div class="col-sm-12">
                                <div class="form-group">
                                    <label class="">Deal Details</label>
                                    <textarea name="description" class="form-control" rows="6">' . $deal->description . '</textarea>
                                </div>
                            </div>';


                if (isset($_GET['add_id'])) $data_generate .= '<input type="hidden" name="seller_id" value="' . $_GET['seller_id'] . '">';
                if (!isset($_GET['add_id']))
                    $data_generate .= '<input type="hidden" id="edit_id" class="form-control" name="edit_id" value="' . Crypt::encrypt($deal->id) . '">';

                return response()->json(array('success' => true, 'data_generate' => $data_generate));

            } else {

                $encrypted_id = $request->input('edit_id');

                if (isset($encrypted_id)) {
                    try {
                        $deal = Deal::find(Crypt::decrypt($encrypted_id));
                    } catch (DecryptException $e) {
                        return redirect()->back()->with('message', 'Error: ' . $e->getMessage());
                    }
                }

                $from_date = '';
                $to_date = '';
                if (!empty($request->start)) $from_date = date('Y-m-d H:i:s', strtotime($request->start . ' 00:00:00'));
                if (!empty($request->end)) $to_date = date('Y-m-d H:i:s', strtotime($request->end . ' 23:59:59'));

                if (Deal::isSameDealExist($deal, $request->product_id, $from_date, $to_date) <= 0) {
                    if (isset($deal)) {
                        $deal->title = $request->deal_title;
                        $deal->description = $request->description;
                        $deal->product_id = $request->product_id;
                        $deal->discount = $request->discount;
                        $deal->discount_type = $request->discount_type;

                        $deal->from_date = $from_date;
                        $deal->to_date = $to_date;
                        $deal->save();
                    }
                } else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . "Another deal is running of this product within this time period.");


                if (isset($encrypted_id))
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_UPDATED_SUCCESSFULLY'));
                else
                    return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_ADDED_SUCCESSFULLY'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    //Product Seller Deal Change Status
    public function productSellerDealChangeStatus(Request $request)
    {
        try {
            $deal = Deal::find($request->deal_id);

            if ($deal->status == DealStatusEnum::PENDING) $deal->status = DealStatusEnum::APPROVED;
            else $deal->status = DealStatusEnum::PENDING;

            $deal->save();

            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . 'Something Went Wrong.');
        }
    }

    public function dealDelete($id)
    {
        if (((int)$id) > 0) {
            $deal = Deal::find($id);
            if (isset($deal) && isset($deal->id)) {
                $deal->delete();
            }
        } else
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::WARNING . env('MSG_WRONG'));

        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . env('MSG_DELETED_SUCCESSFULLY'));
    }
}
