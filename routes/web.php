<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// for admins
Route::get('user-form', [UserController::class, 'userForm'])->name('user-form');
Route::post('user', [UserController::class, 'user'])->name('user');

// for user login
Route::get('login', 'App\Http\Controllers\AuthController@login')->name('login');
Route::post('login_post', 'App\Http\Controllers\AuthController@login_post')->name('login_post');

Route::middleware(['isAuth'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [TransactionController::class, 'dashboard'])->name('dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('create', 'App\Http\Controllers\UserController@create')->name('create');
        Route::post('store', 'App\Http\Controllers\UserController@store')->name('store');
    });
});

