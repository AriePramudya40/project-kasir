<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

// Guest (Belum Login)
Route::middleware('guest')->group(function() {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister']);
    
    // Google
    Route::get('/auth/google', [AuthController::class, 'googleRedirect']);
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
});

// Auth (Sudah Login)
Route::middleware('auth')->group(function() {
    Route::get('/logout', [AuthController::class, 'logout']);
    
    // --- TAMBAHAN: KHUSUS ADMIN SET PASSWORD ---
    Route::get('/auth/set-password', [AuthController::class, 'showSetPassword'])->name('auth.set-password');
    Route::post('/auth/set-password', [AuthController::class, 'processSetPassword']);
    
    // POS
    Route::get('/dashboard', [PosController::class, 'index']);
    Route::post('/transaksi/bayar', [PosController::class, 'bayar']);

    // Pastikan kata keduanya adalah 'cetakStruk', BUKAN 'struk'
    Route::get('/transaksi/struk/{id}', [PosController::class, 'cetakStruk'])->name('transaksi.struk');
    
    
    // Redirect home ke dashboard
    Route::get('/', function() { return redirect('/dashboard'); });
});

// Route Khusus Webhook Midtrans (Jangan dimasukkan ke dalam middleware auth!)
Route::post('/midtrans/callback', [PosController::class, 'callback']);
