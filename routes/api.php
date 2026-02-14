<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes with API Key protection
Route::middleware('api.key')->group(function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    
    // Public schedule search
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
});

// Protected routes with API Key + Bearer Token
Route::middleware(['api.key', 'auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
});

// Admin routes
Route::middleware(['api.key', 'auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/orders', [AdminController::class, 'getAllOrders']);
    Route::post('/orders/{id}/confirm', [AdminController::class, 'confirmOrder']);
    Route::get('/schedules', [AdminController::class, 'getAllSchedules']);
    Route::post('/schedules', [AdminController::class, 'storeSchedule']);
    Route::put('/schedules/{id}', [AdminController::class, 'updateSchedule']);
    Route::delete('/schedules/{id}', [AdminController::class, 'deleteSchedule']);
    Route::get('/buses', [AdminController::class, 'getAllBuses']);
    Route::get('/routes', [AdminController::class, 'getAllRoutes']);
});
