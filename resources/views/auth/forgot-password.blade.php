<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset Password - Bengkel GMJ Management System">
    <title>Lupa Password - Bengkel GMJ</title>

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

            <!-- Reset Title with Accent Line -->
            <div class="space-y-4 pt-2">
                <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-tight">
                    Reset Password
                </h1>
                <div class="w-16 h-1 bg-gradient-to-r from-orange-500 to-rose-500 rounded-full"></div>
                <p class="text-sm sm:text-base text-slate-300/90 max-w-md font-normal leading-relaxed pt-2">
                    Lupa password akun Anda? Masukkan alamat email terdaftar dan buat password baru secara mandiri.
                </p>
            </div>

            <!-- Call to Action Button -->
            <div class="pt-2">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-orange-500 via-rose-500 to-amber-500 px-7 py-3 text-xs font-bold text-white shadow-xl shadow-orange-500/25">
                    <span>Sudah Ingat? Login</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
        </div>

        <!-- Right Column: Fast Glassmorphism Forgot Password Card -->
        <div class="w-full lg:w-5/12 max-w-md">
            <div class="rounded-3xl bg-slate-900/90 border border-white/10 p-7 sm:p-9 shadow-2xl shadow-black/80 space-y-5 relative overflow-hidden">

                <!-- Reset Heading -->
                <div class="text-left space-y-1">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">
                        Reset <span class="border-b-2 border-orange-500 pb-0.5">Password</span>
                    </h2>
                    <p class="text-xs text-slate-400 font-medium">Masukkan email dan password baru Anda</p>
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

                <!-- Forgot Password Form -->
                <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-300">Alamat Email Akun</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="mail" class="h-4 w-4"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <!-- Password Baru Field -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password Baru</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="lock" class="h-4 w-4"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500"
                                placeholder="Minimal 6 karakter">
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300">Konfirmasi Password Baru</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="check-circle" class="h-4 w-4"></i>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full rounded-full bg-slate-800/80 border border-slate-700/60 pl-11 pr-4 py-2.5 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 focus:border-orange-500"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full rounded-full py-3 px-6 text-sm font-bold text-white bg-gradient-to-r from-orange-500 via-rose-500 to-amber-500 hover:from-orange-600 hover:via-rose-600 hover:to-amber-600 shadow-lg shadow-orange-500/25 cursor-pointer mt-2">
                        Simpan Password Baru
                    </button>

                    <!-- Link Back to Login -->
                    <div class="text-center pt-2">
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-orange-400 hover:text-orange-300">
                            Kembali ke <span class="underline font-bold">Halaman Login</span>
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
