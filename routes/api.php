<?php

use App\Http\Controllers\API\CurrentLocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\RideController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\RideRequestController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\DriverReviewController;
use App\Http\Controllers\API\DriverController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  
Route::post('register', [RegisterController::class, 'register']);
Route::post('register-admin', [RegisterController::class, 'registerAdmin']);
Route::post('send-sms-code',function (){
    $response = [
        'success' => true,
        'data'    => 123,
        'message' => 12312,
    ];

    return response()->json($response, 200);
});
Route::post('login', [RegisterController::class, 'login']);

     Route::post('verify-user',[RegisterController::class,'verify_user'])->middleware('auth:api');
     Route::post('resend-verify-code',[RegisterController::class,'resend_verify_code'])->middleware('auth:api');
Route::middleware('auth:api')->group( function () {
Route::post('logout-user', [RegisterController::class, 'logout_user']);
    Route::resource('rides', RideController::class)->middleware('verified');
    Route::post('update-user', [RegisterController::class,'update_user'])->middleware('verified');
    Route::resource('transactions', TransactionController::class)->middleware('verified');
    Route::resource('notifications', NotificationController::class)->middleware('verified');
    Route::resource('payment-methods', PaymentMethodController::class)->middleware('verified');
    Route::get('make-default-payment', [PaymentMethodController::class,'make_default'])->middleware('verified');
    Route::get('payment-methods-default', [PaymentMethodController::class,'get_default'])->middleware('verified');
    Route::resource('ride-requests', RideRequestController::class)->middleware('verified');
    Route::post('send-ride-requests',[RideRequestController::class,'store'])->middleware('verified');
    Route::get('ride-requests-ride',[RideRequestController::class,'show_for_ride'])->middleware('verified');
    Route::resource('driver-reviews', DriverReviewController::class)->middleware('verified');
    Route::get('ride-request-accept/{request_id}', [RideController::class, 'acceptRequest'])->middleware('verified');
    Route::get('get-ride-calculations', [RideController::class, 'getCalculations'])->middleware('verified');

    Route::post('start-ride', [RideController::class, 'startRide'])->middleware('verified');
    Route::post('pause-ride', [RideController::class, 'pauseRide'])->middleware('verified');
    Route::post('resume-ride', [RideController::class, 'resumeRide'])->middleware('verified');
    Route::post('end-ride', [RideController::class, 'endRide'])->middleware('verified');
    Route::post('save-location', [CurrentLocationController::class, 'update'])->middleware('verified');
    Route::post('save-location-passenger', [CurrentLocationController::class, 'update_passenger'])->middleware('verified');
    Route::post('current-location', [CurrentLocationController::class, 'fetch'])->middleware('verified');
    Route::post('get-current-ride-driver', [CurrentLocationController::class, 'get_current_ride_driver'])->middleware('verified');
    Route::post('add-driver-info', [DriverController::class, 'updateDriverInfo'])->middleware('verified');


    Route::get('current-user-dashboard', [DashboardController::class, 'index'])->middleware('verified');

    

});