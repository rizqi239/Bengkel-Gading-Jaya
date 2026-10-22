<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pilih peran akun Bengkel GMJ">
    <title>Pilih Peran Akun - Bengkel GMJ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2 { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-full flex items-center justify-center bg-slate-950 p-4 text-white" style="background: #090d16 url('{{ asset('images/bengkel_bg.jpg') }}') no-repeat center center fixed; background-size: cover;">
    <div class="fixed inset-0 bg-slate-950/85"></div>
    <main class="relative z-10 w-full max-w-lg rounded-3xl border border-white/10 bg-slate-900/95 p-7 shadow-2xl sm:p-9">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold tracking-tight">Pilih Peran Akun</h1>
            <p class="mt-2 text-sm text-slate-400">Akun Google: <span class="text-slate-200">{{ $googleRegistration['email'] }}</span></p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-500/40 bg-rose-500/15 p-3 text-sm text-rose-200">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('google.role.store') }}" method="POST" class="space-y-3">
            @csrf
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-700 bg-slate-800/70 p-4 transition hover:border-orange-500/70">
                <input type="radio" name="role" value="Pemilik Toko" required @checked(old('role') === 'Pemilik Toko') class="h-4 w-4 accent-orange-500">
                <span>
                    <span class="block text-sm font-semibold">Pemilik Toko</span>
                    <span class="mt-1 block text-xs text-slate-400">Akses untuk mengelola operasional bengkel.</span>
                </span>
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-700 bg-slate-800/70 p-4 transition hover:border-orange-500/70">
                <input type="radio" name="role" value="Mekanik" required @checked(old('role') === 'Mekanik') class="h-4 w-4 accent-orange-500">
                <span>
                    <span class="block text-sm font-semibold">Mekanik</span>
                    <span class="mt-1 block text-xs text-slate-400">Akses untuk tugas dan aktivitas servis.</span>
                </span>
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-700 bg-slate-800/70 p-4 transition hover:border-orange-500/70">
                <input type="radio" name="role" value="Kasir" required @checked(old('role') === 'Kasir') class="h-4 w-4 accent-orange-500">
                <span>
                    <span class="block text-sm font-semibold">Kasir</span>
                    <span class="mt-1 block text-xs text-slate-400">Akses untuk transaksi dan pembayaran.</span>
                </span>
            </label>
            <button type="submit" class="mt-3 w-full rounded-full bg-gradient-to-r from-orange-500 via-rose-500 to-amber-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:brightness-110">
                Lanjutkan
            </button>
        </form>
    </main>
</body>
</html>
