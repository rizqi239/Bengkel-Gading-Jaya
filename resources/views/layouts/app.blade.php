<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dashboard Bengkel GMJ - Rubick Theme Management System">
    <title>@yield('title', 'Dashboard') - Bengkel Gading Jaya</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen lg:h-full flex overflow-x-hidden lg:overflow-hidden bg-slate-100 selection:bg-indigo-600 selection:text-white">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm hidden lg:hidden transition-opacity duration-300 opacity-0" onclick="toggleSidebar()"></div>

    <!-- Sidebar Component (Rubick Deep Royal Blue Theme) -->
    <aside id="sidebar" style="background-color: #1b42b6; color: #ffffff;" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-rubick-blue bg-rubick text-white transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:shrink-0 shadow-2xl">
        <!-- Logo / Brand Header -->
        <div class="flex h-20 items-center justify-between px-6 border-b border-blue-800/40">
            <div class="flex items-center gap-3">
                <!-- Clickable Interactive Logo Box -->
                <form action="{{ route('settings.logo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="sidebar-logo-input" title="Klik untuk mengunggah / mengganti Logo Bengkel" class="relative cursor-pointer flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-rubick-blue shadow-md font-extrabold text-xl overflow-hidden hover:ring-2 hover:ring-white/80 transition-all">
                    <!-- Custom Uploaded Logo Image Preview -->
                    @if($logoUrl)
                    <img id="sidebar-logo-preview" src="{{ $logoUrl }}" alt="Logo Bengkel Gading Jaya" class="h-full w-full object-contain rounded-xl">
                    @endif
                    
                    <!-- Default Logo Icon -->
                    <div id="sidebar-logo-default" class="{{ $logoUrl ? 'hidden' : 'flex' }} items-center justify-center">
                        <i data-lucide="wrench" class="h-5 w-5 text-rubick-blue" style="color: #1b42b6;"></i>
                    </div>

                    <!-- Hidden File Input -->
                    <input type="file" id="sidebar-logo-input" name="logo" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>

                <div>
                    <h1 class="text-lg font-bold text-white tracking-wider" style="color: #ffffff;">Gading Jaya</h1>
                    <p class="text-[10px] text-white/90 font-medium uppercase tracking-widest" style="color: #ffffff;"></p>
                </div>
            </div>
            <button class="lg:hidden text-white hover:text-white p-1 rounded-lg hover:bg-white/10" onclick="toggleSidebar()">
                <i data-lucide="x" class="h-5 w-5 text-white"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 space-y-2 px-3 pt-6 pb-5 overflow-y-auto" style="padding-top: 1.5rem;">
            <!-- Active Menu Item (Rubick Style with Notch Effect) -->
            <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 mt-4 {{ Route::is('dashboard') ? 'bg-white text-rubick-blue shadow-lg shadow-blue-950/20 font-bold' : 'text-white hover:bg-white/10' }}" style="margin-top: 1rem; {{ Route::is('dashboard') ? 'background-color: #ffffff; color: #1b42b6;' : 'color: #ffffff;' }}">
                <i data-lucide="layout-dashboard" class="h-5 w-5 shrink-0 mr-3.5 {{ Route::is('dashboard') ? 'text-rubick-blue' : 'text-white' }}" style="margin-right: 14px; {{ Route::is('dashboard') ? 'color: #1b42b6;' : 'color: #ffffff;' }}"></i>
                <span class="ml-1">Dashboard</span>
            </a>

            <a href="{{ route('absensi.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ Route::is('absensi.*') ? 'bg-white text-rubick-blue shadow-lg shadow-blue-950/20 font-bold' : 'text-white hover:bg-white/10' }}" style="{{ Route::is('absensi.*') ? 'background-color: #ffffff; color: #1b42b6;' : 'color: #ffffff;' }}">
                <i data-lucide="calendar-check" class="h-5 w-5 shrink-0 mr-3.5 {{ Route::is('absensi.*') ? 'text-rubick-blue' : 'text-white' }}" style="margin-right: 14px; {{ Route::is('absensi.*') ? 'color: #1b42b6;' : 'color: #ffffff;' }}"></i>
                <span class="ml-1">Absensi Karyawan</span>
            </a>

            <a href="{{ route('stock.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ Route::is('stock.*') ? 'bg-white text-rubick-blue shadow-lg shadow-blue-950/20 font-bold' : 'text-white hover:bg-white/10' }}" style="{{ Route::is('stock.*') ? 'background-color: #ffffff; color: #1b42b6;' : 'color: #ffffff;' }}">
                <i data-lucide="package" class="h-5 w-5 shrink-0 mr-3.5 {{ Route::is('stock.*') ? 'text-rubick-blue' : 'text-white' }}" style="margin-right: 14px; {{ Route::is('stock.*') ? 'color: #1b42b6;' : 'color: #ffffff;' }}"></i>
                <span class="ml-1">Stok Barang</span>
            </a>

            <a href="{{ route('kasir.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ Route::is('kasir.*') ? 'bg-white text-rubick-blue shadow-lg shadow-blue-950/20 font-bold' : 'text-white hover:bg-white/10' }}" style="{{ Route::is('kasir.*') ? 'background-color: #ffffff; color: #1b42b6;' : 'color: #ffffff;' }}">
                <i data-lucide="wrench" class="h-5 w-5 shrink-0 mr-3.5 {{ Route::is('kasir.*') ? 'text-rubick-blue' : 'text-white' }}" style="margin-right: 14px; {{ Route::is('kasir.*') ? 'color: #1b42b6;' : 'color: #ffffff;' }}"></i>
                <span class="ml-1">Kasir</span>
            </a>

            <a href="{{ route('pembelian.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ Route::is('pembelian.*') ? 'bg-white text-rubick-blue shadow-lg shadow-blue-950/20 font-bold' : 'text-white hover:bg-white/10' }}" style="{{ Route::is('pembelian.*') ? 'background-color: #ffffff; color: #1b42b6;' : 'color: #ffffff;' }}">
                <i data-lucide="shopping-cart" class="h-5 w-5 shrink-0 mr-3.5 {{ Route::is('pembelian.*') ? 'text-rubick-blue' : 'text-white' }}" style="margin-right: 14px; {{ Route::is('pembelian.*') ? 'color: #1b42b6;' : 'color: #ffffff;' }}"></i>
                <span class="ml-1">Pembelian Stock</span>
            </a>

            <a href="{{ route('riwayat') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ Route::is('riwayat') ? 'bg-white text-rubick-blue shadow-lg shadow-blue-950/20 font-bold' : 'text-white hover:bg-white/10' }}" style="{{ Route::is('riwayat') ? 'background-color: #ffffff; color: #1b42b6;' : 'color: #ffffff;' }}">
                <i data-lucide="clock" class="h-5 w-5 shrink-0 mr-3.5 {{ Route::is('riwayat') ? 'text-rubick-blue' : 'text-white' }}" style="margin-right: 14px; {{ Route::is('riwayat') ? 'color: #1b42b6;' : 'color: #ffffff;' }}"></i>
                <span class="ml-1">Riwayat Servis</span>
            </a>

            <a href="{{ route('laporan') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ Route::is('laporan') ? 'bg-white text-rubick-blue shadow-lg shadow-blue-950/20 font-bold' : 'text-white hover:bg-white/10' }}" style="{{ Route::is('laporan') ? 'background-color: #ffffff; color: #1b42b6;' : 'color: #ffffff;' }}">
                <i data-lucide="bar-chart-3" class="h-5 w-5 shrink-0 mr-3.5 {{ Route::is('laporan') ? 'text-rubick-blue' : 'text-white' }}" style="margin-right: 14px; {{ Route::is('laporan') ? 'color: #1b42b6;' : 'color: #ffffff;' }}"></i>
                <span class="ml-1">Laporan</span>
            </a>
        </nav>

        <!-- Sidebar Profile Footer -->
        <div class="p-4 border-t border-blue-800/40 bg-blue-900/30">
            <div class="flex items-center justify-between gap-2">
                <a href="{{ route('profile') }}" class="flex-1 min-w-0 flex items-center gap-3 text-left p-1.5 rounded-xl hover:bg-white/10 transition-all group cursor-pointer focus:outline-none" title="Klik untuk membuka Halaman Profil">
                    <div class="relative shrink-0">
                        <img id="sidebar-user-avatar" src="{{ $avatarUrl }}" alt="User Avatar" class="h-10 w-10 rounded-xl object-cover ring-2 ring-white/40 group-hover:ring-white transition-all">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate group-hover:text-blue-100 transition-colors" style="color: #ffffff;">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-[11px] text-white/80 truncate" style="color: #ffffff;">{{ Auth::user()->role ?? 'Admin' }} &bull; {{ Auth::user()->email ?? 'admin@bengkelgmj.com' }}</p>
                    </div>
                </a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-white/80 hover:text-rose-300 p-2 rounded-xl hover:bg-white/10 transition-all shrink-0 cursor-pointer" title="Keluar / Logout" style="color: #ffffff;">
                    <i data-lucide="log-out" class="h-5 w-5 text-white" style="color: #ffffff;"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Logout Form Hidden -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Main Workspace Area -->
    <div class="flex flex-1 flex-col overflow-hidden">
        
        <!-- Rubick Top Header Bar -->
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 backdrop-blur-md px-4 lg:px-8 shrink-0 shadow-xs">
            <!-- Mobile Toggle, Brand & Breadcrumbs -->
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <button class="lg:hidden text-slate-700 hover:text-blue-600 p-1.5 rounded-xl hover:bg-slate-100 active:scale-95 transition-all shrink-0" onclick="toggleSidebar()" aria-label="Open Navigation Menu">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 truncate">
                    <span class="text-slate-400">Application</span>
                    <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400 shrink-0"></i>
                    <span class="text-slate-800 font-bold truncate">@yield('page_title', 'Dashboard')</span>
                </div>
            </div>

            <!-- Right Controls: Search, Notifications, Avatar Button -->
            <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                <!-- Search Input -->
                <div class="relative hidden xs:block sm:block">
                    <input type="text" placeholder="Search..." class="w-28 xs:w-36 sm:w-48 lg:w-64 rounded-full bg-slate-100 border border-slate-200 pl-4 pr-9 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all">
                    <i data-lucide="search" class="absolute right-3 top-2 h-4 w-4 text-slate-400"></i>
                </div>

                <!-- Notification Bell Link -->
                <a href="{{ route('notifikasi') }}" class="relative p-2 text-slate-500 hover:text-rubick-blue hover:bg-slate-100 rounded-full transition-all focus:outline-none cursor-pointer" title="Klik untuk membuka Halaman Notifikasi Bengkel">
                    <i data-lucide="bell" class="h-5 w-5"></i>
                    <span id="notification-badge-dot" class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white animate-pulse"></span>
                </a>

                <!-- Profile Avatar Button & Dropdown -->
                <div class="relative border-l border-slate-200 pl-2.5 sm:pl-3">
                    <button type="button" id="profile-menu-button" onclick="toggleProfileDropdown(event)" class="group flex items-center gap-2.5 p-1 rounded-full hover:bg-slate-100 active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-rubick-blue/30 cursor-pointer" aria-expanded="false" aria-haspopup="true" title="Klik untuk membuka menu profil">
                        <div class="relative">
                            <img id="topbar-user-avatar" src="{{ $avatarUrl }}" alt="User Avatar" class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-200 group-hover:ring-blue-600 transition-all">
                        </div>
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-xs font-bold text-slate-800 leading-tight group-hover:text-rubick-blue transition-colors">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            <span class="text-[10px] font-semibold text-slate-400 leading-none mt-0.5">{{ Auth::user()->role ?? 'Admin' }}</span>
                        </div>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" id="profile-dropdown-arrow"></i>
                    </button>

                    <!-- Rubick Profile Popover Dropdown Menu -->
                    <div id="profile-dropdown-menu" class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white shadow-2xl border border-slate-200/90 z-50 py-2 transition-all duration-200 opacity-0 scale-95 origin-top-right">
                        <!-- User Header Card -->
                        <div class="px-4 py-3 border-b border-slate-100 bg-linear-to-r from-blue-50/60 to-indigo-50/30">
                            <div class="flex items-center gap-3">
                                <div class="relative shrink-0">
                                    <img id="dropdown-user-avatar" src="{{ $avatarUrl }}" alt="User Avatar" class="h-10 w-10 rounded-full object-cover ring-2 ring-blue-600/30">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email ?? 'admin@bengkelgmj.com' }}</p>
                                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-100 text-rubick-blue">
                                        {{ Auth::user()->role ?? 'Admin' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Options -->
                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-rubick-blue transition-colors cursor-pointer">
                                <i data-lucide="user" class="h-4 w-4 text-blue-600"></i>
                                <span>Halaman Profil Saya</span>
                            </a>
                            <button type="button" onclick="triggerAvatarUpload(); toggleProfileDropdown();" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-rubick-blue transition-colors cursor-pointer">
                                <i data-lucide="camera" class="h-4 w-4 text-blue-600"></i>
                                <span>Ganti Foto Profil</span>
                            </button>
                            <a href="{{ route('profile') }}" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-rubick-blue transition-colors cursor-pointer">
                                <i data-lucide="shield-check" class="h-4 w-4 text-emerald-600"></i>
                                <span>Info Peran &amp; Hak Akses</span>
                            </a>
                        </div>

                        <div class="border-t border-slate-100 pt-1 mt-1">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                <i data-lucide="log-out" class="h-4 w-4 text-rose-600"></i>
                                <span>Keluar (Logout)</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Wrapper -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-8 space-y-6 pb-24 lg:pb-8">
            @yield('content')
        </main>

        <!-- Mobile Bottom Navigation Bar (Royal Blue Icons & Labels) -->
        <nav class="lg:hidden" style="position: fixed; bottom: 0; left: 0; right: 0; width: 100%; z-index: 50; background-color: rgba(255, 255, 255, 0.98); backdrop-filter: blur(12px); border-top: 1px solid #cbd5e1; padding: 6px 0; box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);">
            <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-around; width: 100%; margin: 0 auto;">
                
                <a href="{{ route('dashboard') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; min-width: 0; text-decoration: none; padding: 5px 2px; margin: 0 2px; border-radius: 12px; background-color: {{ Route::is('dashboard') ? '#eff6ff' : 'transparent' }}; color: #1b42b6; font-weight: {{ Route::is('dashboard') ? '800' : '600' }};">
                    <i data-lucide="layout-dashboard" style="width: 20px; height: 20px; margin-bottom: 2px; color: #1b42b6; stroke: #1b42b6; stroke-width: {{ Route::is('dashboard') ? '2.5' : '2' }};"></i>
                    <span style="font-size: 11px; line-height: 1; white-space: nowrap; color: #1b42b6;">Dashboard</span>
                </a>

                <a href="{{ route('absensi.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; min-width: 0; text-decoration: none; padding: 5px 2px; margin: 0 2px; border-radius: 12px; background-color: {{ Route::is('absensi.*') ? '#eff6ff' : 'transparent' }}; color: #1b42b6; font-weight: {{ Route::is('absensi.*') ? '800' : '600' }};">
                    <i data-lucide="calendar-check" style="width: 20px; height: 20px; margin-bottom: 2px; color: #1b42b6; stroke: #1b42b6; stroke-width: {{ Route::is('absensi.*') ? '2.5' : '2' }};"></i>
                    <span style="font-size: 11px; line-height: 1; white-space: nowrap; color: #1b42b6;">Absensi</span>
                </a>

                <a href="{{ route('kasir.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; min-width: 0; text-decoration: none; padding: 5px 2px; margin: 0 2px; border-radius: 12px; background-color: {{ Route::is('kasir.*') ? '#eff6ff' : 'transparent' }}; color: #1b42b6; font-weight: {{ Route::is('kasir.*') ? '800' : '600' }};">
                    <i data-lucide="wrench" style="width: 20px; height: 20px; margin-bottom: 2px; color: #1b42b6; stroke: #1b42b6; stroke-width: {{ Route::is('kasir.*') ? '2.5' : '2' }};"></i>
                    <span style="font-size: 11px; line-height: 1; white-space: nowrap; color: #1b42b6;">Kasir</span>
                </a>

                <a href="{{ route('stock.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; min-width: 0; text-decoration: none; padding: 5px 2px; margin: 0 2px; border-radius: 12px; background-color: {{ Route::is('stock.*') ? '#eff6ff' : 'transparent' }}; color: #1b42b6; font-weight: {{ Route::is('stock.*') ? '800' : '600' }};">
                    <i data-lucide="package" style="width: 20px; height: 20px; margin-bottom: 2px; color: #1b42b6; stroke: #1b42b6; stroke-width: {{ Route::is('stock.*') ? '2.5' : '2' }};"></i>
                    <span style="font-size: 11px; line-height: 1; white-space: nowrap; color: #1b42b6;">Stok</span>
                </a>

                <a href="{{ route('riwayat') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; min-width: 0; text-decoration: none; padding: 5px 2px; margin: 0 2px; border-radius: 12px; background-color: {{ Route::is('riwayat') ? '#eff6ff' : 'transparent' }}; color: #1b42b6; font-weight: {{ Route::is('riwayat') ? '800' : '600' }};">
                    <i data-lucide="clock" style="width: 20px; height: 20px; margin-bottom: 2px; color: #1b42b6; stroke: #1b42b6; stroke-width: {{ Route::is('riwayat') ? '2.5' : '2' }};"></i>
                    <span style="font-size: 11px; line-height: 1; white-space: nowrap; color: #1b42b6;">Riwayat</span>
                </a>

            </div>
        </nav>
    </div>

    <!-- Hidden File Input for Avatar Upload -->
    <form id="avatar-upload-form" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" id="user-avatar-input" name="avatar" accept="image/*" onchange="this.form.submit()">
    </form>

    @error('logo')
    <div role="alert" class="fixed bottom-5 right-5 z-50 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-xs font-semibold text-rose-700 shadow-lg">
        {{ $message }}
    </div>
    @enderror

    <!-- Profile Details Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" onclick="handleModalBackdropClick(event)">
        <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-all duration-300" id="profile-modal-content">
            
            <!-- Modal Header with Gradient Banner -->
            <div class="relative h-28 bg-linear-to-r from-blue-700 via-blue-600 to-indigo-700 p-6 flex items-start justify-between" style="background: linear-gradient(135deg, #1b42b6 0%, #2563eb 100%);">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-white/20 text-white backdrop-blur-md uppercase tracking-wider">
                    <i data-lucide="shield" class="h-3 w-3"></i> Profil Pengguna
                </span>
                <button type="button" onclick="closeProfileModal()" class="rounded-full bg-black/20 p-1.5 text-white/80 hover:text-white hover:bg-black/30 transition-all focus:outline-none cursor-pointer">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <!-- Avatar & Main Info Floating Card -->
            <div class="px-6 pb-6 pt-0 -mt-12">
                <div class="flex items-end justify-between mb-4">
                    <div class="relative group cursor-pointer" onclick="triggerAvatarUpload()" title="Klik untuk mengunggah / mengganti foto profil">
                        <img id="modal-user-avatar" src="{{ $avatarUrl }}" alt="User Avatar" class="h-20 w-20 rounded-2xl object-cover ring-4 ring-white shadow-lg group-hover:opacity-90 transition-all">
                        <div class="absolute inset-0 bg-slate-900/40 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <i data-lucide="camera" class="h-6 w-6 text-white"></i>
                        </div>
                    </div>
                    <button type="button" onclick="triggerAvatarUpload()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-colors cursor-pointer">
                        <i data-lucide="upload" class="h-3.5 w-3.5 text-blue-600"></i>
                        <span>Ganti Foto</span>
                    </button>
                </div>

                <div>
                    <h3 class="text-xl font-extrabold text-slate-900" id="modal-user-name">{{ Auth::user()->name ?? 'Administrator' }}</h3>
                    <p class="text-xs text-slate-500 font-medium" id="modal-user-email">{{ Auth::user()->email ?? 'admin@bengkelgmj.com' }}</p>
                </div>

                <!-- Account Information List -->
                <div class="mt-5 space-y-3 border-t border-slate-100 pt-4 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold flex items-center gap-2">
                            <i data-lucide="user-check" class="h-4 w-4 text-blue-600"></i> Peran / Role
                        </span>
                        <span class="font-extrabold text-blue-700 bg-blue-100/80 px-2.5 py-1 rounded-lg" id="modal-user-role">
                            {{ Auth::user()->role ?? 'Admin' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold flex items-center gap-2">
                            <i data-lucide="activity" class="h-4 w-4 text-emerald-600"></i> Status Sesi
                        </span>
                        <span class="font-extrabold text-emerald-600 flex items-center gap-1">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Online (Aktif)
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold flex items-center gap-2">
                            <i data-lucide="shield-check" class="h-4 w-4 text-indigo-600"></i> Akses Sistem
                        </span>
                        <span class="font-bold text-slate-700">Bengkel Gading Jaya</span>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="mt-6 flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                    <a href="{{ route('profile') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition-colors shadow-xs">
                        <i data-lucide="user" class="h-3.5 w-3.5"></i>
                        <span>Ke Halaman Profil</span>
                    </a>
                    <button type="button" onclick="closeProfileModal()" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                        Tutup
                    </button>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 transition-colors shadow-xs">
                        <i data-lucide="log-out" class="h-3.5 w-3.5"></i>
                        <span>Keluar Akun</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            } else {
                backdrop.classList.add('opacity-0');
                sidebar.classList.add('-translate-x-full');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            }
        }

        function toggleProfileDropdown(event) {
            if (event) event.stopPropagation();

            // Close notification dropdown if open
            const notifDropdown = document.getElementById('notification-dropdown-menu');
            if (notifDropdown && !notifDropdown.classList.contains('hidden')) {
                toggleNotificationDropdown();
            }

            const dropdown = document.getElementById('profile-dropdown-menu');
            const arrow = document.getElementById('profile-dropdown-arrow');
            if (!dropdown) return;

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                    if (arrow) arrow.classList.add('rotate-180');
                }, 10);
            } else {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                if (arrow) arrow.classList.remove('rotate-180');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            }
        }

        function triggerAvatarUpload() {
            const fileInput = document.getElementById('user-avatar-input');
            if (fileInput) {
                fileInput.value = '';
                fileInput.click();
            }
        }

        function openProfileModal() {
            const modal = document.getElementById('profile-modal');
            const modalContent = document.getElementById('profile-modal-content');
            if (!modal) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                if (modalContent) {
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }
            }, 10);
        }

        function closeProfileModal() {
            const modal = document.getElementById('profile-modal');
            const modalContent = document.getElementById('profile-modal-content');
            if (!modal) return;

            if (modalContent) {
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
            }
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        function handleModalBackdropClick(event) {
            if (event.target.id === 'profile-modal') {
                closeProfileModal();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('bengkel_notif_read') === 'true') {
                const badgeDot = document.getElementById('notification-badge-dot');
                if (badgeDot) badgeDot.classList.add('hidden');
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function toggleNotificationDropdown(event) {
            if (event) event.stopPropagation();
            
            // Close profile dropdown if open
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                toggleProfileDropdown();
            }

            const dropdown = document.getElementById('notification-dropdown-menu');
            if (!dropdown) return;

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            }
        }

        function markAllNotificationsAsRead(event) {
            if (event) event.stopPropagation();
            const badgeDot = document.getElementById('notification-badge-dot');
            const unreadCount = document.getElementById('notification-unread-count');
            const unreadDots = document.querySelectorAll('.notif-unread-dot');

            if (badgeDot) badgeDot.classList.add('hidden');
            if (unreadCount) {
                unreadCount.textContent = '0 Baru';
                unreadCount.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-500';
            }
            unreadDots.forEach(dot => dot.classList.add('hidden'));
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            const profileBtn = document.getElementById('profile-menu-button');
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                if (!profileBtn.contains(event.target) && !profileDropdown.contains(event.target)) {
                    toggleProfileDropdown();
                }
            }

            const notifDropdown = document.getElementById('notification-dropdown-menu');
            const notifBtn = document.getElementById('notification-menu-button');
            if (notifDropdown && !notifDropdown.classList.contains('hidden')) {
                if (!notifBtn.contains(event.target) && !notifDropdown.contains(event.target)) {
                    toggleNotificationDropdown();
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
