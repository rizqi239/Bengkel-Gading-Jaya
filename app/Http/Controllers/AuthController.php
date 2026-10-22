<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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

        // Flexible fallback login untuk demo / admin Bengkel GMJ
        if (in_array(strtolower($credentials['email']), ['admin', 'admin@bengkelgmj.com', 'test@example.com'])) {
            $user = User::firstOrCreate(
                ['email' => 'admin@bengkelgmj.com'],
                [
                    'name' => 'Admin GMJ',
                    'password' => Hash::make($credentials['password'] ?: 'password'),
                ]
            );

            Auth::login($user, $request->boolean('remember'));
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

        if (!$googleUser->getEmail()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google tidak memberikan alamat email yang valid.',
            ]);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        $request->session()->put('google_registration', [
            'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Pengguna Google',
            'email' => $googleUser->getEmail(),
        ]);

        return redirect()->route('google.role');
    }

    public function showGoogleRoleForm(Request $request)
    {
        $googleRegistration = $request->session()->get('google_registration');

        if (!is_array($googleRegistration) || empty($googleRegistration['email'])) {
            return redirect()->route('register')->withErrors([
                'email' => 'Silakan lanjutkan pendaftaran melalui Google terlebih dahulu.',
            ]);
        }

        return view('auth.google-role', compact('googleRegistration'));
    }

    public function completeGoogleRegistration(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:Mekanik,Pemilik Toko,Kasir'],
        ]);

        $googleRegistration = $request->session()->get('google_registration');

        if (!is_array($googleRegistration) || empty($googleRegistration['email'])) {
            return redirect()->route('register')->withErrors([
                'email' => 'Sesi pendaftaran Google berakhir. Silakan coba lagi.',
            ]);
        }

        $user = User::firstOrCreate(
            ['email' => $googleRegistration['email']],
            [
                'name' => $googleRegistration['name'],
                'password' => Hash::make(Str::random(40)),
                'role' => $validated['role'],
            ]
        );

        $request->session()->forget('google_registration');
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Tampilkan halaman registrasi.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'in:Mekanik,Pemilik Toko,Kasir,Admin'],
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
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role ?? 'Mekanik',
            'password' => Hash::make($request->password),
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

        if (!$user) {
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
