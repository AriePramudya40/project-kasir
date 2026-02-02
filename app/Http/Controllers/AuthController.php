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
            
            // Cek apakah email sudah terdaftar?
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update ID Google
                $user->update(['google_id' => $googleUser->getId()]);
                Auth::login($user);
                return redirect('/dashboard');
            } else {
                return redirect('/login')->with('error', 'Email Google ini belum diregistrasi!');
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal Login Google.');
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
}