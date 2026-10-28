@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page_title', 'Profil Pengguna')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Flash Status Alert -->
    @if(session('status'))
    <div id="status-alert" class="flex items-center justify-between rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 text-white shrink-0">
                <i data-lucide="check-circle" class="h-5 w-5"></i>
            </span>
            <p class="text-xs sm:text-sm font-bold">
                {{ session('status') }}
            </p>
        </div>
        <button onclick="document.getElementById('status-alert').remove()" class="text-emerald-500 hover:text-emerald-800 p-1 rounded-lg">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>
    @endif

    <!-- Profile Header Banner & Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: User Profile Identity Card -->
        <div class="lg:col-span-4 space-y-6">
            <div class="rounded-3xl bg-white shadow-xs border border-slate-200/80 overflow-hidden">
                <!-- Banner Cover -->
                <div class="h-16 bg-linear-to-r from-blue-700 via-blue-600 to-indigo-700 relative p-4 flex items-center" style="background: linear-gradient(135deg, #1b42b6 0%, #2563eb 100%);">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-white/20 text-white backdrop-blur-md uppercase tracking-wider">
                        <i data-lucide="shield" class="h-3 w-3"></i> Profile Account
                    </span>
                </div>

                <!-- Avatar & Quick Details (Spacious Clean Layout with Generous Space) -->
                <div class="px-6 pb-8 text-center" style="padding-top: 2.5rem;">
                    <div class="relative inline-block group cursor-pointer" style="margin-top: 2rem; margin-bottom: 1rem;" onclick="triggerAvatarUpload()" title="Klik untuk mengunggah / mengganti foto profil">
                        <img id="profile-page-avatar" src="{{ $avatarUrl }}" alt="User Avatar" class="h-28 w-28 rounded-2xl object-cover ring-4 ring-slate-100 shadow-md mx-auto group-hover:opacity-90 transition-all">
                        <div class="absolute inset-0 bg-slate-900/40 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <i data-lucide="camera" class="h-6 w-6 text-white"></i>
                        </div>
                    </div>
                    @error('avatar')
                    <p role="alert" class="mb-3 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror

                    <h2 class="text-xl font-extrabold text-slate-900 mt-3">{{ $user->name ?? 'Administrator' }}</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $user->email ?? 'admin@bengkelgmj.com' }}</p>

                    <div class="mt-3.5 inline-flex items-center gap-2 px-4.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs">
                        <i data-lucide="user-check" class="h-4 w-4 text-blue-600"></i>
                        <span>{{ $user->role ?? 'Admin' }}</span>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex justify-center">
                        <button type="button" onclick="triggerAvatarUpload()" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                            <i data-lucide="upload" class="h-4 w-4 text-blue-600"></i>
                            <span>Ganti Foto Profil</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Account Status & Access Info Box -->
            <div class="rounded-3xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">STATUS &amp; AKSES AKUN</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold flex items-center gap-2">
                            <i data-lucide="activity" class="h-4 w-4 text-emerald-600"></i> Status Akun
                        </span>
                        <span class="font-extrabold text-emerald-600 flex items-center gap-1">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Aktif (Online)
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold flex items-center gap-2">
                            <i data-lucide="building" class="h-4 w-4 text-blue-600"></i> Unit Operasional
                        </span>
                        <span class="font-bold text-slate-800">Bengkel Gading Jaya</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold flex items-center gap-2">
                            <i data-lucide="message-square" class="h-4 w-4 text-emerald-600"></i> No. WhatsApp
                        </span>
                        <span class="font-bold text-slate-800">
                            {{ !empty($user->whatsapp) ? $user->whatsapp : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold flex items-center gap-2">
                            <i data-lucide="shield-check" class="h-4 w-4 text-indigo-600"></i> Hak Akses Role
                        </span>
                        <span class="font-bold text-slate-800">
                            @if(($user->role ?? '') === 'Mekanik')
                                Terbatas (Absen, Servis)
                            @elseif(($user->role ?? '') === 'Kasir')
                                Transaksi Kasir &amp; Absen
                            @else
                                Akses Penuh System
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Profile Edit & Password Form -->
        <div class="lg:col-span-8">
            <div class="rounded-3xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200/80 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                        <i data-lucide="user-cog" class="h-5 w-5 text-rubick-blue"></i>
                        <span>Pengaturan Informasi Akun</span>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">Perbarui nama lengkap, email, atau ganti password akun Anda di sini.</p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="user" class="h-4 w-4"></i>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue focus:bg-white transition-all @error('name') border-rose-500 @enderror">
                        </div>
                        @error('name')
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                <i data-lucide="mail" class="h-4 w-4"></i>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue focus:bg-white transition-all @error('email') border-rose-500 @enderror">
                        </div>
                        @error('email')
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Badge (Readonly) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Peran Akun (Role)</label>
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                                <i data-lucide="badge-check" class="h-4 w-4 text-blue-600"></i>
                                {{ $user->role ?? 'Admin' }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">Role otomatis ditentukan oleh sistem</span>
                        </div>
                    </div>

                    <!-- Section Nomor WhatsApp -->
                    <div class="pt-5 border-t border-slate-100 space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i data-lucide="phone" class="h-4 w-4 text-emerald-600"></i>
                                <span>Nomor WhatsApp / Kontak</span>
                            </h4>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Masukkan nomor WhatsApp aktif Anda untuk koordinasi pekerjaan dan notifikasi bengkel.</p>
                        </div>

                        <div>
                            <label for="whatsapp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor WhatsApp <span class="text-emerald-600">(Aktif)</span></label>
                            <div class="relative">
                                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                    <i data-lucide="message-square" class="h-4 w-4 text-emerald-600"></i>
                                </div>
                                <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $user->whatsapp ?? '') }}" placeholder="Contoh: 081234567890" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue focus:bg-white transition-all @error('whatsapp') border-rose-500 @enderror">
                            </div>
                            @error('whatsapp')
                            <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section Ubah Password (Semua Role Diizinkan) -->
                    <div class="pt-5 border-t border-slate-100 space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i data-lucide="key-round" class="h-4 w-4 text-orange-500"></i>
                                <span>Ubah Password Akun</span>
                            </h4>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Semua role akun diizinkan mengubah password. Kosongkan jika tidak ingin mengganti password.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password Baru</label>
                                <div class="relative">
                                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                        <i data-lucide="lock" class="h-4 w-4"></i>
                                    </div>
                                    <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue focus:bg-white transition-all @error('password') border-rose-500 @enderror">
                                </div>
                                @error('password')
                                <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                        <i data-lucide="check-circle-2" class="h-4 w-4"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue focus:bg-white transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" style="background-color: #1b42b6;" class="inline-flex items-center gap-2 rounded-2xl bg-rubick-blue px-6 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 active:scale-95 transition-all cursor-pointer">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
@endsection
