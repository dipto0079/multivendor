<?php

Route::get('/captcha-generate', 'Auth\AuthController@captchaGenerate');
Route::get('/captcha-generate1', 'Auth\AuthController@captchaGenerate1');

/***********************************************************************************************************************
 *                                                ADMIN
 ***********************************************************************************************************************/

Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'Auth\AuthController@getAdminLogin');
    Route::get('/login', 'Auth\AuthController@getAdminLogin');
    Route::post('/login', 'Auth\AuthController@postAdminLogin');
    Route::post('/logout', 'Auth\AuthController@adminLogout');
    Route::post('/profile/update', 'Auth\AuthController@adminProfileUpdate');
});


Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'auth'], function () {

    Route::get('/dashboard', 'DashboardController@dashboard');
    Route::get('/dashboard/ajax', 'DashboardController@dashboardAjax');

    Route::group(['prefix' => '/service/seller'], function () {
        // Service Seller
        Route::get('/', 'ServiceSellerController@serviceSellerList');
        Route::post('/', 'ServiceSellerController@serviceSellerList');
        Route::post('/save', 'ServiceSellerController@serviceSellerSave');
        Route::get('/save', 'ServiceSellerController@serviceSellerSave');
        Route::get('/delete/{id}', 'ServiceSellerController@serviceSellerDelete');
        Route::post('/status-change', 'ServiceSellerController@serviceSellerStatusChange');

        //Service Seller Product
        Route::get('/{seller_id}/details', 'ServiceSellerController@serviceSellerDetails');
        Route::get('/{seller_id}/service/list', 'ServiceSellerController@serviceSellerServiceList');
        Route::get('/service/save', 'ServiceSellerController@serviceSellerServiceSave');
        Route::post('/service/save', 'ServiceSellerController@serviceSellerServiceSave');
        Route::post('/service/is-featured', 'ServiceSellerController@serviceSellerServiceIsFeatured');
        Route::post('/service/is-editors-choice', 'ServiceSellerController@serviceSellerServiceIsEditorsChoice');
        Route::get('/service/delete/{id}', 'ServiceSellerController@serviceSellerServiceDelete');

        Route::get('/service/media/save', 'ServiceSellerController@serviceSellerServiceMediaSave');
        Route::post('/service/media/save', 'ServiceSellerController@serviceSellerServiceMediaSave');
        Route::get('/service/media/delete', 'ServiceSellerController@serviceSellerServiceMediaDelete');

        Route::get('/{seller_id}/notification/list', 'ServiceSellerController@serviceSellerNotificationList');
        Route::get('/notification/save', 'ServiceSellerController@serviceSellerNotificationSave');
        Route::post('/notification/save', 'ServiceSellerController@serviceSellerNotificationSave');
        Route::get('/notification/delete/{notification_id}', 'ServiceSellerController@serviceSellerNotificationDelete');

//        Route::get('/{seller_id}/order/list', 'ServiceSellerController@serviceSellerOrderList');
//        Route::post('/{seller_id}/order/list', 'ServiceSellerController@serviceSellerOrderList');

        Route::get('/{seller_id}/deal/list', 'ServiceSellerController@serviceSellerDealList');
        Route::post('/{seller_id}/deal/list', 'ServiceSellerController@serviceSellerDealList');
        Route::get('/deal/save', 'ServiceSellerController@serviceSellerDealSave');
        Route::post('/deal/save', 'ServiceSellerController@serviceSellerDealSave');
        Route::post('/deal/change-status', 'ServiceSellerController@serviceSellerDealChangeStatus');
        Route::get('/deal/delete/{id}', 'ServiceSellerController@dealDelete');

    });

    Route::group(['prefix' => '/product/seller'], function () {

        // Product Seller
        Route::get('/', 'ProductSellerController@productSellerList');
        Route::post('/', 'ProductSellerController@productSellerList');
        Route::get('/save', 'ProductSellerController@productSellerSave');
        Route::post('/save', 'ProductSellerController@productSellerSave');
        Route::get('delete/{id}', 'ProductSellerController@productSellerDelete');
        Route::post('/status-change', 'ProductSellerController@productSellerStatusChange');

        //Product Seller Products
        Route::get('/{seller_id}/details', 'ProductSellerController@productSellerDetails');
        Route::get('/{seller_id}/product/list', 'ProductSellerController@productSellerProductList');
        Route::post('/{seller_id}/product/list', 'ProductSellerController@productSellerProductList');
        Route::get('/product/save', 'ProductSellerController@productSellerProductSave');
        Route::post('/product/save', 'ProductSellerController@productSellerProductSave');
        Route::post('/product/is-featured', 'ProductSellerController@productSellerProductIsFeatured');
        Route::post('/product/is-editors-choice', 'ProductSellerController@productSellerProductIsEditorsChoice');
        Route::get('/product/delete/{id}', 'ProductSellerController@productSellerProductDelete');

        Route::get('/product/media/save', 'ProductSellerController@productSellerProductMediaSave');
        Route::post('/product/media/save', 'ProductSellerController@productSellerProductMediaSave');
        Route::get('/product/media/delete', 'ProductSellerController@productSellerProductMediaDelete');

        Route::get('/{seller_id}/notification/list', 'ProductSellerController@productSellerNotificationList');
        Route::get('/notification/save', 'ProductSellerController@productSellerNotificationSave');
        Route::post('/notification/save', 'ProductSellerController@productSellerNotificationSave');
        Route::get('/notification/delete/{notification_id}', 'ProductSellerController@productSellerNotificationDelete');

        Route::get('/{seller_id}/order/list', 'ProductSellerController@productSellerOrderList');
        Route::post('/{seller_id}/order/list', 'ProductSellerController@productSellerOrderList');

//        Route::get('/{seller_id}/order/details/{order_id}/{seller_id}', 'OrderController@orderDetails');
//        Route::post('/{seller_id}/order/details/{order_id}/{seller_id}', 'OrderController@orderDetails');

        Route::get('/{seller_id}/deal/list', 'ProductSellerController@productSellerDealList');
        Route::post('/{seller_id}/deal/list', 'ProductSellerController@productSellerDealList');
        Route::get('/deal/save', 'ProductSellerController@productSellerDealSave');
        Route::post('/deal/save', 'ProductSellerController@productSellerDealSave');
        Route::post('/deal/change-status', 'ProductSellerController@productSellerDealChangeStatus');
        Route::get('/deal/delete/{id}', 'ProductSellerController@dealDelete');

//        Route::post('/order/details', 'ProductSellerController@orderDetails');

    });

    Route::post('/store-exists', 'UtilityController@storeExists');

    Route::get('/city-by-country', 'UtilityController@cityByCountry');

    Route::group(['prefix' => '/order'], function () {
        Route::get('/pending', 'OrderController@pendingOrderList');
        Route::get('/accepted', 'OrderController@acceptedOrderList');
        Route::get('/delivered-feedback-pending', 'OrderController@deliveredFeedbackPendingOrderList');
        Route::get('/finalized', 'OrderController@finalizedOrderList');
        Route::get('/rejected', 'OrderController@rejectedOrderList');
        Route::post('/publish-order', 'OrderController@publishOrder');
        Route::get('/reject/{id}', 'OrderController@orderRejected');

        Route::get('/details/{id}/{seller_id?}/{calculate_admin_commission?}', 'OrderController@orderDetails');

        Route::get('/sub_order/status/{id}', 'OrderController@orderSubOrderStatus');
    });

    Route::group(['prefix' => 'buyer'], function () {
        Route::get('/', 'BuyerController@buyerList');
        Route::post('/', 'BuyerController@buyerList');
        Route::post('/edit-profile', 'BuyerController@buyerEditProfile');
        Route::get('/delete/{id}', 'BuyerController@buyerDelete');
        Route::post('/block', 'BuyerController@buyerBlock');

        Route::get('/{buyer_id}/notification/list', 'BuyerController@buyerNotificationList');
        Route::get('/notification/save', 'BuyerController@buyerNotificationSave');
        Route::post('/notification/save', 'BuyerController@buyerNotificationSave');
        Route::get('/notification/delete/{notification_id}', 'BuyerController@buyerNotificationDelete');


        Route::get('/{buyer_id}/order/list', 'BuyerController@buyerOrderList');
        Route::post('/{buyer_id}/order/list', 'BuyerController@buyerOrderList');


//        Route::get('/save','BuyerController@buyerSave');

    });

    Route::group(['prefix' => 'settings'], function () {
        // Settings
        Route::get('/', 'SettingsController@settingList');
        Route::post('/save', 'SettingsController@settingSave');

        //Category
        Route::get('/category/service', 'SettingsController@categoryServiceList');
        Route::get('/category/product', 'SettingsController@categoryProductList');
        Route::post('/category/save', 'SettingsController@categorySave');
        Route::get('/category/delete/{id}', 'SettingsController@categoryDelete');
        Route::post('/category/show-in-public-menu', 'SettingsController@showInPublicMenu');

        //City
        Route::get('/city/list', 'SettingsController@cityList');
        Route::post('/city/save', 'SettingsController@citySave');
        Route::post('/city/status', 'SettingsController@cityStatus');
        Route::get('/city/delete/{id}', 'SettingsController@cityDelete');

        // Location
        Route::get('/location/list', 'SettingsController@locationList');
        Route::post('/location/save', 'SettingsController@locationSave');
        Route::get('/location/delete/{id}', 'SettingsController@locationDelete');

        // Static Page
        Route::get('/static-page', 'SettingsController@staticPageList');
        Route::post('/static-page/save', 'SettingsController@staticPageSave');
        Route::get('/static-page/delete/{id}', 'SettingsController@staticPageDelete');

        // Admin User
        Route::get('/admin-user', 'SettingsController@adminUserList');
        Route::post('/admin-user/save', 'SettingsController@adminUserSave');
        Route::get('/admin-user/delete/{id}', 'SettingsController@adminUserDelete');

        // Admin Role
        Route::get('/admin-role', 'SettingsController@adminRoleList');
        Route::post('/admin-role/save', 'SettingsController@adminRoleSave');
        Route::post('/admin-role/permission', 'SettingsController@adminRolePermission');
        Route::post('/admin-role/permission/save', 'SettingsController@adminRolePermissionSave');
        Route::get('/admin-role/delete/{id}', 'SettingsController@adminRoleDelete');

        //Coupon
        Route::get('/coupon', 'SettingsController@couponList');
        Route::post('/coupon/save', 'SettingsController@couponSave');
        Route::get('/coupon/delete/{id}', 'SettingsController@couponDelete');

        // Advertisement
        Route::get('/advertisement', 'SettingsController@advertisementList');
        Route::post('/advertisement/save', 'SettingsController@advertisementSave');
        Route::get('/advertisement/delete/{id}', 'SettingsController@advertisementDelete');

        // Success Story
        Route::get('/success-story', 'SettingsController@successStoryList');
        Route::post('/success-story/save', 'SettingsController@successStorySave');
        Route::get('/success-story/delete/{id}', 'SettingsController@successStoryDelete');

        // Question
        Route::get('/question', 'SettingsController@questionList');
        Route::get('/question/details/{id}', 'SettingsController@questionDetails')->where('id', '[0-9]+');;
        Route::post('/question/reply', 'SettingsController@questionReplySave');
        Route::get('/question/delete/{id}', 'SettingsController@questionDelete')->where('id', '[0-9]+');;
    });

    Route::group(['prefix' => '/payment'], function () {
        Route::get('/', 'PaymentController@paymentList');

        Route::post('/export', 'PaymentController@paymentExport');

        Route::get('/final', 'PaymentController@paymentFinalList');
        Route::post('/final', 'PaymentController@paymentFinalList');
        Route::post('/final/status', 'PaymentController@paymentFinalStatus');
        Route::post('/finalized', 'PaymentController@paymentFinalized');
    });

    Route::get('/statistics', 'PaymentController@statistics');

});

