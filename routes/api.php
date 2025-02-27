<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gateaway\CallBackController;
use App\Http\Controllers\Api\V1\CustomerControllerApi;
use App\Http\Controllers\Api\V1\Main\CartControllerApi;
use App\Http\Controllers\Api\V1\Main\ShopControllerApi;
use App\Http\Controllers\Api\V1\Main\PaymentControllerApi;
use App\Http\Controllers\Gateaway\StripeWebhookController;
use App\Http\Controllers\Api\V1\Main\BusinessControllerApi;
use App\Http\Controllers\Api\V1\Main\CheckoutControllerApi;
use App\Http\Controllers\Api\V1\Main\CheckoutOldControllerApi;
use App\Http\Controllers\Api\V1\Main\ProductDetailControllerApi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route::post('/areeba/callback', [CallBackController::class, 'areebaCallBack'])
//     ->middleware('throttle:60,1') 
//     ->name('areeba.callback');

// Route::post('/callback/zaincash', [CallBackController::class, 'zainCashCallBack'])->name('zaincash.callback');
// Route::post('/callback/fib', [CallBackController::class, 'fibCallBack'])->name('fib.callback');
    
Route::post('/payment/callback/{provider}', [CallBackController::class, 'handleCallback'])
    ->middleware('throttle:60,1')
    ->name('payment.callback');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:60,1')
    ->name('stripe.webhook');
    // Route::post('/payment/callback/fib', [CallBackController::class, 'handleFIBCallback'])
    // ->middleware('throttle:60,1')
    // ->name('payment.callback.fib');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth Page 
Route::prefix('v1')->middleware(['LocalizationMainMiddleware'])->group(function () {
    Route::post('customer-login', [CustomerControllerApi::class, 'customerLogin']);

    Route::post('/register', [CustomerControllerApi::class, 'register']);
    Route::post('verify-email-otp', [CustomerControllerApi::class, 'verifyEmailOTP']);
    Route::post('resend-email-otp', [CustomerControllerApi::class, 'resendEmailOTP']);
    Route::post('send-phone-number', [CustomerControllerApi::class, 'sendPhoneNumberAfterVerification']);
    Route::post('verify-phone-otp', [CustomerControllerApi::class, 'verifyPhoneOTP']);
    Route::post('resend-phone-otp', [CustomerControllerApi::class, 'resendPhoneOTP']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('customer-data', [CustomerControllerApi::class, 'customerDetails']);
        Route::post('customer-logout', [CustomerControllerApi::class, 'customerLogout']);

    });
});

// Home Page 
Route::prefix('v1')->middleware(['LocalizationMainMiddleware'])->group(function () {
    Route::get('/categories', [BusinessControllerApi::class, 'categoriesApi'])->name('api.business.categories');
    Route::get('/brands', [BusinessControllerApi::class, 'brandsApi'])->name('api.business.brands');
    Route::get('/hero-carousel', [BusinessControllerApi::class, 'heroCarouselApi'])->name('api.business.hero-carousel');
    Route::get('/featured-products', [BusinessControllerApi::class, 'featuredProductsApi'])->name('api.business.featured-products');
    Route::get('/on-sale-products', [BusinessControllerApi::class, 'onSaleProductsApi'])->name('api.business.on-sale-products');
    Route::get('/category-products', [BusinessControllerApi::class, 'productsByCategoryApi'])->name('api.business.category-products');
});

// Shop Page 
Route::prefix('/v1')->group(function () {
    Route::get('/products', [ShopControllerApi::class, 'productShopApi'])->name('api.shop.products');
    Route::get('/products/search', [ShopControllerApi::class, 'searchProductsApi'])->name('api.shop.search');
});

// product Page 
Route::prefix('/v1')->group(function () {
    Route::get('/product/{id}', [ProductDetailControllerApi::class, 'productDetailApi'])->name('api.product.detail');
});

// 🔒 Authenticate API Requests
Route::middleware('auth:sanctum')->group(function () {
    // cart Page 
    Route::prefix('/v1')->group(function () {
        Route::get('/wishlist', [CartControllerApi::class, 'getWishlist']);
        Route::post('/wishlist/add', [CartControllerApi::class, 'addToWishlist']);
        Route::delete('/wishlist/remove/{productId}', [CartControllerApi::class, 'removeFromWishlist']);
        Route::post('/wishlist/move-to-cart', [CartControllerApi::class, 'moveToCart']);
        Route::post('/wishlist/move-all-to-cart', [CartControllerApi::class, 'moveAllToCart']);

        Route::get('/cart', [CartControllerApi::class, 'getCart']);
        Route::post('/cart/add', [CartControllerApi::class, 'addToCart']);
        Route::patch('/cart/update-quantity', [CartControllerApi::class, 'updateCartQuantity']);
        Route::delete('/cart/remove/{cartItemId}', [CartControllerApi::class, 'removeFromCart']);
    });

    // checkout new order functions 
    Route::prefix('/v1')->group(function () {
        Route::get('/checkout/addresses', [CheckoutControllerApi::class, 'getAddresses']);
        Route::get('/checkout/payment-methods', [CheckoutControllerApi::class, 'getPaymentMethods']);
        Route::get('/checkout/cart-totals', [CheckoutControllerApi::class, 'getCartTotals']);

        Route::get('/account/orders', [CheckoutOldControllerApi::class, 'getOrdersList']);
        Route::get('/checkout/old-orders/{orderId}', [CheckoutOldControllerApi::class, 'getOrderDetails']);
        Route::post('/account/orders/{orderId}/cancel', [CheckoutOldControllerApi::class, 'cancelOrder']);
    });

    // Payment Process
    Route::prefix('/v1')->group(function () {
        Route::post('/checkout/place-order', [PaymentControllerApi::class, 'checkoutChecker']);

        Route::post('address-add', [CustomerControllerApi::class, 'addAddress']); // Add address
        Route::patch('address-edit/{addressId}', [CustomerControllerApi::class, 'editAddress']); // Edit address
        Route::delete('address-delete/{addressId}', [CustomerControllerApi::class, 'deleteAddress']);
    });
});