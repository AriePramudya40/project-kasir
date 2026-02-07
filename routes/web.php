<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Authentication
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');
    
    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Set Password (untuk user dari Google OAuth)
    Route::get('/auth/set-password', [AuthController::class, 'showSetPassword'])->name('auth.set-password');
    Route::post('/auth/set-password', [AuthController::class, 'processSetPassword'])->name('auth.set-password.process');
    
    // Dashboard & POS
    Route::get('/dashboard', [PosController::class, 'index'])->name('dashboard');
    
    // Product Management
    Route::post('/produk/tambah', [ProductController::class, 'store'])->name('produk.store');
    
    // Transactions
    Route::post('/transaksi/bayar', [PosController::class, 'bayar'])->name('transaksi.bayar');
    Route::get('/transaksi/struk/{id}', [PosController::class, 'cetakStruk'])->name('transaksi.struk');
});

/*
|--------------------------------------------------------------------------
| Webhook Routes (Tanpa Authentication)
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/callback', [PosController::class, 'callback'])->name('midtrans.callback');