/***********************************************************************************************************************
 *                                                FRONT END
 ***********************************************************************************************************************/

Route::group(['middleware' => 'language'], function () {

    Route::group(['prefix' => '', 'namespace' => 'Auth'], function () {
        Route::get('/login', 'AuthController@getlogin');
        Route::get('/buyer/login', 'AuthController@loginBuyer');
        Route::get('/seller/login', 'AuthController@loginSeller');
        Route::post('/buyer/login', 'AuthController@loginBuyerPost');
        Route::post('/seller/login', 'AuthController@loginSellerPost');
        Route::get('/logout', 'AuthController@publicLogout');

        Route::get('/login/facebook', 'AuthController@redirectToFacebookProvider');
        Route::get('/login/facebook/callback', 'AuthController@handleFacebookProviderCallback');
        Route::get('/login/google', 'AuthController@redirectToGoogleProvider');
        Route::get('/login/google/callback', 'AuthController@handleGoogleProviderCallback');

        Route::get('/seller-registration', 'AuthController@sellerRegistration');
        Route::post('/seller-registration/save', 'AuthController@sellerRegistrationSave');
        Route::get('/seller-registration/city-by-country', 'AuthController@sellerRegistrationCityByCountry');
        Route::post('/seller-registration/city-by-country', 'AuthController@sellerRegistrationCityByCountry');

        Route::get('/buyer-registration', 'AuthController@buyerRegistration');
        Route::post('/buyer-registration/save', 'AuthController@buyerRegistrationSave');
        Route::get('/buyer-email-confirmation/{confirmation_code}', 'AuthController@buyerEmailConfirmation');
    });

    Route::group(['prefix' => 'seller', 'namespace' => 'FrontEnd', 'middleware' => 'auth'], function () {
        Route::get('/edit-profile', 'SellerController@editProfileSeller');
        Route::get('/edit-profile-save', 'SellerController@editProfileSellerSave');
        Route::post('/edit-profile-save', 'SellerController@editProfileSellerSave');

        Route::get('/order-list', 'SellerController@orderList');

        Route::get('/order-list-details', 'SellerController@orderListDetails');
//        Route::get('/order/details/{id}/{seller_id?}', 'OrderController@orderDetails');

        Route::post('/order-change-status', 'SellerController@orderStatusChange');
        Route::get('/order-history', 'SellerController@orderHistory');

        Route::get('/products', 'SellerController@productList');
        Route::get('/services', 'SellerController@serviceList');

        Route::get('/product/deal/{id}', 'SellerController@productDealList');
        Route::post('/product/deal/save', 'SellerController@productDealSave');
        Route::post('/product/save', 'SellerController@productSave');
        Route::get('/product/save', 'SellerController@productSave');
        Route::get('/product/media/save', 'SellerController@productMediaSave');
        Route::post('/product/media/save', 'SellerController@productMediaSave');
        Route::get('/product/media/delete', 'SellerController@productMediaDelete');
        Route::post('/product/featured', 'SellerController@featuredProduct');
        Route::get('/product/delete/{id}', 'SellerController@productDelete');

        Route::post('/product/quantity', 'SellerController@productQuantity');

        Route::get('/notification', 'SellerController@notification');
        Route::get('/remove-notification/{id}', 'SellerController@removeNotification');
        Route::get('/notification-read/{id}', 'SellerController@notificationRead');
        Route::get('/notification-clear-all', 'SellerController@notificationClearAll');

        Route::get('/question', 'SellerController@sellerQuestion');
        Route::get('/question/details/{id}', 'SellerController@sellerQuestionDetails');
        Route::get('/question/delete/{id}', 'SellerController@sellerQuestionDelete');
        Route::post('/question/save', 'SellerController@sellerQuestionSave');
        Route::post('/question/reply/save', 'SellerController@sellerQuestionReplySave');

        Route::post('/payment/claim', 'SellerController@sellerPaymentClaim');

        Route::get('/my-earnings', 'SellerController@sellerMyEarnings');

        Route::get('/shipping-and-tax', 'SellerController@sellerShippingAndTex');
        Route::get('/add-shipping-and-tax', 'SellerController@sellerAddShippingAndTex');
        Route::get('/shipping-and-tax-save', 'SellerController@sellerShippingAndTexSave');
        Route::post('/shipping-and-tax-save', 'SellerController@sellerShippingAndTexSave');
        Route::get('/edit-shipping-and-tax/{id}', 'SellerController@sellerEditShippingAndTex');
        Route::get('/delete-shipping-and-tax/{id}', 'SellerController@sellerDeleteShippingAndTex');
        Route::post('/shipping-and-tax-update', 'SellerController@sellerShippingAndTexUpdate');

        Route::get('/memberships', 'SellerController@sellerMemberships');
        Route::post('membership/save', 'SellerController@sellerMembershipSave');
    });

    Route::group(['prefix' => 'buyer', 'namespace' => 'FrontEnd', 'middleware' => 'auth'], function () {
        Route::get('/my-dashboard', 'BuyerController@myDashboard');
        Route::get('/order-history', 'BuyerController@orderHistory');
        Route::get('/order-history-details', 'BuyerController@orderHistoryDetails');

        Route::get('/wish-list', 'BuyerController@wishList');
        Route::get('/remove-wish-list/{id}', 'BuyerController@removeWishList');
        Route::get('/favourite-store', 'BuyerController@favouriteStore');
        Route::get('/remove-favorite-store/{id?}', 'BuyerController@removeFavoriteStore');

        Route::get('/cart-list', 'BuyerController@buyerCartList');
        Route::post('/update/cart-list', 'BuyerController@buyerUpdateCartList');
        Route::get('/remove-cart-list/{id}', 'BuyerController@removeCartList');
        Route::get('/apply-coupon', 'BuyerController@buyerApplyCoupon');
        Route::post('/delivery-payment', 'BuyerController@deliveryPayment');
        Route::post('/place-order', 'BuyerController@placeOrder');
        Route::get('/shipping-calculation', 'BuyerController@shippingCalculation');

        Route::get('/purchased-success', 'BuyerController@purchasedSuccess');
        Route::get('/purchased-failed', 'BuyerController@purchasedFailed');

        Route::get('/edit-profile', 'BuyerController@editProfileBuyer');
        Route::post('/edit-profile', 'BuyerController@editProfileBuyerSave');

        Route::get('/password', 'BuyerController@password');
        Route::post('/password', 'BuyerController@passwordSave');

        Route::get('/notification', 'BuyerController@notification');
        Route::get('/remove-notification/{id}', 'BuyerController@removeNotification');
        Route::get('/notification-read/{id}', 'BuyerController@notificationRead');
        Route::get('/notification-clear-all', 'BuyerController@notificationClearAll');


        Route::get('/question', 'BuyerController@buyerQuestion');
        Route::get('/question/details/{id}', 'BuyerController@buyerQuestionDetails');
        Route::get('/question/delete/{id}', 'BuyerController@buyerQuestionDelete');
        Route::post('/question/save', 'BuyerController@buyerQuestionSave');
        Route::post('/question/reply/save', 'BuyerController@buyerQuestionReplySave');
    });

    Route::group(['prefix' => '', 'namespace' => 'FrontEnd'], function () {
        Route::get('/', 'HomeController@index');

        Route::group(['prefix' => 'service'], function () {
            Route::get('/', 'HomeController@services');
            Route::get('/category/{cid?}/{first_child_cid?}/{second_child_cid?}/{store_name?}', 'ProductListController@serviceProvider');
            Route::get('/details/{product_id}', 'HomeController@serviceDetails');
            Route::get('/provider/details/{service_id}', 'HomeController@serviceProviderDetails');
        });

        Route::group(['prefix' => 'products'], function () {
            Route::get('/', 'HomeController@products');
            Route::get('/category/{cid?}/{first_child_cid?}/{second_child_cid?}/{store_name?}', 'ProductListController@productCategories');
        });
        Route::get('/product/details/{product_id}', 'HomeController@details');

        Route::post('/product-review', 'HomeController@productReview');
        Route::post('/store-review', 'HomeController@storeReview');

        Route::get('/stores', 'HomeController@storeList');
        Route::get('/store/{store_name}/{cid?}/{first_child_cid?}/{second_child_cid?}', 'ProductListController@storeDetails');


        Route::get('/add-to-favorite-store', 'HomeController@addToFavoriteStore');
        Route::get('/add-to-wish-list', 'HomeController@addToWishList');

        Route::get('/deals', 'HomeController@deals');
        Route::get('/deal/all', 'HomeController@dealAll');
        Route::get('/seller/details/{store_name}', 'HomeController@sellerDetails');
        Route::get('/seller/media', 'HomeController@sellerMedia');


        Route::get('/forget', 'HomeController@forgetPassword');
        Route::post('/password-email', 'HomeController@passwordEmail');
        Route::get('/password-reset/{token}', 'HomeController@passwordReset');
        Route::post('/password-change', 'HomeController@passwordChange');

        Route::get('/add-to-cart', 'HomeController@addToCart');
        Route::post('/add-to-cart', 'HomeController@addToCart');
        Route::get('/cart', 'HomeController@cart');
        Route::get('/remove-cart-items/{id?}', 'HomeController@removeCartItems');
        Route::post('/send-enquiry', 'HomeController@sendEnquiry');

        Route::post('/email-subscribe', 'HomeController@emailSubscribe');
        Route::post('/request-quotation', 'HomeController@requestQuotation');


        Route::get('/switch-language', 'HomeController@switchLanguage');
        Route::get('/about-us', 'HomeController@aboutUs');
        Route::get('/contact-us', 'HomeController@contactUs');
        Route::post('/contact-us-save', 'HomeController@contactUsSave');
        Route::get('/press', 'HomeController@press');
        Route::get('/support', 'HomeController@support');
        Route::get('/privacy-policy', 'HomeController@privacyPolicy');
        Route::get('/term-and-condition', 'HomeController@termAndCondition');


        Route::any('/go/search', 'ProductListController@goSearch');
    });

    Route::post('/email-exists-checking', 'FrontEnd\HomeController@emailExistsChecking');
    Route::post('/seller-email-exists-checking', 'FrontEnd\HomeController@sellerEmailExistsChecking');
    Route::get('/store-exists-checking', 'FrontEnd\HomeController@storeExistsChecking');
    Route::post('/store-exists-checking', 'FrontEnd\HomeController@storeExistsChecking');
});


