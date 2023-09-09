<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('welcome');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
        Route::get('/', [RoleController::class,'index'])->name('index')->middleware('permission:role_index');
        Route::get('/create', [RoleController::class,'create'])->name('create')->middleware('permission:role_create');
        Route::post('/create', [RoleController::class,'store'])->name('store')->middleware('permission:role_create');
        Route::get('/edit/{id}', [RoleController::class,'edit'])->name('edit')->middleware('permission:role_edit');
        Route::post('/update/{id}', [RoleController::class,'update'])->name('update')->middleware('permission:role_edit');
        Route::post('/delete/{id}', [RoleController::class,'delete'])->name('delete')->middleware('permission:role_delete');

        Route::get('/{id}/permissions', 'Permissions\RolesController@rolePermissions')->name('permissions')->middleware('permission:permissions_to_role');
        Route::post('/{id}/permissions-update', 'Permissions\RolesController@updateRolePermissions')->name('updateRolePermissions')->middleware('permission:permissions_to_role');
    });

    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', [UserController::class,'index'])->name('index')/*->middleware('permission:user_index')*/;
        Route::get('/create', [UserController::class,'create'])->name('create')/*->middleware('permission:user_create')*/;
        Route::post('/store', [UserController::class,'store'])->name('store')/*->middleware('permission:user_create')*/;
        Route::get('/edit/{id}', [UserController::class,'edit'])->name('edit')/*->middleware('permission:user_edit')*/;
        Route::post('/update/{id}', [UserController::class,'update'])->name('update')/*->middleware('permission:user_edit')*/;
        Route::post('/delete/{id}', [UserController::class,'delete'])->name('delete')/*->middleware('permission:user_delete')*/;

        Route::post('/{id}/change-status', [UserController::class,'changeStatus'])->name('status')/*->middleware('permission:user_status')*/;
    });

    Route::group(['prefix' => 'devices', 'as' => 'devices.'], function () {
        Route::get('/', [DeviceController::class,'index'])->name('index')/*->middleware('permission:device_index')*/;
        Route::get('/create', [DeviceController::class,'create'])->name('create')/*->middleware('permission:device_create')*/;
        Route::post('/store', [DeviceController::class,'store'])->name('store')/*->middleware('permission:device_create')*/;
        Route::get('/edit/{id}', [DeviceController::class,'edit'])->name('edit')/*->middleware('permission:device_edit')*/;
        Route::get('/show/{id}', [DeviceController::class,'show'])->name('show')/*->middleware('permission:device_show')*/;
        Route::post('/update', [DeviceController::class,'update'])->name('update')/*->middleware('permission:device_edit')*/;
        Route::post('/delete/{id}', [DeviceController::class,'delete'])->name('delete')/*->middleware('permission:device_delete')*/;

        Route::post('/{id}/change-status', [DeviceController::class,'changeStatus'])->name('status')/*->middleware('permission:device_status')*/;
        Route::get('/country/city', [DeviceController::class,'getCityByCountryId'])->name('country.city')/*->middleware('permission:device_status')*/;
        Route::get('/temp', [DeviceController::class,'getDeviceTempAvailable'])->name('temp')/*->middleware('permission:device_status')*/;
    });
});
