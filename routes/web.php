<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LawController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\Customer\CustomerAuth;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Main\BusinessController;
use App\Http\Middleware\LocaleRedirectMiddleware;
use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Middleware\LocalizationMainMiddleware;
use App\Http\Controllers\Auth\ResetPasswordController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
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
Route::get('/signin', [AuthController::class, 'signIn'])->name('super.signin');
Route::post('/signin', [AuthController::class, 'handleSignIn'])->name('super.signin.post');
Route::get('/password-reset', [AuthController::class, 'passwordReset'])->name('super.password.reset');
Route::post('/password-reset', [AuthController::class, 'sendResetLinkEmail'])->name('super.password.email');
Route::post('/logout', [AuthController::class, 'signOut'])->name('super.signout');
Route::get('/lockscreen', [AuthController::class, 'lock'])->name('lockscreen');
Route::post('/unlock', [AuthController::class, 'unlock'])->name('unlock');
Route::get('/auth-logout', [AuthController::class, 'logoutpage'])->name('logoutpage');
Route::get('/suspended-account', [AuthController::class, 'suspend'])->name('suspend');

// Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
// DASHBOARD - ADMIN
Route::prefix('{locale}/super-admin')->middleware(['LocalizationMainMiddleware','superadmincheck','authcheck'])->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('super.dashboard');
    Route::get('/brands-managements', [SuperAdminController::class, 'brand'])->name('super.brand');
    Route::get('/categories-managements', [SuperAdminController::class, 'category'])->name('super.category');
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
    Route::get('/customer-list', [SuperAdminController::class, 'customerList'])->name('super.customerList');
    Route::get('/customer-ranking', [SuperAdminController::class, 'customerRanking'])->name('super.customerRanking');
    Route::get('/customer-orders/{id}', [SuperAdminController::class, 'customerOrder'])->name('super.customerOrder');
    Route::prefix('tasks')->group(function () {
        Route::get('/all-driver-task', [DriverController::class, 'allDriverTask'])->name('driver.task.all');
        Route::get('/driver-task', [DriverController::class, 'driverTask'])->name('driver.task');
    });
    Route::prefix('pdf-viewer')->group(function () {
        Route::get('/invoicepdf/{tracking}', [PdfController::class, 'pdfOrderInvoice'])->name('pdf.order.invoice');
    });
});
// DASHBOARD - DRIVER
Route::prefix('{locale}/drivers')->middleware(['LocalizationMainMiddleware','drivercheck','driverstatuscheck'])->group(function () {
    Route::get('/', [DriverController::class, 'dashboard'])->name('driver.dashboard');
});
// HOME - CUSTOEMRS
Route::prefix('{locale}')->middleware(['LocalizationMainMiddleware'])->group(function () {
    Route::get('/', [BusinessController::class, 'home'])->name('business.home');
    Route::get('account', [BusinessController::class, 'account'])->name('business.account');        
    Route::get('about-us', [BusinessController::class, 'aboutus'])->name('business.aboutus');
    Route::get('contact-us', [BusinessController::class, 'contactus'])->name('business.contactus');
    Route::get('faq', [BusinessController::class, 'faq'])->name('business.faq');
    Route::get('register', [BusinessController::class, 'register'])->name('business.register');
    Route::get('shop', [BusinessController::class, 'productShop'])->name('business.productShop');
    Route::get('categories', [BusinessController::class, 'productCategory'])->name('business.category');
    Route::get('brands', [BusinessController::class, 'productBrand'])->name('business.brand');
    Route::get('spare', [BusinessController::class, 'productShopSpare'])->name('business.productShopSpare');
    Route::get('product/{slug}', [BusinessController::class, 'productDetail'])->name('business.productDetail');
    Route::get('wishlist-list', [BusinessController::class, 'wishlist'])->name('business.whishlist');
    Route::get('view-cart-list', [BusinessController::class, 'viewcart'])->name('business.viewcart');
    Route::get('checkout-list', [BusinessController::class, 'checkout'])->name('business.checkout');
    Route::get('shop-search', [BusinessController::class, 'searchShop'])->name('business.shop.search');
    // ->middleware('update.product.slug')

    Route::post('processing-checkout-list/{digit}/{nvxf}', [BusinessController::class, 'checkoutChecker'])->name('business.checkoutChecker');
    Route::post('/account', [CustomerAuth::class, 'updatePassword'])->name('business.account');
    Route::post('/avatarupload', [CustomerAuth::class, 'avatarupload'])->name('customer.avatarupload');
    Route::post('/register', [CustomerAuth::class, 'register'])->name('customer.register');
    Route::post('/cust-login', [CustomerAuth::class, 'login'])->name('customer.login');
    Route::post('/cust-logout', [CustomerAuth::class, 'logout'])->name('customer.logout');
    
    Route::get('/cust-address', [CustomerAddressController::class, 'index'])->name('customer.address');
    Route::post('/cust-address', [CustomerAddressController::class, 'store'])->name('customer.addresses.store');
});

Route::get('law/terms-conditions', [LawController::class, 'termsCondition'])->name('law.terms');
Route::get('law/privacy-policy', [LawController::class, 'privacyPolicy'])->name('law.privacy');

Route::get('/', function () {
    return redirect()->to('/en', 301);
});
    
    


   
    // Route::middleware([LocaleRedirectMiddleware::class])->group(function () {
    // });