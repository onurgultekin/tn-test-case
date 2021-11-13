<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\SubscriptionController;

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
Route::post('/register', [DeviceController::class, 'register']);

Route::post('/purchase', [DeviceController::class, 'purchase']);

Route::post('/mock-google', [DeviceController::class, 'mockGoogle']);

Route::post('/mock-apple', [DeviceController::class, 'mockApple']);

Route::post('/check-subscription', [SubscriptionController::class, 'check']);

Route::post('/report', [SubscriptionController::class, 'report']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
