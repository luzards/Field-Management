<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckInController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SopChecklistController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // Schedules
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::put('/schedules/{id}', [ScheduleController::class, 'update']);

    // Stores
    Route::get('/stores', [StoreController::class, 'index']);

    // Check-ins
    Route::post('/check-ins', [CheckInController::class, 'store']);
    Route::get('/check-ins', [CheckInController::class, 'index']);

    // News
    Route::get('/news', [NewsController::class, 'index']);
    Route::post('/news/refresh', [NewsController::class, 'refresh']);

    // SOP Checklists
    Route::get('/sop-checklists', [SopChecklistController::class, 'index']);
    Route::get('/sop-checklists/{id}', [SopChecklistController::class, 'show']);
    Route::post('/sop-checklists', [SopChecklistController::class, 'store']);
});
