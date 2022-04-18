<?php

use Illuminate\Support\Facades\Route;
use Mariojgt\TheWatcher\Controllers\Auth\FrontAuth\ResetPassword;
use Mariojgt\TheWatcher\Controllers\Auth\FrontAuth\LoginController;
use Mariojgt\TheWatcher\Controllers\FrontEnd\UserDashboardController;
use Mariojgt\TheWatcher\Controllers\Auth\FrontAuth\RegisterController;
use Mariojgt\TheWatcher\Controllers\FrontEnd\User\StreamingController;

// Login | Register Route need to be logout to view this page
Route::group([
    'middleware' => ['web', 'guest'],
    'prefix'     => config('skeletonAdmin.route_prefix_front'),
], function () {
    // User Login
    Route::get('/login', [LoginController::class, 'index'])->name('login');

    // Do login
    Route::post('/login/user', [LoginController::class, 'login'])->name('login.user');

    // User Registration
    Route::get('/register', [RegisterController::class, 'register'])->name('register.user');
    Route::post('/register/user', [RegisterController::class, 'userRegister'])->name('register.user');

    // Password Reset
    Route::get('/forgot-password', [ResetPassword::class, 'index'])->name('forgot-password');
    Route::post('/password-reset', [ResetPassword::class, 'reset'])->name('password-reset.user');
    Route::get('/password-reset/{token}', [ResetPassword::class, 'passwordReset'])->name('password.reset');
    Route::post('/password-change', [ResetPassword::class, 'passwordChange'])->name('password.change.user');
});

// User verify account
Route::group([
    'middleware' => ['web'],
    'prefix'     => config('skeletonAdmin.route_prefix_front'),
], function () {
    // Warn the user need to be verify
    Route::get('/email/verify', [LoginController::class, 'needVerify'])->name('verification.notice');

    // Login to verify the user
    Route::get('/user/verify/{id}/{time}', [LoginController::class, 'verify'])->name('user.verify');
});

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix'     => config('skeletonAdmin.route_prefix_front'),
], function () {
    // Warn the user need to be verify
    Route::get('/user/home', [UserDashboardController::class, 'index'])->name('user.home');

    // User Streming
    Route::get('/streaming', [StreamingController::class, 'index'])->name('streaming.index');
    Route::get('/streaming/watch', [StreamingController::class, 'watch'])->name('streaming.watch');
    // Streaming data
    Route::post('/streaming/send', [StreamingController::class, 'reciveStreamingVideo'])->name('streaming.send');
});

Route::any('/pusher/auth', [StreamingController::class, 'pusherAuth']);
