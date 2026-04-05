<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Publisher;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publisher\LiveStatsController;
use App\Http\Controllers\Public\RatesController;

// Public pages
Route::get('/', fn() => view('public.home'))->name('home');
Route::get('/rates', [RatesController::class, 'index'])->name('rates');
Route::get('/privacy-policy', fn() => view('public.privacy'))->name('privacy');
Route::get('/terms-of-use', fn() => view('public.terms'))->name('terms');

// Click tracking
Route::get('/track/{code}', [TrackingController::class, 'track'])->name('track');

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Password reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendLink'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Admin & Manager panel
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,manager'])->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Publishers
    Route::prefix('publishers')->name('publishers.')->group(function () {
        Route::get('/', [Admin\PublisherController::class, 'index'])->name('index');
        Route::get('/{user}', [Admin\PublisherController::class, 'show'])->name('show');
        Route::get('/{user}/stats', [Admin\PublisherController::class, 'stats'])->name('stats');
        Route::post('/{user}/activate', [Admin\PublisherController::class, 'activate'])->name('activate');
        Route::post('/{user}/suspend', [Admin\PublisherController::class, 'suspend'])->name('suspend');
        Route::post('/{user}/divider', [Admin\PublisherController::class, 'updateDivider'])->name('update-divider');
        Route::post('/{user}/test-results', [Admin\PublisherController::class, 'updateTestResults'])->name('test-results');
        Route::post('/{user}/payment-status', [Admin\PublisherController::class, 'updatePaymentStatus'])->name('payment-status');
        Route::post('/{user}/fraud-settings', [Admin\PublisherController::class, 'updateFraudSettings'])->name('fraud-settings');
        Route::post('/{user}/tags', [Admin\PublisherController::class, 'addTag'])->name('tags.add');
        Route::delete('/{user}/tags', [Admin\PublisherController::class, 'removeTag'])->name('tags.remove');
        Route::delete('/{user}', [Admin\PublisherController::class, 'destroy'])->name('destroy');
    });

    // Contracts
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [Admin\ContractController::class, 'index'])->name('index');
        Route::post('/publisher/{user}/offer', [Admin\ContractController::class, 'offer'])->name('offer');
        Route::post('/{contract}/expire', [Admin\ContractController::class, 'expire'])->name('expire');
    });

    // Settings (admin only)
    Route::prefix('settings')->name('settings.')->middleware('role:admin')->group(function () {
        Route::get('/', [Admin\SettingsController::class, 'index'])->name('index');
        Route::put('/', [Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/test-email', [Admin\SettingsController::class, 'testEmail'])->name('test-email');
    });

    // Managers (admin only)
    Route::prefix('managers')->name('managers.')->middleware('role:admin')->group(function () {
        Route::get('/', [Admin\ManagerController::class, 'index'])->name('index');
        Route::get('/create', [Admin\ManagerController::class, 'create'])->name('create');
        Route::post('/', [Admin\ManagerController::class, 'store'])->name('store');
        Route::get('/{user}/permissions', [Admin\ManagerController::class, 'editPermissions'])->name('permissions');
        Route::put('/{user}/permissions', [Admin\ManagerController::class, 'updatePermissions'])->name('update-permissions');
        Route::delete('/{user}', [Admin\ManagerController::class, 'destroy'])->name('destroy');
    });

    // Tracking domains
    Route::prefix('tracking-domains')->name('tracking-domains.')->group(function () {
        Route::get('/', [Admin\TrackingDomainController::class, 'index'])->name('index');
        Route::post('/', [Admin\TrackingDomainController::class, 'store'])->name('store');
        Route::post('/{trackingDomain}/toggle', [Admin\TrackingDomainController::class, 'toggle'])->name('toggle');
        Route::delete('/{trackingDomain}', [Admin\TrackingDomainController::class, 'destroy'])->name('destroy');
    });

    // Tracking links
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('/', [Admin\TrackingLinkController::class, 'index'])->name('index');
        Route::get('/create', [Admin\TrackingLinkController::class, 'create'])->name('create');
        Route::post('/', [Admin\TrackingLinkController::class, 'store'])->name('store');
        Route::get('/{trackingLink}/edit', [Admin\TrackingLinkController::class, 'edit'])->name('edit');
        Route::put('/{trackingLink}', [Admin\TrackingLinkController::class, 'update'])->name('update');
        Route::delete('/{trackingLink}', [Admin\TrackingLinkController::class, 'destroy'])->name('destroy');
        Route::post('/{trackingLink}/toggle', [Admin\TrackingLinkController::class, 'toggle'])->name('toggle');
    });

    // Country rates
    Route::prefix('rates')->name('rates.')->group(function () {
        Route::get('/', [Admin\CountryRateController::class, 'index'])->name('index');
        Route::post('/', [Admin\CountryRateController::class, 'store'])->name('store');
        Route::put('/{countryRate}', [Admin\CountryRateController::class, 'update'])->name('update');
        Route::delete('/{countryRate}', [Admin\CountryRateController::class, 'destroy'])->name('destroy');
        Route::post('/bulk', [Admin\CountryRateController::class, 'bulkStore'])->name('bulk');
    });

    // Withdrawals
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [Admin\WithdrawalController::class, 'index'])->name('index');
        Route::post('/threshold', [Admin\WithdrawalController::class, 'updateThreshold'])->name('threshold');
        Route::post('/{withdrawal}/approve', [Admin\WithdrawalController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [Admin\WithdrawalController::class, 'reject'])->name('reject');
    });

    // Fraud alerts
    Route::prefix('fraud')->name('fraud.')->group(function () {
        Route::get('/', [Admin\FraudAlertController::class, 'index'])->name('index');
        Route::get('/publisher/{user}', [Admin\FraudAlertController::class, 'show'])->name('show');
        Route::post('/{fraudAlert}/resolve', [Admin\FraudAlertController::class, 'resolve'])->name('resolve');
        Route::post('/resolve-all', [Admin\FraudAlertController::class, 'resolveAll'])->name('resolve-all');
    });

    // Ad presets
    Route::prefix('ad-presets')->name('ad-presets.')->group(function () {
        Route::get('/', [Admin\AdPresetController::class, 'index'])->name('index');
        Route::post('/', [Admin\AdPresetController::class, 'store'])->name('store');
        Route::put('/{adPreset}', [Admin\AdPresetController::class, 'update'])->name('update');
        Route::delete('/{adPreset}', [Admin\AdPresetController::class, 'destroy'])->name('destroy');
    });

    // Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [Admin\SupportController::class, 'index'])->name('index');
        Route::get('/{supportTicket}', [Admin\SupportController::class, 'show'])->name('show');
        Route::post('/{supportTicket}/reply', [Admin\SupportController::class, 'reply'])->name('reply');
        Route::post('/{supportTicket}/close', [Admin\SupportController::class, 'close'])->name('close');
    });
});

