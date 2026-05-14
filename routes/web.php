<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login'); //added redirect to login --- custom
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Stripe checkout success and cancel callbacks
use App\Http\Controllers\PaymentController;

Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

// Keep old routes for backward compatibility if needed, but pointing to the new controller
Route::get('/payment-success', [PaymentController::class, 'success']);
Route::get('/payment-cancel', [PaymentController::class, 'cancel']);

require __DIR__ . '/auth.php';

// custom route file
require __DIR__ . '/backend_farhad.php';

// custom route file
require __DIR__ . '/frontend_abdullah.php';
