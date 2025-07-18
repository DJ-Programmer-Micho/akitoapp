<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LawController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\Main\OrderController;
use App\Http\Controllers\Customer\CustomerAuth;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Main\BusinessController;
use App\Http\Middleware\LocaleRedirectMiddleware;
use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Middleware\LocalizationMainMiddleware;
use App\Http\Controllers\Gateaway\PaymentController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Gateaway\TransactionController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Customer\CustomerAddressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// FIB Payment Method
// Route::post('fib/callback', [FibCallbackController::class, 'handle'])->name('fib.callback');
// Route::get('/payment/fib/{paymentId}', [BusinessController::class, 'showFIBPaymentPage'])
//     ->name('payment.fib');

// SITEMAPS
Route::get("sitemap_en.xml" , function () { return \Illuminate\Support\Facades\Redirect::to('sitemap_en.xml'); });
Route::get("sitemap_ar.xml" , function () { return \Illuminate\Support\Facades\Redirect::to('sitemap_ar.xml'); });
Route::get("sitemap_ku.xml" , function () { return \Illuminate\Support\Facades\Redirect::to('sitemap_ku.xml'); });

Route::post('/set-locale', [LocalizationMainMiddleware::class, 'setLocale'])->name('setLocale');

// routes/web.php
Route::get('/temp-images/{filename}', function ($filename) {
    $path = storage_path('app/livewire-tmp/' . $filename);

    if (!File::exists($path)) {
        abort(404); // Return 404 if file is not found
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
})->name('temp-images');

// DASHBOARD - AUTH
Route::get('/signin-akitu-a', [AuthController::class, 'signIn'])->name('super.signin');
Route::post('/signin-akitu-a', [AuthController::class, 'handleSignIn'])->name('super.signin.post');
Route::get('/password-reset', [AuthController::class, 'passwordReset'])->name('super.password.reset');
Route::post('/password-reset', [AuthController::class, 'sendResetLinkEmail'])->name('super.password.email');
Route::post('/logout', [AuthController::class, 'signOut'])->name('super.signout');
Route::get('/lockscreen', [AuthController::class, 'lock'])->name('lockscreen');
Route::post('/unlock', [AuthController::class, 'unlock'])->name('unlock');
Route::get('/auth-logout', [AuthController::class, 'logoutpage'])->name('logoutpage');
Route::get('/suspended-account', [AuthController::class, 'suspend'])->name('suspend');

// CUSTOMER OTP
Route::prefix('{locale}')->middleware(['LocalizationMainMiddleware'])->group(function () {
    // EMAIL FORGET
    Route::get('uasfdr-oiugo-gfhft-iuyer/password/forgot', [CustomerAuth::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('uasfdr-oiugo-gfhft-iuyer/password/email', [CustomerAuth::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/ytuew-uasfdr-oiugo-gfhft/reset-password/{token}', [CustomerAuth::class, 'showResetForm'])->name('password.reset');
    Route::post('ytuew-uasfdr-oiugo-gfhft/password/reset', [CustomerAuth::class, 'reset'])->name('password.update');
    Route::get('/success-reset-password', [CustomerAuth::class, 'successResetMsg'])->name('password.successResetMsg');
    // EMAIL
    Route::get('/email-verify-otp/{id}/{email}', [CustomerAuth::class,'goEmailOTP'])->name('goEmailOTP');
    Route::get('/update-email-otp/{id}', [CustomerAuth::class,'goReEmailOTP'])->name('goReEmailOTP');
    Route::post('/update-email-otp-ser/{id}', [CustomerAuth::class,'updateReEmailOTP'])->name('updateReEmailOTP');
    Route::get('/resend-verify-otp/{id}/{email}', [CustomerAuth::class,'resendEmailOTP'])->name('resendEmailOTP');
    Route::post('/email-verify-otp', [CustomerAuth::class,'verifyEmailOTP'])->name('verifyEmailOTP');
    // PHONE
    Route::get('/verify-otp/{id}/{phone}', [CustomerAuth::class,'goOTP'])->name('goOTP');
    Route::get('/update-phone-otp/{id}', [CustomerAuth::class,'goRePhoneOTP'])->name('goRePhoneOTP');
    Route::post('/update-phone-otp-ser/{id}', [CustomerAuth::class,'updateRePhoneOTP'])->name('updateRePhoneOTP');
    Route::get('/phone-resend-verify-otp/{id}/{phone}', [CustomerAuth::class,'resendPhoneOTP'])->name('resendPhoneOTP');
    Route::post('/verify-otp', [CustomerAuth::class,'verifyOTP'])->name('verifyOTP');
});

// DASHBOARD - ADMIN
Route::prefix('{locale}/super-admin')->middleware(['LocalizationMainMiddleware','superadmincheck','authcheck'])->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('super.dashboard');
    Route::get('/tickers-managements', [SuperAdminController::class, 'ticker'])->name('super.ticker');
    Route::get('/brands-managements', [SuperAdminController::class, 'brand'])->name('super.brand');
    Route::get('/soons-managements', [SuperAdminController::class, 'soon'])->name('super.soon');
    Route::get('/categories-managements', [SuperAdminController::class, 'category'])->name('super.category');
    Route::get('/intensities-managements', [SuperAdminController::class, 'intensity'])->name('super.intensity');
    Route::get('/tags-managements', [SuperAdminController::class, 'tag'])->name('super.tag');
    Route::get('/colors-managements', [SuperAdminController::class, 'color'])->name('super.color');
    Route::get('/sizes-managements', [SuperAdminController::class, 'size'])->name('super.size');
    Route::get('/materials-managements', [SuperAdminController::class, 'material'])->name('super.material');
    Route::get('/capacities-managements', [SuperAdminController::class, 'capacity'])->name('super.capacity');
    Route::get('/product-table', [SuperAdminController::class, 'tProduct'])->name('super.product.table');
    Route::get('/product-create', [SuperAdminController::class, 'cProduct'])->name('super.product.create');
    Route::get('/product-recommend', [SuperAdminController::class, 'recommendProduct'])->name('super.product.recommend');
    Route::get('/product-add-edit-recommend/{id}', [SuperAdminController::class, 'recommendProductEdit'])->name('super.product.recommend.edit');
    Route::get('/edit/{id}', [SuperAdminController::class, 'eProduct'])->name('super.product.edit');
    Route::get('/product-adjust', [SuperAdminController::class, 'adjustProduct'])->name('super.product.adjust');
    Route::get('/users-managements', [SuperAdminController::class, 'user'])->name('super.users');
    Route::get('/driver-team-managements', [SuperAdminController::class, 'driverTeam'])->name('super.driver.team');
    Route::get('/driver-team-managements-add', [SuperAdminController::class, 'driverTeamStore'])->name('super.driver.team.add');
    Route::get('/driver-team-managements-edit/{id}', [SuperAdminController::class, 'driverTeamEdit'])->name('super.driver.team.edit');
    Route::get('/profile', [SuperAdminController::class, 'profile'])->name('super.profile');
    Route::get('/order-managements', [SuperAdminController::class, 'orderManagements'])->name('super.orderManagements');
    Route::get('/order-management-viewer/{id}', [SuperAdminController::class, 'orderManagementsViewer'])->name('super.orderManagementsViewer');
    Route::get('/order-invoice/{tracking}', [SuperAdminController::class, 'orderInvoice'])->name('super.orderInvoice');
    Route::get('/delivery-zones', [SuperAdminController::class, 'deliveryZones'])->name('super.deliveryZones');
    Route::get('/shipping-costs', [SuperAdminController::class, 'shippingCost'])->name('super.shippingCost');
    Route::get('/customer-profile/{id}', [SuperAdminController::class, 'customerProfile'])->name('super.customerProfile');
    Route::get('/customer-list', [SuperAdminController::class, 'customerList'])->name('super.customerList');
    Route::get('/customer-ranking', [SuperAdminController::class, 'customerRanking'])->name('super.customerRanking');
    Route::get('/customer-orders/{id}', [SuperAdminController::class, 'customerOrder'])->name('super.customerOrder');
    Route::get('/customer-discounts', [SuperAdminController::class, 'customerDiscount'])->name('super.customerDiscount');
    Route::prefix('tasks')->group(function () {
        Route::get('/all-driver-task', [DriverController::class, 'allDriverTask'])->name('driver.task.all');
        Route::get('/driver-task', [DriverController::class, 'driverTask'])->name('driver.task');
    });
    Route::prefix('pdf-viewer')->group(function () {
        Route::get('/invoicepdf/{tracking}', [PdfController::class, 'pdfOrderInvoice'])->name('pdf.order.invoice');
        Route::get('/actionpdf/{tracking}', [PdfController::class, 'pdfOrderAction'])->name('pdf.order.action');
    });
    Route::get('/setting-logo', [SuperAdminController::class, 'settingLogo'])->name('setting.logo');
    Route::get('/setting-hero', [SuperAdminController::class, 'settingHero'])->name('setting.hero');
    Route::get('/setting-email', [SuperAdminController::class, 'settingEmail'])->name('setting.email');
    Route::get('/setting-info', [SuperAdminController::class, 'settingInfo'])->name('setting.info');
    Route::get('/setting-recaptcha', [SuperAdminController::class, 'settingRecaptcha'])->name('setting.recaptcha');
    Route::get('/setting-banner', [SuperAdminController::class, 'settingBanner'])->name('setting.banner');
    Route::get('/setting-language', [SuperAdminController::class, 'settingLanguage'])->name('setting.language');
    Route::get('/setting-prices', [SuperAdminController::class, 'settingPrice'])->name('setting.price');
});
// DASHBOARD - DRIVER
Route::prefix('{locale}/drivers')->middleware(['LocalizationMainMiddleware','drivercheck','driverstatuscheck'])->group(function () {
    Route::get('/', [DriverController::class, 'dashboard'])->name('driver.dashboard');
});
// HOME - CUSTOEMRS
Route::prefix('{locale}')->middleware(['LocalizationMainMiddleware'])->group(function () {
    Route::get('/', [BusinessController::class, 'home'])->name('business.home');
    Route::get('about-us', [BusinessController::class, 'aboutus'])->name('business.aboutus');
    Route::get('contact-us', [BusinessController::class, 'contactus'])->name('business.contactus');
    Route::get('faq', [BusinessController::class, 'faq'])->name('business.faq');
    Route::get('register', [BusinessController::class, 'register'])->name('business.register');
    Route::get('shop', [BusinessController::class, 'productShop'])->name('business.productShop');
    Route::get('categories', [BusinessController::class, 'productCategory'])->name('business.category');
    Route::get('brands', [BusinessController::class, 'productBrand'])->name('business.brand');
    Route::get('coming-soon', [BusinessController::class, 'productSoon'])->name('business.soon');
    Route::get('spare', [BusinessController::class, 'productShopSpare'])->name('business.productShopSpare');
    Route::get('product/{slug}', [BusinessController::class, 'productDetail'])->name('business.productDetail');
    Route::get('shop-search', [BusinessController::class, 'searchShop'])->name('business.shop.search');
    Route::get('/order-view/{tracking}', [PdfController::class, 'pdfOrderView'])->name('pdf.order.customer');
    Route::get('/order-cancel-pre-view/{tracking}', [PdfController::class, 'pdfOrderViewCancel'])->name('pdf.order.customer.cancel');
    Route::post('/register', [CustomerAuth::class, 'register'])->name('customer.register');
    Route::post('/cust-login', [CustomerAuth::class, 'login'])->name('customer.login');
    Route::post('/cust-logout', [CustomerAuth::class, 'logout'])->name('customer.logout');
    Route::middleware(['customeridcheck','customercheck'])->group(function () {
        Route::get('account', [BusinessController::class, 'account'])->name('business.account');        
        Route::get('view-cart-list', [BusinessController::class, 'viewcart'])->name('business.viewcart');
        Route::get('wishlist-list', [BusinessController::class, 'wishlist'])->name('business.whishlist');
        Route::get('checkout-list', [BusinessController::class, 'checkout'])->name('business.checkout');
        Route::get('/checkout/order/{orderId}', [BusinessController::class, 'checkoutOrder'])->name('business.checkout.order');
        //POST METHODE
        Route::post('processing-checkout-list/{digit}/{nvxf}', [BusinessController::class, 'checkoutChecker'])->name('business.checkoutChecker');
        Route::post('processing-checkout-old-list/{digit}/{orderId}/{grandTotalUpdated}', [BusinessController::class, 'checkoutExistingOrder'])->name('business.checkoutExistingOrder');
        Route::get('proccess/success', [BusinessController::class, 'checkSuccess'])->name('business.checkout.success');
        Route::get('proccess/failed', [BusinessController::class, 'checkFaild'])->name('business.checkout.failed');

        // Stripe Payment Page for front-end processing
        Route::get('/payment/stripe/{orderId}', [PaymentController::class, 'showStripePaymentPage'])
            ->name('payment.stripe');

        Route::post('/checkout/{orderId}', [PaymentController::class, 'checkout'])->name('checkout');
        Route::get('/payment/success', [PaymentController::class, 'digitSuccess'])->name('digit.payment.success');
        Route::get('/payment/cancel', [PaymentController::class, 'digitCancel'])->name('digit.payment.cancel');
        Route::get('/payment/error', [PaymentController::class, 'digitError'])->name('digit.payment.error');
        
        Route::post('/account', [CustomerAuth::class, 'updatePassword'])->name('business.account.post');
        Route::post('/avatarupload', [CustomerAuth::class, 'avatarupload'])->name('customer.avatarupload');
        
        Route::get('/cust-address', [CustomerAddressController::class, 'index'])->name('customer.address');
        Route::post('/cust-address', [CustomerAddressController::class, 'store'])->name('customer.addresses.store');
        Route::get('/cust-address/{addressId}/edit', [CustomerAddressController::class, 'edit'])->name('customer.addresses.edit');
        Route::put('/cust-address/{addressId}/edit', [CustomerAddressController::class, 'update'])->name('customer.addresses.update');
        Route::delete('/cust-address/{addressId}/delete', [CustomerAddressController::class, 'destroy'])->name('customer.addresses.delete');

        Route::get('/payment/status/{paymentId}/{paymentMethod}', [PaymentController::class, 'checkFIBPaymentStatus'])->name('payment.status');

    // Payment Process
    Route::get('/payment/process/{orderId}/{paymentId}/{paymentMethod}', [PaymentController::class, 'processFrontPayment'])->name('payment.process');

    // FIB Payment Routes
    Route::get('/payment/fib/{paymentId}', [PaymentController::class, 'showFIBPaymentPage'])->name('payment.fib');
    // Route::get('/payment/status/{paymentId}', [PaymentController::class, 'checkFIBPaymentStatus'])->name('payment.status');
    
    // Areeba & ZainCash Payment Routes
    // Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    // Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    // Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');
});
});
Route::post('/payment/cancel-timeout/{paymentId}', [PaymentController::class, 'cancelPayment'])->name('time.payment.cancel');
Route::post('/payment/cancel-by-user/{orderId}', [OrderController::class, 'cancelOrder'])->name('action.payment.cancel');

Route::get('law/terms-conditions', [LawController::class, 'termsCondition'])->name('law.terms');
Route::get('law/privacy-policy', [LawController::class, 'privacyPolicy'])->name('law.privacy');

Route::get('/', function () {
    return redirect()->to('/en', 301);
});