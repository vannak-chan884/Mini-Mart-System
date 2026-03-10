<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\UserController;

// ── Authentication (public) ──────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',   [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // rate limit: 5 attempts per minute
    Route::post('logout',  [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me',       [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
    Route::put('password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum'); // NEW
});

// ── All authenticated routes ─────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Products
    Route::get('products/low-stock', [ProductController::class, 'lowStock']);
    Route::get('products/search',    [ProductController::class, 'search']);
    Route::apiResource('products',   ProductController::class);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // POS
    Route::prefix('pos')->group(function () {
        Route::get('products',              [PosController::class, 'products']);    // NEW — lightweight
        Route::get('cart',                  [PosController::class, 'cart']);
        Route::post('cart/add',             [PosController::class, 'addToCart']);
        Route::post('cart/update',          [PosController::class, 'updateCart']);
        Route::post('cart/remove',          [PosController::class, 'removeFromCart']);
        Route::post('cart/clear',           [PosController::class, 'clearCart']);
        Route::post('checkout/cash',        [PosController::class, 'checkoutCash']);
        Route::post('checkout/khqr',        [PosController::class, 'generateKhqr']);
        Route::post('checkout/khqr/verify', [PosController::class, 'verifyKhqr']);
    });

    // Sales
    Route::post('sales',       [SaleController::class, 'store']);     // NEW — direct sale
    Route::get('sales/export', [SaleController::class, 'export']);
    Route::apiResource('sales', SaleController::class)->except(['store', 'create', 'edit']);

    // Expenses
    Route::get('expenses/summary', [ExpenseController::class, 'summary']);
    Route::get('expenses/monthly', [ExpenseController::class, 'monthly']);
    Route::apiResource('expenses', ExpenseController::class);

    // Expense Categories
    Route::apiResource('expense-categories', ExpenseCategoryController::class);

    // Users
    Route::get('users/me/permissions',      [UserController::class, 'myPermissions']);
    Route::get('users/{user}/permissions',  [UserController::class, 'permissions']);       // NEW
    Route::put('users/{user}/permissions',  [UserController::class, 'updatePermissions']); // NEW
    Route::apiResource('users',             UserController::class);
});