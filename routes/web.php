<?php

use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Route;
use  Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('migrate', function(){
    Artisan::call('migrate');
    Artisan::call('passport:install');
    return "Done Babes";
});

Route::get('/', function(){
    return view('welcome');
});

Route::get('not-verified', function(){
    return response()->json(['error'=>"User Not Verified"]) ;
})->name('verification.notice');

Route::get('not-loggedin', function(){
    return response()->json(['error'=>"User Not Logged In"]) ;
})->name('login');