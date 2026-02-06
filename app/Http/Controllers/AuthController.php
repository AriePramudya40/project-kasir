<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // --- VIEW ---
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    // --- REGISTER MANUAL ---
    public function processRegister(Request $request) {
        $request->validate([
            'name' => 'required',
            'username' => 'required|alpha_dash|min:3',
            'password' => 'required|min:6',
            'role' => 'required|in:kasir'
        ]);

        $dummyEmail = $request->username . '@kasir.lokal';

        if (User::where('email', $dummyEmail)->exists()) {
            return back()->withErrors(['username' => 'Username sudah terpakai.']);
        }

        User::create([
            'name' => $request->name,
            'email' => $dummyEmail,
            'password' => Hash::make($request->password),
            'role' => 'kasir'
        ]);

        return redirect('/login')->with('success', 'Registrasi Berhasil! Silakan Login.');
    }

    // --- LOGIN MANUAL (TANPA OTP) ---
    public function processLogin(Request $request) {
        $input = $request->login_id;
        $password = $request->password;
        
        // Deteksi Email vs Username
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $input, 'password' => $password];
        } else {
            $credentials = ['email' => $input . '@kasir.lokal', 'password' => $password];
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Redirect ke Dashboard dengan Pesan Selamat Datang
            return redirect('/dashboard')->with('login_success', "Selamat Datang, {$user->name}!");
        }
        
        return back()->with('error', 'Username/Email atau Password salah!');
    }

    // --- GOOGLE REDIRECT ---
    public function googleRedirect(Request $request) {
        if ($request->has('role')) {
            session(['register_role' => $request->role]);
        }
        return Socialite::driver('google')->redirect();
    }

    // --- GOOGLE CALLBACK (LANGSUNG LOGIN) ---
    public function googleCallback() {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            // 1. USER LAMA (LOGIN)
            if ($user) {
                if ($user->role === 'admin' && !$user->password) {
                     Auth::login($user); return redirect('/auth/set-password');
                }
                // Cek apakah Admin punya password (Login manual wajib)
                if ($user->role !== 'kasir' && $user->password) {
                    return redirect('/login')->with('error', 'Admin harap login manual.');
                }
                
                if (!$user->google_id) { $user->update(['google_id' => $googleUser->getId()]); }

                // LOGIN LANGSUNG
                Auth::login($user);
                return redirect('/dashboard')->with('login_success', "Selamat Datang kembali, {$user->name}!");
            } 
            
            // 2. USER BARU (REGISTER)
            else {
                $targetRole = session('register_role');
                if ($targetRole && in_array($targetRole, ['admin', 'kasir'])) {
                    $newUser = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'role' => $targetRole,
                        'password' => null
                    ]);
                    session()->forget('register_role');
                    Auth::login($newUser);

                    if ($targetRole === 'admin') {
                        return redirect('/auth/set-password');
                    }

                    // LOGIN LANGSUNG
                    return redirect('/dashboard')->with('login_success', "Selamat Datang, {$newUser->name}!");
                } else {
                    return redirect('/login')->with('error', 'Akun belum terdaftar.');
                }
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal Login Google.');
        }
    }

    public function logout() { Auth::logout(); return redirect('/login'); }
    
    // (Fungsi Set Password Admin biarkan tetap ada)
    public function showSetPassword() { return view('auth.set-password'); }
    public function processSetPassword(Request $request) {
        $request->validate(['password' => 'required|min:6|confirmed']);
        $user = User::find(Auth::id());
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect('/dashboard')->with('login_success', 'Password dibuat. Selamat Datang!');
    }
}