<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Product Catalog Routes
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{product:slug}', [ProductController::class, 'show'])->name('catalog.show');

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
});

require __DIR__.'/auth.php';