// Publisher panel
Route::prefix('publisher')->name('publisher.')->middleware(['auth', 'role:publisher'])->group(function () {
    Route::get('/dashboard', [Publisher\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [Publisher\StatsController::class, 'index'])->name('stats');
    Route::get('/adcode', [Publisher\AdCodeController::class, 'index'])->name('adcode');
    Route::post('/adcode/select', [Publisher\AdCodeController::class, 'selectPreset'])->name('adcode.select');

    Route::post('/contract/{contract}/accept', [Publisher\ContractController::class, 'accept'])->name('contract.accept');
    Route::post('/contract/{contract}/reject', [Publisher\ContractController::class, 'reject'])->name('contract.reject');

    Route::get('/withdrawals', [Publisher\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [Publisher\WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/withdrawals/save-address', [Publisher\WithdrawalController::class, 'saveAddress'])->name('withdrawals.save-address');

    // Live stats JSON endpoint for real-time click counter
    Route::get('/live-stats', [LiveStatsController::class, 'index'])->name('live-stats');

    // Profile
    Route::get('/profile', [Publisher\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [Publisher\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [Publisher\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [Publisher\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/avatar/remove', [Publisher\ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    Route::get('/support', [Publisher\SupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [Publisher\SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [Publisher\SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{supportTicket}', [Publisher\SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{supportTicket}/reply', [Publisher\SupportController::class, 'reply'])->name('support.reply');
});
