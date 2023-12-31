<?php

use App\Http\Controllers\account\AuthController;
use App\Http\Controllers\merchant\MerchantAuthController;
use App\Http\Controllers\merchant\OrderController;
use App\Http\Controllers\merchant\ProductController;
use App\Http\Controllers\merchant\VariatonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//for merchant registration and login
Route::group(['prefix' => 'merchant'], function () {
        Route::post('/register',[MerchantAuthController::class,'register']);
        Route::post('/login',[MerchantAuthController::class,'login']);
        Route::post('/logout', [MerchantAuthController::class, 'logout'])->middleware('auth:sanctum');

        // create product
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        //place order
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

        // New route for getting order history
        Route::post('/order-history', [OrderController::class, 'getOrderHistory']);
});

//for merchant's product variation
Route::group(['prefix' => 'variation'], function () {
        // color route
        Route::get('/colors', [VariatonController::class, 'color_index']);
        Route::get('/colors/{id}', [VariatonController::class, 'color_show']);

        // unit route
        Route::get('/units', [VariatonController::class, 'unit_index']);
        Route::get('/units/{id}', [VariatonController::class, 'unit_show']);
});





