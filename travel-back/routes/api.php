<?php

use App\Http\Controllers\Api\Admin\TopBannerController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LogoController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;



// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated (any role)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Admin-only
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::patch('/users/{id}/restore', [UserController::class, 'restore']);
});

//google or facebook login route
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);


// Top banner routes
Route::get('top-banners', [TopBannerController::class, 'index']);
Route::get('top-banners/{topBanner}', [TopBannerController::class, 'show']);
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::get('top-banners', [TopBannerController::class, 'adminIndex']);
    Route::get('top-banners/{topBanner}', [TopBannerController::class, 'show']);
    Route::post('top-banners', [TopBannerController::class, 'store']);
    Route::put('top-banners/{topBanner}', [TopBannerController::class, 'update']);
    Route::delete('top-banners/{topBanner}', [TopBannerController::class, 'destroy']);
});


// Public: fetch the current logo
Route::get('logo', [LogoController::class, 'index']);

// Admin: create/update/delete the logo
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::post('logo', [LogoController::class, 'store']);
    Route::put('logo/{logo}', [LogoController::class, 'update']);
    Route::delete('logo/{logo}', [LogoController::class, 'destroy']);
});

