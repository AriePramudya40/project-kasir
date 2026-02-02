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
    
    // POS
    Route::get('/dashboard', [PosController::class, 'index']);
    Route::post('/transaksi/bayar', [PosController::class, 'bayar']);
    
    // Redirect home ke dashboard
    Route::get('/', function() { return redirect('/dashboard'); });
});
