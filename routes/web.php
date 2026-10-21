<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProductInvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/products/{product}/invoice', [ProductInvoiceController::class, 'show'])
    ->name('products.invoice');
Route::get('/products/{product}/invoice/download', [ProductInvoiceController::class, 'download'])
    ->name('products.invoice.download');

Route::resource('products', ProductController::class)->except('destroy');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [
            AdminController::class,
            'dashboard',
        ])->name('admin.dashboard');
    });

    // Khusus Kasir
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir/dashboard', [
            KasirController::class,
            'dashboard',
        ])->name('kasir.dashboard');
    });

});
