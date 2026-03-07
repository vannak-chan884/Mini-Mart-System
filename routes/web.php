<?php

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
    return view('welcome');
});


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    // POS Routes
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/add', [PosController::class, 'addToCart'])->name('pos.add');
    Route::post('pos/update', [PosController::class, 'updateCart'])->name('pos.update');
    Route::post('pos/remove', [PosController::class, 'removeFromCart'])->name('pos.remove');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // ABA PayWay Routes
    Route::post('/pos/payway/generate',  [PosController::class, 'generatePayway'])->name('pos.payway.generate');
    Route::get('/pos/payway/callback',   [PosController::class, 'paywayCallback'])->name('pos.payway.callback');
    Route::post('/pos/payway/verify',    [PosController::class, 'verifyPayway'])->name('pos.payway.verify');

    Route::get('pos-cart-data', function () {
        return response()->json(session('cart', []));
    });
    Route::get('pos/search', [PosController::class, 'search'])->name('pos.search');

    Route::get('pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');

    Route::post('pos/generate-khqr', [PosController::class, 'generateKhqr'])->name('pos.generateKhqr');

    Route::post('pos/verify-khqr', [PosController::class, 'verifyKhqr'])->name('pos.verifyKhqr');

    Route::resource('sales', SaleController::class)->only(['index', 'show', 'destroy']);

    // Expense Routes
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show', 'destroy']);
    Route::resource('expense-categories', ExpenseCategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show', 'destroy']);

    Route::get('expenses/report/monthly', [ExpenseController::class, 'monthlyReport'])->name('expenses.monthlyReport');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
