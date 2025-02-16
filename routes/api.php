<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gateaway\CallBackController;

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

    // Route::post('/payment/callback/fib', [CallBackController::class, 'handleFIBCallback'])
    // ->middleware('throttle:60,1')
    // ->name('payment.callback.fib');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
