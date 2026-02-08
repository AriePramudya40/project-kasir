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

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');
    
    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Password Set (Google User)
    Route::get('/auth/set-password', [AuthController::class, 'showSetPassword'])->name('auth.set-password');
    Route::post('/auth/set-password', [AuthController::class, 'processSetPassword'])->name('auth.set-password.process');
    
    // POS Dashboard
    Route::get('/dashboard', [PosController::class, 'index'])->name('dashboard');
    
    // Product Management (Tambah, Update, Delete)
    Route::post('/produk/tambah', [ProductController::class, 'store'])->name('produk.store');
    Route::put('/produk/update/{id}', [ProductController::class, 'update'])->name('produk.update'); // BARU
    Route::delete('/produk/hapus/{id}', [ProductController::class, 'destroy'])->name('produk.destroy'); // BARU
    
    // Transaksi
    Route::post('/transaksi/bayar', [PosController::class, 'bayar'])->name('transaksi.bayar');
    Route::get('/transaksi/struk/{id}', [PosController::class, 'cetakStruk'])->name('transaksi.struk');
});

// Webhook Midtrans
Route::post('/midtrans/callback', [PosController::class, 'callback'])->name('midtrans.callback');