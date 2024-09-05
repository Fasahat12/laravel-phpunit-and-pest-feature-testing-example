<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');

Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])
    ->name('products');

    Route::middleware('is_admin')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products/create', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/edit/{product}', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/update/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/delete/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
