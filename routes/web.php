<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\StoreManagementController;
use App\Http\Controllers\Admin\ScheduleManagementController;
use App\Http\Controllers\Admin\CheckInManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SopReportController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

// Admin Auth
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes
Route::prefix('admin')->middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // AM Users
    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    // Stores
    Route::get('/stores', [StoreManagementController::class, 'index'])->name('admin.stores.index');
    Route::get('/stores/create', [StoreManagementController::class, 'create'])->name('admin.stores.create');
    Route::post('/stores', [StoreManagementController::class, 'store'])->name('admin.stores.store');
    Route::get('/stores/{id}/edit', [StoreManagementController::class, 'edit'])->name('admin.stores.edit');
    Route::put('/stores/{id}', [StoreManagementController::class, 'update'])->name('admin.stores.update');
    Route::delete('/stores/{id}', [StoreManagementController::class, 'destroy'])->name('admin.stores.destroy');

    // Schedules
    Route::get('/schedules', [ScheduleManagementController::class, 'index'])->name('admin.schedules.index');
    Route::get('/schedules/create', [ScheduleManagementController::class, 'create'])->name('admin.schedules.create');
    Route::post('/schedules', [ScheduleManagementController::class, 'store'])->name('admin.schedules.store');
    Route::get('/schedules/{id}/edit', [ScheduleManagementController::class, 'edit'])->name('admin.schedules.edit');
    Route::put('/schedules/{id}', [ScheduleManagementController::class, 'update'])->name('admin.schedules.update');
    Route::delete('/schedules/{id}', [ScheduleManagementController::class, 'destroy'])->name('admin.schedules.destroy');

    // Check-ins
    Route::get('/check-ins', [CheckInManagementController::class, 'index'])->name('admin.checkins.index');
    Route::get('/check-ins/{id}', [CheckInManagementController::class, 'show'])->name('admin.checkins.show');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');

    // SOP Reports
    Route::get('/sop-reports', [SopReportController::class, 'index'])->name('admin.sop-reports.index');
    Route::get('/sop-reports/{id}', [SopReportController::class, 'show'])->name('admin.sop-reports.show');
});
