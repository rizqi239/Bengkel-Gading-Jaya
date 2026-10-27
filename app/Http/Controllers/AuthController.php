<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Coba autentikasi standar via Database
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email/Username atau password yang Anda masukkan tidak cocok.',
        ])->onlyInput('email');
    }

    public function redirectToGoogle()
    {
        $redirectUrl = config('services.google.redirect') ?: url('/auth/google/callback');

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $redirectUrl = config('services.google.redirect') ?: url('/auth/google/callback');
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->user();
        } catch (\Throwable $exception) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login dengan Google gagal. Silakan coba lagi.',
            ]);
        }

        $email = Str::lower(trim((string) $googleUser->getEmail()));

        if ($email === '') {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google tidak memberikan alamat email yang valid.',
            ]);
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user) {
            $request->session()->forget('google_registration');
            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('google_registration', [
            'email' => $email,
            'name' => trim((string) $googleUser->getName()) ?: Str::before($email, '@'),
        ]);

        return redirect()->route('register');
    }

    /**
     * Tampilkan halaman registrasi.
     */
    public function showRegisterForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register', [
            'googleRegistration' => $request->session()->get('google_registration'),
        ]);
    }

    /**
     * Proses pendaftaran akun baru.
     */
    public function register(Request $request)
    {
        $googleRegistration = $request->session()->get('google_registration');

        if (is_array($googleRegistration)) {
            $validated = $request->validate([
                'role' => ['required', 'string', 'in:Mekanik,Pemilik Toko,Kasir'],
            ], [
                'role.required' => 'Peran (Role) akun wajib dipilih.',
                'role.in' => 'Pilihan peran (Role) tidak valid.',
            ]);

            $email = Str::lower(trim((string) ($googleRegistration['email'] ?? '')));
            $name = trim((string) ($googleRegistration['name'] ?? ''));

            if ($email === '' || $name === '') {
                $request->session()->forget('google_registration');

                return redirect()->route('register')->withErrors([
                    'email' => 'Data akun Google tidak valid. Silakan mulai pendaftaran Google kembali.',
                ]);
            }

            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if (! $user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'role' => $validated['role'],
                    'password' => Hash::make(Str::random(64)),
                ]);
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user, true);
            $request->session()->forget('google_registration');
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('status', 'Pendaftaran dengan Google berhasil!');
        }

        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'in:Mekanik,Pemilik Toko,Kasir'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Peran (Role) akun wajib dipilih.',
            'role.in' => 'Pilihan peran (Role) tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Pendaftaran akun berhasil!');
    }

    /**
     * Tampilkan halaman lupa password.
     */
    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    /**
     * Proses reset/perbarui password bagi pengguna yang lupa.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Akun dengan alamat email ini tidak ditemukan.',
            ])->onlyInput('email');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Password akun Anda berhasil diperbarui! Silakan masuk menggunakan password baru.');
    }

    /**
     * Logout pengguna dan arahkan langsung ke halaman login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }
}
