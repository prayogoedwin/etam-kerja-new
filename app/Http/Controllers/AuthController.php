<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\RecaptchaV3;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // buat file auth/login.blade.php
    }

    public function login(Request $request)
    {

        // dd([
        // 'input' => $request->captcha,
        // 'session' => session('captcha'),
        // 'match' => captcha_check($request->captcha)
        // ]);

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|captcha', // Validasi captcha
            // 'captcha' => ['required', function ($attribute, $value, $fail) {
            // if (!captcha_check($value)) {
            //     $fail('Captcha tidak valid.');
            // }
            'recaptcha_token' => ['required', new RecaptchaV3(0.5)],  // v3
        ]);

        // Debug auth attempt
        // $user = \App\Models\User::where('email', $request->username)->first();

        // dd([
        //     'input_username' => $request->username,
        //     'input_password' => $request->password,
        //     'user_found' => $user ? true : false,
        //     'user_email' => $user?->email,
        //     'user_password_hash' => $user?->password,
        //     'password_check' => $user ? \Hash::check($request->password, $user->password) : false,
        // ]);

        // Jika validasi captcha dan kredensial login berhasil
        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            // return redirect()->route('dashboard'); // Ganti dengan rute yang sesuai

            $user = Auth::user();
            $userId = $user->id;
            $role = $user->roles->first()?->name;

            // ===== ROLE-BASED CHECK =====
            switch ($role) {

                case 'pencari-kerja':
                    $exists = \App\Models\UserPencari::where('name', $userId)->exists();
                    if ($exists) {
                        Auth::logout();
                        return back()->withErrors(['login_error' => 'Proses registrasi akun pencari kerja anda belum selesai, masuk ke menu daftar untuk menyelesaikan pendaftaran.']);
                    }
                    break;

                case 'penyedia-kerja':
                    $exists = \App\Models\UserPenyedia::where('name', $userId)->exists();
                    if ($exists) {
                        Auth::logout();
                        return back()->withErrors(['login_error' => 'Proses registrasi akun perusahaan anda belum selesai, masuk ke menu daftar untuk menyelesaikan pendaftaran.']);
                    }
                    break;

                case 'admin-bkk':
                    $exists = \App\Models\UserBkk::where('name', $userId)->exists();
                    if ($exists) {
                        // $rl = encode_url('admin-bkk');
                        // return redirect()
                        // ->to('/depan/daftar?rl=' . $rl)
                        // ->with('info', 'Akun BKK Anda sudah terdaftar. Silakan selesaikan proses pendaftaran.');
                        Auth::logout();
                        return back()->withErrors(['login_error' => 'Proses registrasi akun BKK anda belum selesai, masuk ke menu daftar untuk menyelesaikan pendaftaran.']);
                    }
                    break;
            }

            // Redirect berdasarkan role
            return match ($user->roles[0]['name']) {
                'eksekutif-provinsi' => redirect()->route('dashboard.eksekutif'),
                'eksekutif-kabkota' => redirect()->route('dashboard.eksekutif.kabkota'),
                default => redirect()->route('dashboard'),
            };

        }

        return back()->withErrors(['login_error' => 'Invalid credentials or captcha']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}


?>
