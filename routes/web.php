<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Auth::routes();



Route::middleware('auth', 'role:admin')->group(function () {
    
});

Route::middleware('auth')->group(function () {
    Route::resource('transactions', TransactionController::class);
    Route::resource('items', ItemController::class);
    Route::resource('users', CustomerController::class);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::put('/transactions.acc', [App\Http\Controllers\TransactionController::class]);
});


