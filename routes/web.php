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

    // ── DASHBOARD (always visible to any logged-in admin) ────────
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ── CATEGORIES ───────────────────────────────────────────────
    Route::resource('categories', CategoryController::class)
        ->middleware('permission:categories.view');

    // ── PRODUCTS ─────────────────────────────────────────────────
    // Custom routes MUST come before resource()
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
        ->only(['index'])
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
    Route::get('pos', [PosController::class, 'index'])
        ->name('pos.index')
        ->middleware('permission:pos.view');

    Route::post('pos/add', [PosController::class, 'addToCart'])
        ->name('pos.add')
        ->middleware('permission:pos.view');

    Route::post('pos/update', [PosController::class, 'updateCart'])
        ->name('pos.update')
        ->middleware('permission:pos.view');

    Route::post('pos/remove', [PosController::class, 'removeFromCart'])
        ->name('pos.remove')
        ->middleware('permission:pos.view');

    Route::post('pos/checkout', [PosController::class, 'checkout'])
        ->name('pos.checkout')
        ->middleware('permission:pos.view');

    Route::get('pos/find-by-barcode', [PosController::class, 'findByBarcode'])
        ->name('pos.findByBarcode')
        ->middleware('permission:pos.view');

    Route::get('pos-cart-data', fn () => response()->json(session('cart', [])))
        ->middleware('permission:pos.view');

    Route::get('pos/search', [PosController::class, 'search'])
        ->name('pos.search')
        ->middleware('permission:pos.view');

    Route::get('pos/receipt/{sale}', [PosController::class, 'receipt'])
        ->name('pos.receipt')
        ->middleware('permission:pos.view');

    Route::post('pos/generate-khqr', [PosController::class, 'generateKhqr'])
        ->name('pos.generateKhqr')
        ->middleware('permission:pos.view');

    Route::post('pos/verify-khqr', [PosController::class, 'verifyKhqr'])
        ->name('pos.verifyKhqr')
        ->middleware('permission:pos.view');

    // ABA PayWay
    Route::post('pos/payway/generate', [PosController::class, 'generatePayway'])
        ->name('pos.payway.generate')
        ->middleware('permission:pos.view');

    Route::get('pos/payway/callback', [PosController::class, 'paywayCallback'])
        ->name('pos.payway.callback')
        ->middleware('permission:pos.view');

    Route::post('pos/payway/verify', [PosController::class, 'verifyPayway'])
        ->name('pos.payway.verify')
        ->middleware('permission:pos.view');

    // ── SALES ────────────────────────────────────────────────────
    Route::patch('sales/{sale}/delivery', [SaleController::class, 'updateDelivery'])
    ->name('sales.updateDelivery');

    Route::patch('sales/{sale}/status', [SaleController::class, 'updateStatus'])
    ->name('sales.updateStatus');
    Route::resource('sales', SaleController::class)
        ->only(['index', 'show', 'destroy'])
        ->middleware('permission:sales.view');

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
    Route::get('permissions', [RolePermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('permission:users.view');

    Route::post('permissions/update', [RolePermissionController::class, 'update'])
        ->name('permissions.update')
        ->middleware('permission:users.view');

    // ── USERS ────────────────────────────────────────────────────
    Route::get('users/{user}/permissions', [UserController::class, 'permissions'])
        ->name('users.permissions')
        ->middleware('permission:users.view');

    Route::post('users/{user}/permissions', [UserController::class, 'updatePermissions'])
        ->name('users.permissions.update')
        ->middleware('permission:users.view');

    Route::resource('users', UserController::class)
        ->middleware('permission:users.view');

    // ── ACTIVITY LOGS ────────────────────────────────────────────
    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index')
        ->middleware('permission:users.view');

    // ── CLOSING REPORTS ──────────────────────────────────────────
    Route::get('closing-reports', [ClosingReportController::class, 'index'])
        ->name('closing-reports.index')
        ->middleware('permission:expenses.view');

    Route::get('closing-reports/{closingReport}', [ClosingReportController::class, 'show'])
        ->name('closing-reports.show')
        ->middleware('permission:expenses.view');

    Route::post('closing-reports/trigger', [ClosingReportController::class, 'trigger'])
        ->name('closing-reports.trigger')
        ->middleware('permission:expenses.view');

    Route::post('closing-reports/{closingReport}/resend-telegram', [ClosingReportController::class, 'resendTelegram'])
        ->name('closing-reports.resend-telegram')
        ->middleware('permission:expenses.view');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';