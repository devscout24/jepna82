<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BulkPackageBuyController;
use App\Http\Controllers\Api\ContractAnalyzerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LandingPageController;

use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\UserSubscriptionController;
use Illuminate\Support\Facades\Route;


Route::post('/forgot-password/request-otp', [AuthController::class, 'requestPasswordResetOtp']);

Route::post('/forget/password', [AuthController::class, 'forgetPassword']);
Route::post('/check-otp', [AuthController::class, 'checkOtp']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPasswordWithOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::post('/resend-otp', [AuthController::class, 'resendOtp']);



Route::controller(PackageController::class)->group(function () {
    Route::get('package/subscription/list', 'subscriptionList');
    Route::get('credits/plans', 'creditPlansList');
    Route::get('basic/plan/list', 'basicPlansList');
    Route::get('free/plan/list', 'freePlanList');
});

Route::controller(LandingPageController::class)->group(function () {
    Route::get('landing/page/data', 'getLandingPageData');
    Route::get('landing/page/banner', 'getBannerData');
    Route::get('landing/page/feature', 'getFeatureData');
});







Route::middleware('auth:api')->group(function () {

    Route::get('/dashboard/data', [DashboardController::class, 'index']);

    Route::controller(UserSubscriptionController::class)->group(function () {

        Route::post('user/subscription/purchase', 'store');
        Route::put('user/subscription/{id}', 'update');
        Route::delete('user/subscription/{id}', 'destroy');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('change-password', 'changePassword');
        Route::post('update-profile', 'updateProfile');
        Route::get('profile', 'profile');
    });

    Route::controller(BulkPackageBuyController::class)->group(function () {
        Route::post('bulk-package/buy', 'store');
        Route::post('bulk-package/one-time-topup', 'oneTimeTopUp');
    });



    Route::controller(ContractAnalyzerController::class)->group(function () {
        Route::post('contract/analyze', 'analyze');
        Route::get('user/contract/data', 'UserContract');
        Route::get('contract/paywall/{id}', 'contractResult');
        Route::get('contract/results/{id}', 'getAnalysisResult');
        Route::post('contract/unlock/{id}', 'unlockContract');
    });
});


Route::post('subscription/webhook', [UserSubscriptionController::class, 'handlesubscription']);
//
