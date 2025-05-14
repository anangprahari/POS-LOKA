<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PosController; // Tambahkan controller baru untuk POS
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
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::get('/orders/{id}/print', [OrderController::class, 'printInvoice'])->name('orders.print');
    Route::post('/orders/partial-payment', [OrderController::class, 'partialPayment'])->name('orders.partial-payment');
});

// User routes (untuk halaman yang bisa diakses role user)
Route::prefix('user')->middleware(['auth', 'role.user'])->group(function () {
    Route::get('/', [HomeController::class, 'userDashboard'])->name('user.dashboard');

    // POS routes untuk user
    Route::get('/pos', [PosController::class, 'index'])->name('user.pos.index');
    Route::get('/pos/products', [PosController::class, 'getProducts'])->name('user.pos.products');
    Route::get('/pos/recent-transactions', [PosController::class, 'getRecentTransactions'])->name('user.pos.recent');
});

// Shared routes (bisa diakses kedua role)
Route::middleware('auth')->group(function () {
    // Fitur yang dapat diakses oleh kedua role
    Route::resource('customers', CustomerController::class);

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);

    // Purchase route
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.cart.index');

    // Shared Orders routes
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/recent', [OrderController::class, 'recent'])->name('orders.recent');

    // Shared Products routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Translations route for React component
    Route::get('/locale/{type}', function ($type) {
        $translations = trans($type);
        return response()->json($translations);
    });
});
