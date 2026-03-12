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
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
    Route::put('password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
});

// ── PUBLIC routes — no token needed ─────────────────────────────────
Route::get('products/low-stock', [ProductController::class, 'lowStock']);
Route::get('products/search', [ProductController::class, 'search']);
Route::get('products/{product}', [ProductController::class, 'show']);
Route::get('products', [ProductController::class, 'index']);

Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('categories', [CategoryController::class, 'index']);

// ── Authenticated routes — token required ────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Products (write operations only — read is public above)
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::patch('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    // Categories (write operations only)
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);
    Route::patch('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

    // POS
    Route::prefix('pos')->group(function () {
        Route::get('products', [PosController::class, 'products']);
        Route::get('cart', [PosController::class, 'cart']);
        Route::post('cart/add', [PosController::class, 'addToCart']);
        Route::post('cart/update', [PosController::class, 'updateCart']);
        Route::post('cart/remove', [PosController::class, 'removeFromCart']);
        Route::post('cart/clear', [PosController::class, 'clearCart']);
        Route::post('checkout/cash', [PosController::class, 'checkoutCash']);
        Route::post('checkout/khqr', [PosController::class, 'generateKhqr']);
        Route::post('checkout/khqr/verify', [PosController::class, 'verifyKhqr']);
    });

    // Sales
    Route::post  ('sales',               [SaleController::class, 'store']);
    Route::get   ('sales',               [SaleController::class, 'index']);
    Route::get   ('sales/export',        [SaleController::class, 'export']);

    // ✅ MUST be before sales/{sale}
    Route::post  ('sales/{sale}/status', [SaleController::class, 'updateStatus']);
    Route::patch ('sales/{sale}/status', [SaleController::class, 'updateStatus']);

    // These come AFTER
    Route::get   ('sales/{sale}',        [SaleController::class, 'show']);
    Route::put   ('sales/{sale}',        [SaleController::class, 'update']);
    Route::patch ('sales/{sale}',        [SaleController::class, 'update']);
    Route::delete('sales/{sale}',        [SaleController::class, 'destroy']);

    // Expenses
    Route::get('expenses/summary', [ExpenseController::class, 'summary']);
    Route::get('expenses/monthly', [ExpenseController::class, 'monthly']);
    Route::apiResource('expenses', ExpenseController::class);

    // Expense Categories
    Route::apiResource('expense-categories', ExpenseCategoryController::class);

    // Users
    Route::get('users/me/permissions', [UserController::class, 'myPermissions']);
    Route::get('users/{user}/permissions', [UserController::class, 'permissions']);
    Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions']);
    Route::apiResource('users', UserController::class);

    Route::delete('auth/account', [AuthController::class, 'deleteAccount']);

    // Customer Checkout
    Route::post('checkout/khqr', [App\Http\Controllers\Api\CheckoutController::class, 'generateKhqr']);
    Route::post('checkout/khqr/verify', [App\Http\Controllers\Api\CheckoutController::class, 'verifyKhqr']);
});