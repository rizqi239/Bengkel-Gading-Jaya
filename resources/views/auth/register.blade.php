<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register Account - Bengkel GMJ Management System">
    <title>Daftar Akun Baru - Bengkel GMJ</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite & CDN) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen relative flex items-center justify-center p-4 sm:p-8 text-white overflow-x-hidden selection:bg-orange-500 selection:text-white" style="background: #090d16 url('{{ asset('images/bengkel_bg.jpg') }}') no-repeat center center fixed; background-size: cover;">

    <!-- Static Dark Overlay -->
    <div class="fixed inset-0 z-0 bg-slate-950/85 pointer-events-none"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-6xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-16 py-6">

        <!-- Left Column: Branding & Welcome Message -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between space-y-8 text-left">
            <!-- Brand Icon/Logo Header -->
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 flex items-center justify-center shadow-lg shadow-orange-500/20">
                    <i data-lucide="wrench" class="h-5 w-5 text-white"></i>
                </div>
                <span class="text-sm font-bold tracking-wider text-slate-200 uppercase">Bengkel Gading Jaya</span>
            </div>

            <!-- Welcome Title with Accent Line -->
            <div class="space-y-4 pt-2">
                <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-tight">
                    Join Us!
                </h1>
                <div class="w-16 h-1 bg-gradient-to-r from-orange-500 to-rose-500 rounded-full"></div>
                <p class="text-sm sm:text-base text-slate-300/90 max-w-md font-normal leading-relaxed pt-2">
                    Daftarkan akun baru untuk bergabung dalam Sistem Manajemen Bengkel Gading Jaya Motor. Kelola operasional servis, stok, dan kasir dengan mudah.
                </p>
            </div>

            <!-- Call to Action Button -->
            <div class="pt-2">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-orange-500 via-rose-500 to-amber-500 px-7 py-3 text-xs font-bold text-white shadow-xl shadow-orange-500/25">
                    <span>Sudah Punya Akun? Login</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
        </div>

        <!-- Right Column: Fast Glassmorphism Register Card -->
        <div class="w-full lg:w-5/12 max-w-md">
            <div class="rounded-3xl bg-slate-900/90 border border-white/10 p-7 sm:p-9 shadow-2xl shadow-black/80 space-y-5 relative overflow-hidden">

                <!-- Sign Up Heading -->
                <div class="text-left space-y-1">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">
                        Sign <span class="border-b-2 border-orange-500 pb-0.5">up</span>
                    </h2>
                    <p class="text-xs text-slate-400 font-medium">Buat akun baru untuk mengakses sistem</p>
                </div>

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="flex items-start gap-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 p-3.5 text-rose-200 text-xs font-medium">
                        <i data-lucide="alert-circle" class="h-4 w-4 text-rose-300 shrink-0 mt-0.5"></i>
                        <div class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Register Form -->
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Full Name Field -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-semibold text-slate-300">Nama Lengkap</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="user" class="h-4 w-4"></i>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500"
                                placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-300">Alamat Email</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="mail" class="h-4 w-4"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <!-- Role Select Field -->
                    <div class="space-y-1.5">
                        <label for="role" class="block text-xs font-semibold text-slate-300">Peran / Role Akun</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="shield" class="h-4 w-4"></i>
                            </div>
                            <select id="role" name="role" required
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500 cursor-pointer">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }} class="bg-slate-900 text-slate-400">-- Pilih Peran / Role --</option>
                                <option value="Mekanik" {{ old('role') == 'Mekanik' ? 'selected' : '' }} class="bg-slate-900 text-white">🔧 Mekanik / Teknisi Bengkel</option>
                                <option value="Pemilik Toko" {{ old('role') == 'Pemilik Toko' ? 'selected' : '' }} class="bg-slate-900 text-white">👑 Pemilik Toko / Owner</option>
                                <option value="Kasir" {{ old('role') == 'Kasir' ? 'selected' : '' }} class="bg-slate-900 text-white">💼 Kasir / Operasional</option>
                            </select>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="lock" class="h-4 w-4"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500"
                                placeholder="Minimal 6 karakter">
                        </div>
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300">Konfirmasi Password</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="check-circle" class="h-4 w-4"></i>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500"
                                placeholder="Ulangi password">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full rounded-full py-3 px-6 text-sm font-bold text-white bg-gradient-to-r from-orange-500 via-rose-500 to-amber-500 hover:from-orange-600 hover:via-rose-600 hover:to-amber-600 shadow-lg shadow-orange-500/25 cursor-pointer mt-2">
                        Daftar Akun
                    </button>

                    <!-- Divider / Pemisah -->
                    <div class="relative flex items-center justify-center my-4">
                        <div class="border-t border-slate-700/80 w-full"></div>
                        <span class="bg-slate-900 px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-400 shrink-0">atau</span>
                        <div class="border-t border-slate-700/80 w-full"></div>
                    </div>

                    <!-- Tombol Google Sign-In -->
                    <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center gap-3 rounded-full py-2.5 px-5 text-xs font-bold text-slate-200 bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 transition-all shadow-md">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.2 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 10.8 0 12s.7 2.3 1.9 4.7l3.7-2.9z"/>
                            <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.2-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                        </svg>
                        <span>Daftar dengan Google</span>
                    </a>

                    <!-- Link Back to Login -->
                    <div class="text-center pt-2">
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-orange-400 hover:text-orange-300">
                            Sudah punya akun? <span class="underline font-bold">Login di sini</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
