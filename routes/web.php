<?php

use App\Http\Controllers\Admin\ClosingReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── DASHBOARD ────────────────────────────────────────────────
    Route::get('/', [DashboardController::class, 'dashboard'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    // ── CATEGORIES ───────────────────────────────────────────────
    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // ── PRODUCTS ─────────────────────────────────────────────────
    // Trash / restore / force-delete MUST come before resource()
    Route::get('products/trash', [ProductController::class, 'trash'])
        ->name('products.trash')
        ->middleware('permission:products.view');

    Route::post('products/{id}/restore', [ProductController::class, 'restore'])
        ->name('products.restore')
        ->middleware('permission:products.edit');

    Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDestroy'])
        ->name('products.forceDestroy')
        ->middleware('permission:products.delete');

    Route::resource('products', ProductController::class)
        ->only(['index', 'show'])
        ->middleware('permission:products.view');

    Route::resource('products', ProductController::class)
        ->only(['create', 'store'])
        ->middleware('permission:products.create');

    Route::resource('products', ProductController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:products.edit');

    Route::resource('products', ProductController::class)
        ->only(['destroy'])
        ->middleware('permission:products.delete');

    // ── POS ──────────────────────────────────────────────────────
    Route::middleware('permission:pos.view')->group(function () {
        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('pos/add', [PosController::class, 'addToCart'])->name('pos.add');
        Route::post('pos/update', [PosController::class, 'updateCart'])->name('pos.update');
        Route::post('pos/remove', [PosController::class, 'removeFromCart'])->name('pos.remove');
        Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('pos/find-by-barcode', [PosController::class, 'findByBarcode'])->name('pos.findByBarcode');
        Route::get('pos-cart-data', fn () => response()->json(session('cart', [])));
        Route::get('pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::get('pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::post('pos/generate-khqr', [PosController::class, 'generateKhqr'])->name('pos.generateKhqr');
        Route::post('pos/verify-khqr', [PosController::class, 'verifyKhqr'])->name('pos.verifyKhqr');

        // ABA PayWay
        Route::post('pos/payway/generate', [PosController::class, 'generatePayway'])->name('pos.payway.generate');
        Route::get('pos/payway/callback', [PosController::class, 'paywayCallback'])->name('pos.payway.callback');
        Route::post('pos/payway/verify', [PosController::class, 'verifyPayway'])->name('pos.payway.verify');
    });

    // ── SALES ────────────────────────────────────────────────────
    Route::middleware('permission:sales.view')->group(function () {
        Route::resource('sales', SaleController::class)->only(['index', 'show', 'destroy']);
    });

    // ── EXPENSES ─────────────────────────────────────────────────
    // Custom route MUST come before resource()
    Route::get('expenses/report/monthly', [ExpenseController::class, 'monthlyReport'])
        ->name('expenses.monthlyReport')
        ->middleware('permission:expenses.view');

    Route::resource('expenses', ExpenseController::class)
        ->only(['index', 'show'])
        ->middleware('permission:expenses.view');

    Route::resource('expenses', ExpenseController::class)
        ->only(['create', 'store'])
        ->middleware('permission:expenses.create');

    Route::resource('expenses', ExpenseController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:expenses.edit');

    Route::resource('expenses', ExpenseController::class)
        ->only(['destroy'])
        ->middleware('permission:expenses.delete');

    // ── EXPENSE CATEGORIES ───────────────────────────────────────
    Route::resource('expense-categories', ExpenseCategoryController::class)
        ->only(['index', 'show'])
        ->middleware('permission:expense_categories.view');

    Route::resource('expense-categories', ExpenseCategoryController::class)
        ->only(['create', 'store'])
        ->middleware('permission:expense_categories.create');

    Route::resource('expense-categories', ExpenseCategoryController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:expense_categories.edit');

    Route::resource('expense-categories', ExpenseCategoryController::class)
        ->only(['destroy'])
        ->middleware('permission:expense_categories.delete');

    // ── PERMISSIONS (admin only) ─────────────────────────────────
    Route::middleware('permission:users.view')->group(function () {
        Route::get('permissions', [RolePermissionController::class, 'index'])->name('permissions.index');
        Route::post('permissions/update', [RolePermissionController::class, 'update'])->name('permissions.update');
    });

    // ── USERS ────────────────────────────────────────────────────
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
        Route::post('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
        Route::resource('users', UserController::class);
    });

    // ── ACTIVITY LOGS ────────────────────────────────────────────
    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index')
        ->middleware('permission:users.view');

    // ── CLOSING REPORTS ──────────────────────────────────────────
    Route::middleware('permission:expenses.view')->group(function () {
        Route::get('closing-reports', [ClosingReportController::class, 'index'])->name('closing-reports.index');
        Route::get('closing-reports/{closingReport}', [ClosingReportController::class, 'show'])->name('closing-reports.show');
        Route::post('closing-reports/trigger', [ClosingReportController::class, 'trigger'])->name('closing-reports.trigger');
        Route::post('closing-reports/{closingReport}/resend-telegram', [ClosingReportController::class, 'resendTelegram'])->name('closing-reports.resend-telegram');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';