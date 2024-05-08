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

    Route::match(['get', 'post'] ,'deposit', [TransactionController::class, 'deposit'])->name('deposit');
    Route::match(['get', 'post'] ,'withdraw', [TransactionController::class, 'withdraw'])->name('withdraw');

});

