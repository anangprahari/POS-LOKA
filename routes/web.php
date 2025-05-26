<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/login');
});
Auth::routes();

// Route untuk mengecek role dan mengarahkan ke halaman yang sesuai
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('home');
        } else {
            return redirect()->route('user.dashboard');
        }
    })->name('dashboard');
});

// Admin routes (restricted to admin role)
Route::prefix('admin')->middleware(['auth', 'role.admin'])->group(function () {
    Route::get('/export/low-stock-products', [HomeController::class, 'exportLowStockProducts'])->name('export.low-stock-products');
    Route::get('/export/hot-products', [HomeController::class, 'exportHotProducts'])->name('export.hot-products');
    Route::get('/export/hot-products-year', [HomeController::class, 'exportHotProductsYear'])->name('export.hot-products-year');
    Route::get('/export/best-selling-products', [HomeController::class, 'exportBestSellingProducts'])->name('export.best-selling-products');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/export-details', [OrderController::class, 'exportDetails'])->name('orders.export-details');
    Route::resource('orders', OrderController::class)->except(['store', 'show']);
    Route::get('/suppliers/export', [App\Http\Controllers\SupplierController::class, 'export'])->name('suppliers.export');
    Route::resource('suppliers', SupplierController::class);
    Route::get('/orders/{id}/print', [OrderController::class, 'printInvoice'])->name('orders.print');
    Route::post('/orders/partial-payment', [OrderController::class, 'partialPayment'])->name('orders.partial-payment');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class)->except(['show']);

    // Admin POS access
    Route::get('/pos', [CartController::class, 'index'])->name('admin.pos.index');
});

// User routes (untuk halaman yang bisa diakses role user)
Route::prefix('user')->middleware(['auth', 'role.user'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('user.dashboard');



    // User POS access
    Route::get('/pos', [CartController::class, 'index'])->name('user.pos.index');
});

// Shared routes (bisa diakses kedua role)
Route::middleware('auth')->group(function () {
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::resource('customers', CustomerController::class);
    Route::get('/pos', [CartController::class, 'index'])->name('pos.index');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty'])->name('cart.change-qty');
    Route::delete('/cart/delete', [CartController::class, 'delete'])->name('cart.delete');
    Route::post('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/pos/products', [PosController::class, 'getProducts'])->name('pos.products');
    Route::get('/pos/recent-transactions', [PosController::class, 'getRecentTransactions'])->name('pos.recent');
    Route::get('/orders/recent', [OrderController::class, 'recent'])->name('orders.recent');
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.cart.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/locale/{type}', function ($type) {
        $translations = trans($type);
        return response()->json($translations);
    });
});
