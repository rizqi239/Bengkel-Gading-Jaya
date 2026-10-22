@extends('layouts.app')

@section('title', 'Pusat Notifikasi - Bengkel GMJ')
@section('page_title', 'Notifikasi Bengkel')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Page Header & Actions Banner -->
    <div class="rounded-3xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-blue-100 text-rubick-blue flex items-center justify-center shrink-0 ring-4 ring-blue-50">
                <i data-lucide="bell" class="h-6 w-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Pusat Notifikasi</h1>
                    <span id="unread-pill" class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-600 border border-rose-200">
                        3 Belum Dibaca
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-medium mt-1">Daftar pemberitahuan resmi sistem, stok barang menipis, transaksi kasir, dan absensi bengkel.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button" onclick="markAllAsReadPage()" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-rubick-blue transition-all shadow-xs cursor-pointer">
                <i data-lucide="check-check" class="h-4 w-4 text-emerald-600"></i>
                <span>Tandai Semua Dibaca</span>
            </button>
            <button type="button" onclick="deleteAllNotificationsPage()" class="inline-flex items-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-100 hover:text-rose-700 transition-all shadow-xs cursor-pointer">
                <i data-lucide="trash-2" class="h-4 w-4 text-rose-600"></i>
                <span>Hapus Semua Notifikasi</span>
            </button>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-rubick-blue px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-800 transition-all cursor-pointer" style="background-color: #1b42b6;">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Quick Notification Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="rounded-3xl bg-white p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
            <div class="h-11 w-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="inbox" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Notifikasi</p>
                <p id="stat-total-count" class="text-xl font-extrabold text-slate-900 mt-0.5">5 Pesan</p>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
            <div class="h-11 w-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="bell-ring" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Belum Dibaca</p>
                <p id="stat-unread-count" class="text-xl font-extrabold text-rose-600 mt-0.5">3 Pesan</p>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
            <div class="h-11 w-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peringatan Stok</p>
                <p id="stat-stock-count" class="text-xl font-extrabold text-amber-600 mt-0.5">2 Item</p>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
            <div class="h-11 w-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="shopping-bag" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Transaksi &amp; Absen</p>
                <p id="stat-trx-count" class="text-xl font-extrabold text-slate-900 mt-0.5">3 Aktivitas</p>
            </div>
        </div>
    </div>

    <!-- Notification List & Filter Tabs Container -->
    <div class="rounded-3xl bg-white shadow-xs border border-slate-200/80 overflow-hidden">
        
        <!-- Filter Category Tabs -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="filterNotif('all', this)" class="notif-tab-btn px-4 py-2 rounded-2xl text-xs font-extrabold bg-rubick-blue text-white shadow-xs transition-all cursor-pointer" style="background-color: #1b42b6;">
                    Semua (5)
                </button>
                <button type="button" onclick="filterNotif('unread', this)" class="notif-tab-btn px-4 py-2 rounded-2xl text-xs font-bold text-slate-600 hover:bg-slate-200/70 transition-all cursor-pointer">
                    Belum Dibaca (3)
                </button>
                <button type="button" onclick="filterNotif('stock', this)" class="notif-tab-btn px-4 py-2 rounded-2xl text-xs font-bold text-slate-600 hover:bg-slate-200/70 transition-all cursor-pointer">
                    Stok Menipis (2)
                </button>
                <button type="button" onclick="filterNotif('transaksi', this)" class="notif-tab-btn px-4 py-2 rounded-2xl text-xs font-bold text-slate-600 hover:bg-slate-200/70 transition-all cursor-pointer">
                    Transaksi Kasir (2)
                </button>
                <button type="button" onclick="filterNotif('absensi', this)" class="notif-tab-btn px-4 py-2 rounded-2xl text-xs font-bold text-slate-600 hover:bg-slate-200/70 transition-all cursor-pointer">
                    Absensi (1)
                </button>
            </div>

            <span class="text-xs text-slate-400 font-semibold">Diperbarui real-time</span>
        </div>

        <!-- Notification Items Container -->
        <div class="divide-y divide-slate-100" id="notif-items-wrapper">
            
            <!-- Item 1: Stock Alert -->
            <div class="notif-card notif-unread notif-stock p-5 sm:p-6 hover:bg-slate-50/80 transition-colors flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 ring-4 ring-amber-50">
                        <i data-lucide="package-search" class="h-6 w-6"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700 uppercase tracking-wider">Peringatan Stok</span>
                            <span class="text-xs text-slate-400 font-medium">&bull; 10 Menit lalu</span>
                            <span class="notif-unread-tag px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-600">BARU</span>
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Stok Barang "Oli Yamalube Matic 0.8L" Tersisa 3 Pcs</h3>
                        <p class="text-xs text-slate-600 font-medium">Kuantitas stok barang ini hampir habis dari batas minimum aman. Harap segera melakukan pengadaan/tambah stok agar layanan bengkel tidak terganggu.</p>
                        <div class="pt-2">
                            <a href="{{ route('stock.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-colors">
                                <i data-lucide="plus-circle" class="h-3.5 w-3.5"></i>
                                <span>Tambah Stok Barang Sekarang</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="dismissNotif(this)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition-all cursor-pointer" title="Hapus Notifikasi">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <!-- Item 2: Stock Alert 2 -->
            <div class="notif-card notif-unread notif-stock p-5 sm:p-6 hover:bg-slate-50/80 transition-colors flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 ring-4 ring-rose-50">
                        <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 uppercase tracking-wider">Stok Habis</span>
                            <span class="text-xs text-slate-400 font-medium">&bull; 30 Menit lalu</span>
                            <span class="notif-unread-tag px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-600">BARU</span>
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Stok Barang "Kampas Rem Depan Vario 150" Habis (0 Pcs)</h3>
                        <p class="text-xs text-slate-600 font-medium">Stok barang telah mencapai angka 0. Transaksi sparepart ini sementara ditangguhkan hingga persediaan diisi ulang.</p>
                        <div class="pt-2">
                            <a href="{{ route('stock.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-colors">
                                <i data-lucide="box" class="h-3.5 w-3.5"></i>
                                <span>Kelola Inventaris Stok</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="dismissNotif(this)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition-all cursor-pointer" title="Hapus Notifikasi">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <!-- Item 3: Transaksi Kasir -->
            <div class="notif-card notif-unread notif-transaksi p-5 sm:p-6 hover:bg-slate-50/80 transition-colors flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 ring-4 ring-emerald-50">
                        <i data-lucide="receipt" class="h-6 w-6"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 uppercase tracking-wider">Kasir &amp; Pembayaran</span>
                            <span class="text-xs text-slate-400 font-medium">&bull; 45 Menit lalu</span>
                            <span class="notif-unread-tag px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-600">BARU</span>
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Pembayaran Kasir Selesai - Rp 350.000</h3>
                        <p class="text-xs text-slate-600 font-medium">Transaksi atas nama pelanggan Honda Vario 125 telah berhasil diproses oleh Kasir dan invoice pembayaran sudah diterbitkan.</p>
                        <div class="pt-2">
                            <a href="{{ route('riwayat') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors">
                                <i data-lucide="file-text" class="h-3.5 w-3.5"></i>
                                <span>Lihat Riwayat Transaksi</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="dismissNotif(this)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition-all cursor-pointer" title="Hapus Notifikasi">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <!-- Item 4: Absensi -->
            <div class="notif-card notif-read notif-absensi p-5 sm:p-6 hover:bg-slate-50/80 transition-colors flex items-start justify-between gap-4 opacity-80">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="h-12 w-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 ring-4 ring-blue-50">
                        <i data-lucide="calendar-check" class="h-6 w-6"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700 uppercase tracking-wider">Absensi Kehadiran</span>
                            <span class="text-xs text-slate-400 font-medium">&bull; 2 Jam lalu</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Absensi Masuk Hari Ini Tercatat</h3>
                        <p class="text-xs text-slate-600 font-medium">Sistem telah mencatat waktu kehadiran Anda jam 08:00 WIB. Terima kasih telah melakukan absen secara tepat waktu.</p>
                        <div class="pt-2">
                            <a href="{{ route('absensi.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors">
                                <i data-lucide="calendar" class="h-3.5 w-3.5 text-blue-600"></i>
                                <span>Cek Data Absensi Hari Ini</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="dismissNotif(this)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition-all cursor-pointer" title="Hapus Notifikasi">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <!-- Item 5: Transaksi Kasir 2 -->
            <div class="notif-card notif-read notif-transaksi p-5 sm:p-6 hover:bg-slate-50/80 transition-colors flex items-start justify-between gap-4 opacity-80">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 ring-4 ring-indigo-50">
                        <i data-lucide="shield-check" class="h-6 w-6"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700 uppercase tracking-wider">Sistem Bengkel GMJ</span>
                            <span class="text-xs text-slate-400 font-medium">&bull; 1 Hari lalu</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Pemberitahuan Pembaharuan Sistem Versi 2.0</h3>
                        <p class="text-xs text-slate-600 font-medium">Seluruh fitur pencetakan invoice kasir, penambahan stok mekanik, dan penghapusan otomatis tanpa konfirmasi pop-up telah diaktifkan secara optimal.</p>
                    </div>
                </div>
                <button type="button" onclick="dismissNotif(this)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition-all cursor-pointer" title="Hapus Notifikasi">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

        </div>

        <!-- Empty State Container (Hidden by default) -->
        <div id="notif-empty-state" class="hidden p-12 text-center space-y-3">
            <div class="h-16 w-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <i data-lucide="bell-off" class="h-8 w-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Tidak Ada Notifikasi pada Kategori Ini</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">Semua notifikasi pada kategori ini telah Anda selesaikan atau dibersihkan.</p>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    function filterNotif(category, btnElement) {
        // Update active tab button visual
        const buttons = document.querySelectorAll('.notif-tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-rubick-blue', 'text-white', 'shadow-xs');
            btn.style.backgroundColor = '';
            btn.classList.add('text-slate-600', 'hover:bg-slate-200/70');
        });

        btnElement.classList.remove('text-slate-600', 'hover:bg-slate-200/70');
        btnElement.classList.add('bg-rubick-blue', 'text-white', 'shadow-xs');
        btnElement.style.backgroundColor = '#1b42b6';

        // Filter cards logic
        const cards = document.querySelectorAll('.notif-card');
        let visibleCount = 0;

        cards.forEach(card => {
            if (category === 'all') {
                card.classList.remove('hidden');
                visibleCount++;
            } else if (category === 'unread') {
                if (card.classList.contains('notif-unread')) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            } else {
                if (card.classList.contains('notif-' + category)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            }
        });

        const emptyState = document.getElementById('notif-empty-state');
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    function markAllAsReadPage() {
        // Save read state to localStorage
        try {
            localStorage.setItem('bengkel_notif_read', 'true');
        } catch (e) {}

        // Immediately hide topbar red badge dot
        const badgeDot = document.getElementById('notification-badge-dot');
        if (badgeDot) badgeDot.classList.add('hidden');

        // Immediately hide/fade out all red unread tags and update card opacity
        const unreadCards = document.querySelectorAll('.notif-unread');
        unreadCards.forEach(card => {
            card.classList.remove('notif-unread');
            card.classList.add('notif-read', 'opacity-80');
            const unreadTag = card.querySelector('.notif-unread-tag');
            if (unreadTag) {
                unreadTag.style.transition = 'all 0.2s ease';
                unreadTag.style.opacity = '0';
                setTimeout(() => unreadTag.remove(), 200);
            }
        });

        // Fade out and hide top header red unread pill badge
        const unreadPill = document.getElementById('unread-pill');
        if (unreadPill) {
            unreadPill.style.transition = 'all 0.3s ease';
            unreadPill.style.opacity = '0';
            setTimeout(() => {
                unreadPill.classList.add('hidden');
            }, 300);
        }

        // Update stats counter
        const statUnread = document.getElementById('stat-unread-count');
        if (statUnread) {
            statUnread.textContent = '0 Pesan';
            statUnread.className = 'text-xl font-extrabold text-slate-800 mt-0.5';
        }
    }

    function deleteAllNotificationsPage() {
        try {
            localStorage.setItem('bengkel_notifs_cleared', 'true');
            localStorage.setItem('bengkel_notif_read', 'true');
        } catch (e) {}

        const badgeDot = document.getElementById('notification-badge-dot');
        if (badgeDot) badgeDot.classList.add('hidden');

        const allCards = document.querySelectorAll('.notif-card');
        allCards.forEach(card => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => card.remove(), 300);
        });

        const unreadPill = document.getElementById('unread-pill');
        if (unreadPill) unreadPill.classList.add('hidden');

        const statTotal = document.getElementById('stat-total-count');
        if (statTotal) statTotal.textContent = '0 Pesan';

        const statUnread = document.getElementById('stat-unread-count');
        if (statUnread) {
            statUnread.textContent = '0 Pesan';
            statUnread.className = 'text-xl font-extrabold text-slate-800 mt-0.5';
        }

        const statStock = document.getElementById('stat-stock-count');
        if (statStock) statStock.textContent = '0 Item';

        const statTrx = document.getElementById('stat-trx-count');
        if (statTrx) statTrx.textContent = '0 Aktivitas';

        setTimeout(() => {
            const emptyState = document.getElementById('notif-empty-state');
            if (emptyState) emptyState.classList.remove('hidden');
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('bengkel_notifs_cleared') === 'true') {
            deleteAllNotificationsPage();
        } else if (localStorage.getItem('bengkel_notif_read') === 'true') {
            markAllAsReadPage();
        }
    });
    function dismissNotif(btn) {
        const card = btn.closest('.notif-card');
        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.remove();
                // Re-check empty state if needed
                const remaining = document.querySelectorAll('.notif-card:not(.hidden)');
                if (remaining.length === 0) {
                    const emptyState = document.getElementById('notif-empty-state');
                    if (emptyState) emptyState.classList.remove('hidden');
                }
            }, 300);
        }
    }
</script>
@endsection
