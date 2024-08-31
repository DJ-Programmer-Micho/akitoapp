<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Main\BusinessController;
use App\Http\Middleware\LocaleRedirectMiddleware;
use App\Http\Middleware\LocalizationMainMiddleware;
use App\Http\Controllers\SuperAdmin\SuperAdminController;

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


// Route::middleware([LocaleRedirectMiddleware::class])->group(function () {
// ADMIN
    Route::prefix('{locale}/super-admin')->middleware(['LocalizationMainMiddleware'])->group(function () {
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
    });
// HOME
    Route::prefix('{locale}')->middleware(['LocalizationMainMiddleware'])->group(function () {
        Route::get('/', [BusinessController::class, 'home'])->name('business.home');
        Route::get('shop', [BusinessController::class, 'productShop'])->name('business.productShop');
        Route::get('categories', [BusinessController::class, 'productCategory'])->name('business.category');
        Route::get('brands', [BusinessController::class, 'productBrand'])->name('business.brand');
        Route::get('spare', [BusinessController::class, 'productShopSpare'])->name('business.productShopSpare');
        Route::get('product/{slug}', [BusinessController::class, 'productDetail'])->name('business.productDetail');
    });
    

   
// });