<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Customer\CustomerAuth;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Main\BusinessController;
use App\Http\Middleware\LocaleRedirectMiddleware;
use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Middleware\LocalizationMainMiddleware;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Customer\CustomerAddressController;
use App\Http\Controllers\LawController;

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
        Route::get('/edit/{id}', [SuperAdminController::class, 'eProduct'])->name('super.product.edit');
        Route::get('/users-managements', [SuperAdminController::class, 'user'])->name('super.users');
        Route::get('/profile', [SuperAdminController::class, 'profile'])->name('super.profile');
        Route::get('/delivery-zones', [SuperAdminController::class, 'deliveryZones'])->name('super.deliveryZones');
        Route::get('/shipping-costs', [SuperAdminController::class, 'shippingCost'])->name('super.shippingCost');
    });
    // HOME - CUSTOEMRS
    Route::prefix('{locale}')->middleware(['LocalizationMainMiddleware'])->group(function () {
        Route::get('/', [BusinessController::class, 'home'])->name('business.home');
        Route::get('account', [BusinessController::class, 'account'])->name('business.account');        
        Route::get('register', [BusinessController::class, 'register'])->name('business.register');
        Route::get('shop', [BusinessController::class, 'productShop'])->name('business.productShop');
        Route::get('categories', [BusinessController::class, 'productCategory'])->name('business.category');
        Route::get('brands', [BusinessController::class, 'productBrand'])->name('business.brand');
        Route::get('spare', [BusinessController::class, 'productShopSpare'])->name('business.productShopSpare');
        Route::get('product/{slug}', [BusinessController::class, 'productDetail'])->name('business.productDetail');
        Route::get('wishlist-list', [BusinessController::class, 'wishlist'])->name('business.whishlist');
        Route::get('view-cart-list', [BusinessController::class, 'viewcart'])->name('business.viewcart');
        Route::get('checkout-list', [BusinessController::class, 'checkout'])->name('business.checkout');
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