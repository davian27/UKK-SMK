<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Auth::routes();



// Route::middleware('auth', 'role:admin')->group(function () {
// });

Route::middleware('auth')->group(function () {
    Route::resource('users', CustomerController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('items', ItemController::class);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Route::prefix('transactions')->group(function () {
    Route::patch('/{transaction}/acc', [AccController::class, 'acc'])->name('transactions.acc');
    Route::put('/{transaction}/reject', [AccController::class, 'reject'])->name('transactions.reject');
});

Route::get('/', function () {
    return redirect()->route('login');
});


