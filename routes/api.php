<?php

use App\Http\Controllers\account\AuthController;
use App\Http\Controllers\merchant\MerchantAuthController;
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

//for user registration and login
Route::group(['prefix' => 'account'], function () {
    Route::post('/register',[AuthController::class,'register']);
});

//for merchant registration and login
Route::group(['prefix' => 'merchant'], function () {
    Route::post('/register',[MerchantAuthController::class,'register']);
    Route::post('/login',[MerchantAuthController::class,'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});