/***********************************************************************************************************************
 *                                                MOBILE APPS
 ***********************************************************************************************************************/

Route::group(['prefix' => 'mobile-app', 'namespace' => 'Mobile'], function () {


    /***************************************************** Both Apps Using *********************************************/
    Route::get('/category-list/{cid?}/{first_child_cid?}/{second_child_cid?}', 'MobileController@categoryList');
    Route::post('/category-list', 'MobileController@categoryList');

    Route::get('/product-by/type', 'ProductController@ProductByType');
    Route::post('/product-by/type', 'ProductController@ProductByType');

    Route::get('/countries','UserController@countries');
    Route::post('/countries','UserController@countries');

    Route::get('/city-by-country','UserController@cityByCountry');
    Route::post('/city-by-country','UserController@cityByCountry');

    Route::get('/city-by-country-id','UserController@cityByCountryId');
    Route::post('/city-by-country-id','UserController@cityByCountryId');


    /***********************************************************************************************************************/





    Route::get('/products/{id}', 'MobileController@productsByCategory');

    Route::get('/category-product-list', 'ProductController@categoryProductList');
    Route::post('/category-product-list', 'ProductController@categoryProductList');

    Route::get('/user-profile-info', 'UserController@getUserProfile');
    Route::post('/user-profile-info', 'UserController@getUserProfile');





    Route::group(['prefix' => 'user'], function () {
        Route::get('/login', 'UserController@userLogin');
        Route::post('/login', 'UserController@userLogin');

        Route::get('/facebook/login', 'UserController@userFacebookLogin');
        Route::post('/facebook/login', 'UserController@userFacebookLogin');

        Route::get('/google/plus/login', 'UserController@userGooglePlusLogin');
        Route::post('/google/plus/login', 'UserController@userGooglePlusLogin');

        Route::get('/registration', 'UserController@userRegister');
        Route::post('/registration', 'UserController@userRegister');

        Route::get('/registration/varification/{varification_code}', 'UserController@userRegisterVerification');
        Route::post('/forget/password', 'UserController@userForgetPassword');
        Route::post('/profile/update', 'UserController@updateUserProfile');

        //Transaction
        Route::get('/transaction','ProductController@userTransaction');
        Route::post('/transaction', 'ProductController@userTransaction');

        Route::get('/track-order','ProductController@userTrackOrder');
        Route::post('/track-order', 'ProductController@userTrackOrder');

    });


    Route::post('/product/type/list', 'ProductController@ProductTypeList');



    Route::get('/product-details', 'ProductController@ProductDetails');
    Route::post('/product-details', 'ProductController@ProductDetails');

    Route::get('/product-by/seller', 'ProductController@ProductBySeller');
    Route::post('/product-by/seller', 'ProductController@ProductBySeller');

    Route::get('/product-store-info', 'ProductController@ProductStoreInfo');
    Route::post('/product-store-info', 'ProductController@ProductStoreInfo');

    Route::get('/product-review-list', 'ProductController@ProductReviewList');
    Route::post('/product-review-list', 'ProductController@ProductReviewList');

    Route::get('/product-add-to-favorite', 'ProductController@ProductAddToFavorite');
    Route::post('/product-add-to-favorite', 'ProductController@ProductAddToFavorite');

    Route::post('/user-ask-question', 'ProductController@userAskQuestionSave');

    Route::get('/user-question-list', 'ProductController@userQuestionList');
    Route::post('/user-question-list', 'ProductController@userQuestionList');

    Route::get('/user-question-details', 'ProductController@userQuestionDetails');
    Route::post('/user-question-details', 'ProductController@userQuestionDetails');

    Route::post('/user-question-reply', 'ProductController@questionReply');

    Route::get('/product-write-review', 'ProductController@productWriteReview');
    Route::post('/product-write-review', 'ProductController@productWriteReview');

    Route::post('product/near-by', 'ProductController@productNearBy');

    Route::post('/product/popularity', 'ProductController@productPopularity');

    Route::post('product/purchase', 'ProductController@productPurchase');

    Route::get('add-to-cart-mobile', 'PurchaseController@addToCartMobile');
    Route::post('add-to-cart-mobile', 'PurchaseController@addToCartMobile');

    Route::get('cart-item-by-user', 'PurchaseController@cartItemByUser');
    Route::post('cart-item-by-user', 'PurchaseController@cartItemByUser');

    Route::get('/remove-cart-item', 'PurchaseController@removeCartItems');
    Route::post('/remove-cart-item', 'PurchaseController@removeCartItems');

    Route::post('/apply-coupon', 'PurchaseController@buyerApplyCoupon');

    Route::get('/cart-update', 'PurchaseController@cartUpdate');
    Route::post('/cart-update', 'PurchaseController@cartUpdate');

    Route::get('/payment-confirm', 'PurchaseController@cartPaymentConfirm');
    Route::post('/payment-confirm', 'PurchaseController@cartPaymentConfirm');

    Route::get('/shipping-tax-charge', 'PurchaseController@shippingTaxCharge');
    Route::post('/shipping-tax-charge', 'PurchaseController@shippingTaxCharge');
});
