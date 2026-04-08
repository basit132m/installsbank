<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/login',           [Api\AuthController::class, 'login']);
    Route::post('/forgot-password', [Api\AuthController::class, 'forgotPassword']);
});

// Authenticated publisher API
Route::middleware(['auth:sanctum', 'role:publisher'])->group(function () {
    Route::post('/auth/logout',      [Api\AuthController::class, 'logout']);
    Route::put('/device-token',      [Api\AuthController::class, 'updateDeviceToken']);

    Route::get('/dashboard',         [Api\DashboardController::class, 'index']);
    Route::get('/live-stats',        [Api\DashboardController::class, 'liveStats']);

    Route::get('/stats',             [Api\StatsController::class, 'index']);

    Route::get('/withdrawals',       [Api\WithdrawalController::class, 'index']);
    Route::post('/withdrawals',      [Api\WithdrawalController::class, 'store']);
    Route::post('/withdrawals/save-address', [Api\WithdrawalController::class, 'saveAddress']);

    Route::get('/profile',           [Api\ProfileController::class, 'show']);
    Route::put('/profile',           [Api\ProfileController::class, 'update']);
    Route::put('/profile/password',  [Api\ProfileController::class, 'updatePassword']);
    Route::post('/profile/avatar',   [Api\ProfileController::class, 'updateAvatar']);

    Route::get('/support',           [Api\SupportController::class, 'index']);
    Route::post('/support',          [Api\SupportController::class, 'store']);
    Route::get('/support/{ticket}',  [Api\SupportController::class, 'show']);
    Route::post('/support/{ticket}/reply', [Api\SupportController::class, 'reply']);

    Route::get('/chat/messages',     [Api\ChatController::class, 'messages']);
    Route::post('/chat/send',        [Api\ChatController::class, 'send']);
});
