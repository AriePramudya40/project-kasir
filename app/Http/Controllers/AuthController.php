<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // --- HALAMAN VIEW ---
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    // --- PROSES REGISTER ---
    // --- PROSES REGISTER (UPDATED) ---
    public function processRegister(Request $request) {
        // 1. Validasi Input (Wajib isi role)
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,kasir' // Hanya boleh pilih admin atau kasir
        ]);

        // 2. Simpan ke Database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role // <-- Ambil langsung dari pilihan user
        ]);

        return redirect('/login')->with('success', 'Registrasi Berhasil! Silakan Login.');
    }

    // --- PROSES LOGIN MANUAL ---
    public function processLogin(Request $request) {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/dashboard');
        }
        return back()->with('error', 'Email atau Password salah!');
    }

    // --- PROSES LOGIN GOOGLE ---
    public function googleRedirect() {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback() {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // 1. Cari apakah email ini sudah ada di database?
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // A. JIKA SUDAH ADA (Login)
                // Update Google ID-nya biar sinkron, lalu login
                $user->update(['google_id' => $googleUser->getId()]);
                Auth::login($user);
                return redirect('/dashboard');
            } else {
                // B. JIKA BELUM ADA (Register Otomatis)
                // Kita buatkan akun baru
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'role' => 'kasir',     // Default jadi Kasir
                    'password' => null,    // Tidak punya password (login harus pakai Google terus)
                ]);

                // Langsung login
                Auth::login($newUser);
                return redirect('/dashboard')->with('success', 'Akun berhasil dibuat otomatis!');
            }

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal Login Google. Error: ' . $e->getMessage());
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
}