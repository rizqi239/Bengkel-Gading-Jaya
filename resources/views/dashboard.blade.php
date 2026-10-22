@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Announcement Banner (Rubick Royal Blue #1b42b6 Banner) -->
    <div id="top-announcement-banner" style="background-color: #1b42b6;" class="flex items-center justify-between gap-3 sm:gap-4 rounded-2xl bg-rubick-blue bg-rubick p-3.5 sm:p-4 text-white shadow-xl shadow-blue-900/10 transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-white shrink-0">
                <i data-lucide="sparkles" class="h-5 w-5 text-blue-200"></i>
            </span>
            <p class="text-xs sm:text-sm font-medium leading-relaxed text-white">
                Selamat datang di <span class="font-bold underline text-white">Sistem Informasi Bengkel Gading Jaya</span>
            </p>
        </div>
        <button onclick="document.getElementById('top-announcement-banner').remove()" class="text-blue-200 hover:text-white p-1 rounded-lg hover:bg-white/10 shrink-0">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>

    <!-- Section 1: General Report (3 Columns Top Grid) -->
    <div id="general-report-section" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-800 tracking-tight">General Report</h3>
            <div class="flex items-center gap-2">
                <select id="dashboardPeriodFilter" class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs focus:outline-none focus:ring-2 focus:ring-rubick-blue cursor-pointer" aria-label="Filter periode laporan">
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 sm:gap-6">
            <!-- Card 1: Sales / Revenue Summary (Col 5) -->
            <div class="md:col-span-5 rounded-3xl bg-white p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 shadow-xs">
                            <i data-lucide="shopping-bag" class="h-6 w-6"></i>
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600 border border-emerald-100/60">
                            47% <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>
                        </span>
                    </div>

                    <div class="mt-4">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Rp {{ $stats['total_sales'] }}</h2>
                        <p class="mt-1.5 text-xs text-slate-400 leading-relaxed font-medium">
                            Total pendapatan perbaikan service &amp; penjualan sparepart real-time dari Kasir.
                        </p>
                    </div>

                    <div class="mt-6 space-y-3.5 border-t border-slate-100 pt-5">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-400 uppercase tracking-wider text-[10px]">TOTAL TRANSAKSI</span>
                            <span class="text-slate-800 font-bold flex items-center gap-1.5">{{ $stats['total_transactions'] }} <span class="text-emerald-500 text-[10px] font-extrabold">&bull; Real-Time</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-400 uppercase tracking-wider text-[10px]">TRANSAKSI DIBATALKAN</span>
                            <span class="text-slate-800 font-bold flex items-center gap-1.5">{{ $stats['cancellation_case'] }} <span class="text-rose-500 text-[10px] font-extrabold">&bull; Dibatalkan</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-400 uppercase tracking-wider text-[10px]">SERVIS SELESAI</span>
                            <span class="text-slate-800 font-bold flex items-center gap-1.5">{{ $stats['completed_repairs'] }} <span class="text-emerald-500 text-[10px] font-extrabold">&bull; Selesai</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-400 uppercase tracking-wider text-[10px]">SERVIS PROSES / PENDING</span>
                            <span class="text-slate-800 font-bold flex items-center gap-1.5">{{ $stats['active_repairs'] }} <span class="text-amber-500 text-[10px] font-extrabold">&bull; Dalam Proses</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-400 uppercase tracking-wider text-[10px]">TOTAL PELANGGAN</span>
                            <span class="text-slate-800 font-bold flex items-center gap-1.5">{{ $stats['total_customers'] }} <span class="text-emerald-500 text-[10px] font-extrabold">&bull; Pelanggan</span></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-2">
                    <a href="{{ route('dashboard.export-image') }}" download="Laporan_Foto_Bengkel_GMJ_{{ date('Y-m-d') }}.png" onclick="downloadReportPhoto(event)" style="background-color: #1b42b6;" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-rubick-blue bg-rubick px-5 py-3 text-xs font-bold text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 active:scale-[0.99] transition-all cursor-pointer">
                        <i data-lucide="image" class="h-4 w-4"></i>
                        <span id="download-report-btn-text">Download Reports (Foto)</span>
                        <i data-lucide="arrow-right-circle" class="h-4 w-4"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: Stok Barang & Sparepart (Col 4) -->
            <div class="md:col-span-4 rounded-3xl bg-white p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">STOK BARANG &amp; SPAREPART</span>
                        <a href="{{ route('stock.index') }}" class="text-slate-400 hover:text-rubick-blue transition-colors">
                            <i data-lucide="external-link" class="h-4 w-4"></i>
                        </a>
                    </div>

                    <div class="mt-2">
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total_stock_count'] }} <span class="text-sm font-semibold text-slate-500">Pcs</span></h3>
                        <p class="text-[11px] text-slate-400 font-medium">Total unit sparepart tersedia di bengkel</p>
                    </div>

                    <!-- List Stok Sparepart -->
                    <div class="mt-5 space-y-2 border-t border-slate-100 pt-4">
                        @foreach($stockWidgetList as $item)
                        <div class="flex items-center justify-between gap-3 p-2 rounded-2xl hover:bg-slate-50 transition-colors">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $item->name }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold">{{ $item->category }} &bull; {{ $item->code ?? 'SP-001' }}</p>
                            </div>
                            <span class="inline-flex rounded-full {{ $item->quantity <= 10 ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-rubick-blue' }} px-2.5 py-1 text-xs font-extrabold shrink-0">
                                {{ $item->quantity }} Pcs
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 pt-2">
                    <a href="{{ route('stock.index') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                        <i data-lucide="package" class="h-4 w-4 text-rubick-blue"></i>
                        <span>Kelola Stok Barang</span>
                    </a>
                </div>
            </div>

            <!-- Card 3: Riwayat Pembelian Sparepart (Toko Supplier) (Col 3) -->
            <div class="md:col-span-3 rounded-3xl bg-white p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">PEMBELIAN SPAREPART</span>
                        <a href="{{ route('pembelian.index') }}" class="text-slate-400 hover:text-rubick-blue transition-colors">
                            <i data-lucide="shopping-cart" class="h-4 w-4"></i>
                        </a>
                    </div>

                    <p class="text-[11px] text-slate-400 font-medium mb-3">Riwayat toko &amp; supplier pembelian</p>

                    <!-- List Pembelian Sparepart / Toko -->
                    <div class="space-y-3">
                        @forelse($pembelianWidgetList as $pem)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80 hover:bg-slate-100/70 transition-colors space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-rubick-blue truncate">{{ $pem->supplier_name }}</p>
                                    <p class="text-[11px] font-semibold text-slate-800 truncate mt-0.5">{{ $pem->barang }}</p>
                                </div>
                                <span class="inline-flex rounded-full {{ $pem->status === 'Diterima' ? 'bg-emerald-100 text-emerald-700' : ($pem->status === 'Dibatalkan' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }} px-2 py-0.5 text-[10px] font-bold shrink-0">
                                    {{ $pem->status }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] font-semibold text-slate-500 border-t border-slate-200/60 pt-1.5">
                                <span>Total: <strong class="text-slate-900">Rp {{ number_format($pem->total_harga, 0, ',', '.') }}</strong></span>
                                <span>{{ is_string($pem->tanggal_pesan) ? $pem->tanggal_pesan : ($pem->tanggal_pesan ? $pem->tanggal_pesan->format('d M Y') : '-') }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 text-center text-xs font-semibold text-slate-500">
                            Belum ada riwayat pembelian supplier. <a href="{{ route('pembelian.create') }}" class="text-rubick-blue underline font-bold">Tambah Pembelian</a>.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6 pt-2">
                    <a href="{{ route('pembelian.index') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                        <i data-lucide="file-text" class="h-3.5 w-3.5 text-rubick-blue"></i>
                        <span>Lihat Semua Pembelian</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Google Maps Lokasi Bengkel -->
   <div class="rounded-3xl bg-white p-5 sm:p-6 shadow-xs border border-slate-200/80 space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="map-pin" class="h-5 w-5 text-rose-500"></i>
                <span>Lokasi Bengkel Gading Jaya</span>
            </h3>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">Jalan KH. A. Tajuddin, Sriamur, Kec. Babelan, Jawa Barat 17610</p>
        </div>
        <a href="https://maps.google.com/?cid=3687205906647820347" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
            <i data-lucide="navigation" class="h-3.5 w-3.5 text-rubick-blue"></i>
            <span>Buka di Maps</span>
        </a>
    </div>
    <div class="w-full h-72 sm:h-80 rounded-2xl overflow-hidden border border-slate-200/80 shadow-inner bg-slate-100">
        <iframe 
            class="w-full h-full border-0" 
            src="https://maps.google.com/maps?q=BENGKEL+GADING+JAYA+MOTOR,+Jalan+KH.+A.+Tajuddin,+Sriamur,+Babelan,+Jawa+Barat&hl=id&z=17&output=embed" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>
    <!-- Section 4: Weekly Top Products Data Table (Real-Time Stock Terlaris) -->
    <div class="rounded-3xl bg-white p-5 sm:p-6 shadow-xs border border-slate-200/80 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>Produk Terlaris Minggu Ini</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-rubick-blue border border-blue-200/80">
                        <i data-lucide="zap" class="h-3 w-3 text-amber-500 fill-amber-500"></i> Terlaris
                    </span>
                </h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Daftar produk sparepart dengan penjualan terbanyak dari transaksi kasir</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('stock.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-rubick-blue px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition-colors" style="background-color: #1b42b6;">
                    <i data-lucide="plus-circle" class="h-5 w-5 shrink-0"></i>
                    <span>Tambah Stok</span>
                </a>
                <a href="{{ route('dashboard.export') }}" download="Laporan_Operasional_Bengkel_GMJ_{{ date('Y-m-d') }}.csv" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-colors cursor-pointer">
                    <i data-lucide="file-spreadsheet" class="h-3.5 w-3.5 text-rubick-blue"></i>
                    <span>Export Laporan (CSV)</span>
                </a>
            </div>
        </div>

        <!-- Mobile Card View Component -->
        <div class="md:hidden space-y-4">
            @forelse($topProducts as $item)
            <div class="top-product-card rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center -space-x-2 shrink-0">
                            @foreach($item['images'] as $img)
                            <img src="{{ $img }}" alt="Product Image" class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-xs">
                            @endforeach
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm">{{ $item['name'] }}</p>
                            <p class="text-[11px] text-slate-400 font-semibold flex items-center gap-1">
                                <span>{{ $item['category'] }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-rubick-blue font-bold">Terjual / Berkurang: {{ $item['sold'] }} Pcs</span>
                            </p>
                        </div>
                    </div>
                    @if($item['status'] === 'Active')
                        <span class="inline-flex items-center gap-1 text-emerald-600 text-xs font-bold shrink-0 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                            <i data-lucide="check-square" class="h-3.5 w-3.5"></i> Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-rose-500 text-xs font-bold shrink-0 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200/60">
                            <i data-lucide="x-square" class="h-3.5 w-3.5"></i> Inactive
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-between border-t border-slate-200/60 pt-2 text-xs">
                    <span class="text-slate-500 font-medium">Stok Tersedia: <strong class="text-slate-800 font-bold">{{ $item['stock'] }} Pcs</strong></span>
                    <div class="flex items-center gap-3">
                        <a href="{{ $item['edit_url'] }}" class="inline-flex items-center gap-1 text-slate-600 hover:text-rubick-blue font-bold">
                            <i data-lucide="edit-3" class="h-3.5 w-3.5"></i> Edit
                        </a>
                        @if(!empty($item['is_db']) && !empty($item['id']))
                        <button type="button" onclick="fastDeleteDashboardProduct(this, '{{ route('stock.destroy', $item['id']) }}', '{{ addslashes($item['name']) }}')" class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700 font-bold bg-transparent border-0 p-0 cursor-pointer transition-colors">
                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Delete
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-6 text-center text-xs font-semibold text-slate-500">
                Belum ada data stok barang. Silakan tambah barang di menu <a href="{{ route('stock.index') }}" class="text-rubick-blue underline font-bold">Stok Barang</a>.
            </div>
            @endforelse
        </div>

        <!-- Desktop Table Component -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="w-32 px-4 py-4">IMAGES</th>
                        <th class="w-[40%] px-4 py-4">PRODUCT NAME</th>
                        <th class="w-28 px-4 py-4">STOCK</th>
                        <th class="w-36 px-4 py-4">STATUS</th>
                        <th class="w-48 px-4 py-4 text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium">
                    @forelse($topProducts as $item)
                    <tr class="top-product-row hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center -space-x-2">
                                @foreach($item['images'] as $img)
                                <img src="{{ $img }}" alt="Product Image" class="h-9 w-9 rounded-full object-cover ring-2 ring-white shadow-xs">
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-bold text-slate-900 text-sm">{{ $item['name'] }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] text-slate-400 font-semibold">{{ $item['category'] }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-rubick-blue bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                    <i data-lucide="shopping-bag" class="h-3 w-3"></i> Berkurang: {{ $item['sold'] }} Pcs
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-bold text-slate-700">
                            {{ $item['stock'] }} Pcs
                        </td>
                        <td class="px-4 py-4">
                            @if($item['status'] === 'Active')
                                <span class="inline-flex items-center gap-1 text-emerald-600 font-bold bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/60">
                                    <i data-lucide="check-square" class="h-3.5 w-3.5"></i> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-rose-500 font-bold bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200/60">
                                    <i data-lucide="x-square" class="h-3.5 w-3.5"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="inline-flex items-center gap-4">
                                <a href="{{ $item['edit_url'] }}" class="inline-flex items-center gap-1 text-slate-600 hover:text-rubick-blue font-bold transition-colors">
                                    <i data-lucide="edit-3" class="h-3.5 w-3.5"></i> Edit
                                </a>
                                @if(!empty($item['is_db']) && !empty($item['id']))
                                <button type="button" onclick="fastDeleteDashboardProduct(this, '{{ route('stock.destroy', $item['id']) }}', '{{ addslashes($item['name']) }}')" class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700 font-bold bg-transparent border-0 p-0 cursor-pointer transition-colors">
                                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Delete
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-xs font-semibold text-slate-500">
                            Belum ada data stok barang. Silakan tambah barang di menu <a href="{{ route('stock.index') }}" class="text-rubick-blue underline font-bold">Stok Barang</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 pt-5">
            <div class="flex flex-wrap items-center gap-3">
                <span id="top-products-info" class="text-xs font-medium text-slate-500"></span>
                <div id="top-products-pagination-buttons" class="flex items-center gap-1 text-xs text-slate-500 overflow-x-auto max-w-full py-1">
                    <button class="h-8 w-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50">&laquo;</button>
                    <button class="h-8 w-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50">&lsaquo;</button>
                    <button class="h-8 w-8 rounded-lg bg-rubick-blue text-white font-bold flex items-center justify-center">1</button>
                    <button class="h-8 w-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50 font-bold">2</button>
                    <button class="h-8 w-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50 font-bold">3</button>
                    <button class="h-8 w-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50">&rsaquo;</button>
                    <button class="h-8 w-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50">&raquo;</button>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-semibold">Tampilkan:</span>
                <select id="top-products-per-page" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-rubick-blue/20">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<!-- Html2Canvas Library for Image Capture -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.getElementById('dashboardPeriodFilter')?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('period', this.value);
       
        window.location.assign(url.toString());
    });

    function fastDeleteDashboardProduct(btnElement, deleteUrl, productName) {
        // 1. Immediately animate & remove row/card from screen (0ms delay)
        const targetElement = btnElement.closest('.top-product-card') || btnElement.closest('.top-product-row');
        if (targetElement) {
            targetElement.style.transition = 'all 0.25s cubic-bezier(0.4, 0, 0.2, 1)';
            targetElement.style.opacity = '0';
            targetElement.style.transform = 'scale(0.96)';
            setTimeout(() => {
                targetElement.remove();
            }, 250);
        }

        // 2. Perform background AJAX DELETE request
        if (deleteUrl) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            }).then(response => {
                console.log('Product deleted from database successfully in background');
            }).catch(err => {
                console.error('Error during background deletion:', err);
            });
        }
    }
    function downloadReportPhoto(event) {
        if (typeof html2canvas === 'undefined') {
            return; // Falls back to server-side PNG route
        }

        event.preventDefault();
        const section = document.getElementById('general-report-section') || document.querySelector('.space-y-6');
        const btnText = document.getElementById('download-report-btn-text');
        
        if (btnText) btnText.textContent = 'Memproses Foto...';

        html2canvas(section, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#f1f5f9'
        }).then(canvas => {
            const link = document.createElement('a');
            const today = new Date().toISOString().slice(0,10);
            link.download = `Laporan_Foto_Bengkel_GMJ_${today}.png`;
            link.href = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            if (btnText) btnText.textContent = 'Download Reports (Foto)';
        }).catch(err => {
            console.error('Html2Canvas Error, fallback to server image:', err);
            window.location.href = "{{ route('dashboard.export-image') }}";
            if (btnText) btnText.textContent = 'Download Reports (Foto)';
        });
    }

    // Weekly Top Products Table Pagination Logic
    document.addEventListener('DOMContentLoaded', function () {
        const mobileCards = Array.from(document.querySelectorAll('.top-product-card'));
        const desktopRows = Array.from(document.querySelectorAll('.top-product-row'));
        const paginationContainer = document.getElementById('top-products-pagination-buttons');
        const perPageSelect = document.getElementById('top-products-per-page');
        const infoText = document.getElementById('top-products-info');

        const totalItems = Math.max(mobileCards.length, desktopRows.length);
        if (totalItems === 0) {
            if (infoText) infoText.textContent = '';
            if (paginationContainer) paginationContainer.innerHTML = '';
            return;
        }

        let currentPage = 1;
        let perPage = parseInt(perPageSelect ? perPageSelect.value : 10) || 10;

        function renderTablePage(page) {
            const totalPages = Math.ceil(totalItems / perPage) || 1;
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            currentPage = page;

            const startIdx = (currentPage - 1) * perPage;
            const endIdx = startIdx + perPage;

            mobileCards.forEach((card, index) => {
                if (index >= startIdx && index < endIdx) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            desktopRows.forEach((row, index) => {
                if (index >= startIdx && index < endIdx) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            if (infoText) {
                const currentEnd = Math.min(endIdx, totalItems);
                infoText.textContent = `Menampilkan ${startIdx + 1} - ${currentEnd} dari ${totalItems} data`;
            }

            renderButtons(totalPages);
        }

        function createBtn(text, isDisabled, onClick) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = text;
            btn.disabled = isDisabled;
            btn.className = `h-8 w-8 rounded-lg border border-slate-200 flex items-center justify-center transition-colors ${isDisabled ? 'opacity-40 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-50 text-slate-700 font-bold'}`;
            if (!isDisabled) {
                btn.addEventListener('click', onClick);
            }
            return btn;
        }

        function renderButtons(totalPages) {
            if (!paginationContainer) return;
            paginationContainer.innerHTML = '';

            // First Page Button <<
            const firstBtn = createBtn('&laquo;', currentPage === 1, () => renderTablePage(1));
            paginationContainer.appendChild(firstBtn);

            // Prev Button <
            const prevBtn = createBtn('&lsaquo;', currentPage === 1, () => renderTablePage(currentPage - 1));
            paginationContainer.appendChild(prevBtn);

            // Page Numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (currentPage <= 3) {
                endPage = Math.min(totalPages, 5);
            }
            if (currentPage > totalPages - 2) {
                startPage = Math.max(1, totalPages - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.innerHTML = i;
                if (i === currentPage) {
                    pageBtn.className = 'h-8 w-8 rounded-lg bg-rubick-blue text-white font-bold flex items-center justify-center shadow-xs cursor-default';
                } else {
                    pageBtn.className = 'h-8 w-8 rounded-lg border border-slate-200 text-slate-700 font-bold flex items-center justify-center hover:bg-slate-50 transition-colors';
                    pageBtn.addEventListener('click', () => renderTablePage(i));
                }
                paginationContainer.appendChild(pageBtn);
            }

            // Next Button >
            const nextBtn = createBtn('&rsaquo;', currentPage === totalPages, () => renderTablePage(currentPage + 1));
            paginationContainer.appendChild(nextBtn);

            // Last Button >>
            const lastBtn = createBtn('&raquo;', currentPage === totalPages, () => renderTablePage(totalPages));
            paginationContainer.appendChild(lastBtn);
        }

        if (perPageSelect) {
            perPageSelect.addEventListener('change', function () {
                perPage = parseInt(this.value) || 10;
                renderTablePage(1);
            });
        }

        renderTablePage(1);
    });
</script>
@endsection