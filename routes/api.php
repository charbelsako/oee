<?php

use App\Http\Controllers\Api\DeviceController;
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

Route::post('/store', [DeviceController::class,'store'])->name('post.store');
Route::post('/store/device', [DeviceController::class,'storeDevice'])->name('post.store.device');
// @TODO: May not be needed
Route::post('/device/status', [DeviceController::class,'deviceStatus'])->name('post.device.status');
Route::post('/microtime', [DeviceController::class,'microtime'])->name('get.microtime');
