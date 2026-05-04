<?php

use App\Http\Controllers\Backend\Abdullah\BannerController;
use App\Http\Controllers\Backend\Abdullah\StatController;
use App\Http\Controllers\Backend\Abdullah\FeatureController;
use App\Http\Controllers\Backend\Abdullah\WorkController;
use App\Http\Controllers\Backend\Abdullah\FileController;
use App\Http\Controllers\Backend\Abdullah\ScanController;
use App\Http\Controllers\Backend\Abdullah\SubscribePlanController;
use App\Http\Controllers\MonthlySubscriptionController;
use Illuminate\Support\Facades\Route;




// banner routes
Route::get('/admin/banner/index', [BannerController::class, 'index'])->name('admin.banner.index');
Route::get('/admin/banner/create', [BannerController::class, 'create'])->name('admin.banner.create');
Route::post('/admin/banner/store', [BannerController::class, 'store'])->name('admin.banner.store');
Route::get('/admin/banner/edit/{id}', [BannerController::class, 'edit'])->name('admin.banner.edit');
Route::put('/admin/banner/update/{id}', [BannerController::class, 'update'])->name('admin.banner.update');
Route::delete('/admin/banner/destroy/{id}', [BannerController::class, 'destroy'])->name('admin.banner.destroy');


// stat routes
Route::get('/admin/stat/index', [StatController::class, 'index'])->name('admin.stat.index');
Route::get('/admin/stat/create', [StatController::class, 'create'])->name('admin.stat.create');
Route::post('/admin/stat/store', [StatController::class, 'store'])->name('admin.stat.store');
Route::get('/admin/stat/edit/{id}', [StatController::class, 'edit'])->name('admin.stat.edit');
Route::put('/admin/stat/update/{id}', [StatController::class, 'update'])->name('admin.stat.update');
Route::delete('/admin/stat/destroy/{id}', [StatController::class, 'destroy'])->name('admin.stat.destroy');


// feature routes
Route::get('/admin/feature/index', [FeatureController::class, 'index'])->name('admin.feature.index');
Route::get('/admin/feature/create', [FeatureController::class, 'create'])->name('admin.feature.create');
Route::post('/admin/feature/store', [FeatureController::class, 'store'])->name('admin.feature.store');
Route::get('/admin/feature/edit/{id}', [FeatureController::class, 'edit'])->name('admin.feature.edit');
Route::put('/admin/feature/update/{id}', [FeatureController::class, 'update'])->name('admin.feature.update');
Route::delete('/admin/feature/destroy/{id}', [FeatureController::class, 'destroy'])->name('admin.feature.destroy');


// work routes
Route::get('/admin/work/index', [WorkController::class, 'index'])->name('admin.work.index');
Route::get('/admin/work/create', [WorkController::class, 'create'])->name('admin.work.create');
Route::post('/admin/work/store', [WorkController::class, 'store'])->name('admin.work.store');
Route::get('/admin/work/edit/{id}', [WorkController::class, 'edit'])->name('admin.work.edit');
Route::put('/admin/work/update/{id}', [WorkController::class, 'update'])->name('admin.work.update');
Route::delete('/admin/work/destroy/{id}', [WorkController::class, 'destroy'])->name('admin.work.destroy');

// file routes
Route::get('/admin/file/index', [FileController::class, 'index'])->name('admin.file.index');
Route::get('/admin/file/create', [FileController::class, 'create'])->name('admin.file.create');
Route::post('/admin/file/store', [FileController::class, 'store'])->name('admin.file.store');
Route::get('/admin/file/edit/{id}', [FileController::class, 'edit'])->name('admin.file.edit');
Route::put('/admin/file/update/{id}', [FileController::class, 'update'])->name('admin.file.update');
Route::delete('/admin/file/destroy/{id}', [FileController::class, 'destroy'])->name('admin.file.destroy');

// scan routes
Route::get('/admin/scan/index', [ScanController::class, 'index'])->name('admin.scan.index');
Route::get('/admin/scan/create', [ScanController::class, 'create'])->name('admin.scan.create');
Route::post('/admin/scan/store', [ScanController::class, 'store'])->name('admin.scan.store');
Route::get('/admin/scan/edit/{id}', [ScanController::class, 'edit'])->name('admin.scan.edit');
Route::put('/admin/scan/update/{id}', [ScanController::class, 'update'])->name('admin.scan.update');
Route::delete('/admin/scan/destroy/{id}', [ScanController::class, 'destroy'])->name('admin.scan.destroy');


//subscription plan routes
Route::get('/admin/subscribe_plan/index', [SubscribePlanController::class, 'index'])->name('admin.subscribe_plan.index');
Route::get('/admin/subscribe_plan/create', [SubscribePlanController::class, 'create'])->name('admin.subscribe_plan.create');
Route::post('/admin/subscribe_plan/store', [SubscribePlanController::class, 'store'])->name('admin.subscribe_plan.store');
Route::get('/admin/subscribe_plan/edit/{id}', [SubscribePlanController::class, 'edit'])->name('admin.subscribe_plan.edit');
Route::put('/admin/subscribe_plan/update/{id}', [SubscribePlanController::class, 'update'])->name('admin.subscribe_plan.update');
Route::delete('/admin/subscribe_plan/destroy/{id}', [SubscribePlanController::class, 'destroy'])->name('admin.subscribe_plan.destroy');  


// monthly plan routes
Route::get('/admin/monthly_plan/index', [MonthlySubscriptionController::class, 'index'])->name('admin.monthly_plan.index');
Route::get('/admin/monthly_plan/create', [MonthlySubscriptionController::class, 'create'])->name('admin.monthly_plan.create');
Route::post('/admin/monthly_plan/store', [MonthlySubscriptionController::class, 'store'])->name('admin.monthly_plan.store');
Route::get('/admin/monthly_plan/edit/{id}', [MonthlySubscriptionController::class, 'edit'])->name('admin.monthly_plan.edit');
Route::put('/admin/monthly_plan/update/{id}', [MonthlySubscriptionController::class, 'update'])->name('admin.monthly_plan.update');
Route::delete('/admin/monthly_plan/destroy/{id}', [MonthlySubscriptionController::class, 'destroy'])->name('admin.monthly_plan.destroy');