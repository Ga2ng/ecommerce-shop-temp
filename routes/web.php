<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

// News (public)
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Product Catalog Routes
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{product:slug}', [ProductController::class, 'show'])->name('catalog.show');

// Payment Routes (Guest checkout allowed)
Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/checkout', [PaymentController::class, 'processCheckout'])->name('payment.process');
Route::get('/payment/{order}', [PaymentController::class, 'payment'])->name('payment.payment');
Route::get('/payment/{order}/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/{order}/failed', [PaymentController::class, 'failed'])->name('payment.failed');

// Webhook Route (no auth required)
Route::post('/webhook/midtrans', [WebhookController::class, 'handle'])->name('payment.webhook');

// API Routes for Regions
Route::prefix('api')->group(function () {
    Route::get('/provinces', [App\Http\Controllers\Api\RegionController::class, 'provinces'])->name('api.provinces');
    Route::get('/cities/{provinceId}', [App\Http\Controllers\Api\RegionController::class, 'cities'])->name('api.cities');
    Route::get('/districts/{cityId}', [App\Http\Controllers\Api\RegionController::class, 'districts'])->name('api.districts');
    Route::get('/villages/{districtId}', [App\Http\Controllers\Api\RegionController::class, 'villages'])->name('api.villages');
    
    // Shipping API
    Route::post('/shipping/calculate', [App\Http\Controllers\Api\ShippingController::class, 'calculateCost'])->name('api.shipping.calculate');
    Route::post('/shipping/city-id', [App\Http\Controllers\Api\ShippingController::class, 'getCityId'])->name('api.shipping.getCityId');
    Route::get('/shipping/couriers', [App\Http\Controllers\Api\ShippingController::class, 'getCouriers'])->name('api.shipping.couriers');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    
    // Products Management
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    
    // News Management
    Route::resource('news', App\Http\Controllers\Admin\NewsController::class);

    // Transaksi berhasil (dashboard analisis)
    Route::get('transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::post('transactions/{transaction}/send-receipt', [App\Http\Controllers\Admin\TransactionReceiptController::class, 'send'])->name('transactions.send-receipt');
});

require __DIR__.'/auth.php';
