<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubscribeController;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [AuthController::class, 'register'])->name('api.register');
Route::post('user/login', [AuthController::class, 'login'])->name('api.login');
Route::get('user/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('user/resend-verification', [AuthController::class, 'resendVerification'])->name('verification.resend');


Route::middleware('auth:api')->group(function () {
	Route::get('user/me', [AuthController::class, 'me'])->name('api.me');
	Route::post('user/logout', [AuthController::class, 'logout'])->name('api.logout');
	Route::post('user/refresh', [AuthController::class, 'refresh'])->name('api.refresh');
});

Route::middleware('auth:api')->group(function () {
    // subscription routes
	Route::get('/plans', [SubscribeController::class, 'plans'])->name('api.plans');
	Route::post('/subscribe', [SubscribeController::class, 'subscribe'])->name('api.subscribe');
	Route::post('/cancel-subscription', [SubscribeController::class, 'cancel'])->name('api.cancel_subscription');
	Route::post('/change-plan', [SubscribeController::class, 'changePlan'])->name('api.change_plan');
	Route::get('/subscription-status', [SubscribeController::class, 'status'])->name('api.subscription_status');
});



Route::post('/stripe/webhook', [\Laravel\Cashier\Http\Controllers\WebhookController::class, '__invoke']);
