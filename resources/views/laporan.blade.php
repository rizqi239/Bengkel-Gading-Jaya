@extends('layouts.app')

@section('title', 'Laporan Keuangan & Operasional')
@section('page_title', 'Laporan Keuangan & Operasional')

@section('content')
<div class="space-y-6 sm:space-y-8">
    {{-- Alert Success Notification --}}
    @if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-200/80 p-4 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle" class="h-5 w-5 text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
    </div>
    @endif

    {{-- Header Action & Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Ringkasan Laporan Keuangan</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Laporan terintegrasi dari Kasir, Pembelian Supplier, dan Catatan Keuangan Operasional</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openModal('tambahLaporanModal')" class="inline-flex items-center gap-2 rounded-2xl bg-rubick-blue px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition-colors cursor-pointer">
                <i data-lucide="plus-circle" class="h-4 w-4"></i>
                <span>Tambah Catatan Keuangan</span>
            </button>
        </div>
    </div>

    {{-- Top Summary Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">PENDAPATAN (PEMASUKAN)</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="h-4 w-4"></i>
                </div>
            </div>
            <h4 id="report-income-total" class="text-lg sm:text-2xl font-black text-slate-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
            <p class="text-[11px] text-emerald-600 font-bold mt-1">Servis &amp; Pemasukan Kasir</p>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">BIAYA OPERASIONAL</span>
                <div class="h-8 w-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="trending-down" class="h-4 w-4"></i>
                </div>
            </div>
            <h4 id="report-expense-total" class="text-lg sm:text-2xl font-black text-slate-900">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</h4>
            <p class="text-[11px] text-rose-500 font-bold mt-1">Pembelian &amp; Operasional</p>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">SALDO OPERASIONAL</span>
                <div class="h-8 w-8 rounded-xl bg-blue-50 text-rubick-blue flex items-center justify-center">
                    <i data-lucide="wallet" class="h-4 w-4"></i>
                </div>
            </div>
            <h4 id="report-profit-total" class="text-lg sm:text-2xl font-black {{ $labaBersih >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                Rp {{ number_format($labaBersih, 0, ',', '.') }}
            </h4>
            <p class="text-[11px] text-slate-400 font-bold mt-1">Sisa uang setelah semua pengeluaran tercatat</p>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">SERVIS SELESAI</span>
                <div class="h-8 w-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="wrench" class="h-4 w-4"></i>
                </div>
            </div>
            <h4 class="text-lg sm:text-2xl font-black text-slate-900">{{ $totalServis }} <span class="text-xs font-semibold text-slate-400">Transaksi</span></h4>
            <p class="text-[11px] text-indigo-600 font-bold mt-1">Total Unit Diperbaiki</p>
        </div>
    </div>

    {{-- Section 2: Chart & Summary --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6 sm:gap-8">
        {{-- Grafik Pendapatan Mingguan --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="bar-chart-2" class="h-5 w-5 text-rubick-blue"></i>
                        <span>Grafik Pendapatan Mingguan</span>
                    </h4>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Trend pemasukan harian dalam 7 hari terakhir</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-extrabold text-emerald-700 border border-emerald-200/60">
                    <i data-lucide="activity" class="h-3.5 w-3.5"></i> Real-Time
                </span>
            </div>

            <!-- Modern Bar Chart Container -->
            <div class="mt-4 flex h-64 sm:h-72 items-end gap-2 sm:gap-3 rounded-2xl bg-slate-50/80 p-4 sm:p-5 border border-slate-100">
                @foreach($weeklyData as $w)
                <div class="flex-1 flex flex-col items-center justify-end h-full gap-2 group">
                    {{-- Value Badge (Above Bar) --}}
                    <div class="text-center min-h-6 flex items-center justify-center">
                        @if($w['amount'] > 0)
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-rubick-blue border border-blue-200/80 shadow-xs">
                                Rp {{ number_format($w['amount'], 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-[10px] font-semibold text-slate-400">
                                Rp 0
                            </span>
                        @endif
                    </div>

                    {{-- Bar Column --}}
                    <div class="w-full flex-1 flex flex-col justify-end items-center max-w-10">
                        <div class="w-full rounded-2xl transition-all duration-300 {{ $w['amount'] > 0 ? 'bg-linear-to-t from-[#1b42b6] to-[#3b82f6] shadow-sm shadow-blue-600/30 group-hover:from-blue-800 group-hover:to-blue-600' : 'bg-slate-200/80 group-hover:bg-slate-300' }}" style="height: {{ $w['height'] }}%;"></div>
                    </div>

                    {{-- Day & Date Label (Below Bar) --}}
                    <div class="text-center pt-2 border-t border-slate-200/60 w-full">
                        <span class="block text-xs font-bold text-slate-800 uppercase tracking-wide">{{ $w['day'] }}</span>
                        <span class="block text-[10px] text-slate-400 font-medium">{{ $w['date'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Operasional --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <h4 class="text-base sm:text-lg font-bold text-slate-900 mb-1">Ringkasan Operasional</h4>
                <p class="text-xs text-slate-400 font-medium mb-4">Metrik kunci aktivitas operasional bengkel</p>
                
                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3.5 border border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                            <span class="font-semibold text-slate-700">Transaksi Selesai</span>
                        </div>
                        <span class="font-bold text-slate-900 bg-white px-2.5 py-1 rounded-xl border border-slate-200/80">{{ $totalServis }} Transaksi</span>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3.5 border border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="alert-triangle" class="h-4 w-4 text-amber-500"></i>
                            <span class="font-semibold text-slate-700">Stok Menipis (&le; 5)</span>
                        </div>
                        <span class="font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-200/60">{{ $stokHampirHabis }} Item</span>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3.5 border border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="shopping-bag" class="h-4 w-4 text-rose-500"></i>
                            <span class="font-semibold text-slate-700">Total Pembelian Sparepart</span>
                        </div>
                        <span class="font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-xl border border-rose-200/60">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 pt-2 sm:flex-row">
                <button type="button" onclick="openPayrollModal()" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 py-2.5 text-xs font-bold text-emerald-700 transition-colors hover:bg-emerald-100">
                    <i data-lucide="wallet" class="h-3.5 w-3.5"></i>
                    <span>Kelola Gaji</span>
                </button>
                <a href="{{ route('pembelian.index') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                    <i data-lucide="external-link" class="h-3.5 w-3.5 text-rubick-blue"></i>
                    <span>Kelola Pembelian Supplier</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Section 3: Tabel Rincian Laporan Keuangan (Unified Stream: Service, Pembelian & Catatan Manual) --}}
    <div class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>Rincian Arus Kas &amp; Transaksi</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-rubick-blue border border-blue-200/80">
                        {{ $reportItems->count() }} Record
                    </span>
                </h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Semua data transaksi otomatis dari Kasir &amp; Pembelian beserta catatan keuangan manual</p>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden space-y-3">
            @forelse($reportItems as $item)
            <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $item->kode }}</span>
                        <h4 class="text-sm font-bold text-slate-900 mt-0.5">{{ $item->keterangan }}</h4>
                        <p class="text-[11px] text-slate-500 font-medium">{{ $item->kategori }} &bull; {{ is_string($item->tanggal) ? $item->tanggal : $item->tanggal->format('d M Y H:i') }}</p>
                    </div>
                    @if($item->tipe === 'Pemasukan')
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full shrink-0">
                            + Pemasukan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full shrink-0">
                            - Pengeluaran
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-between border-t border-slate-200/60 pt-2 text-xs">
                    <span class="font-extrabold text-slate-900">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                    
                    <form action="{{ route('laporan.destroy', ['id' => $item->id, 'source' => $item->source]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold text-rose-500 hover:text-rose-700 bg-transparent border-0 p-0 cursor-pointer">
                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-6 text-center text-xs font-semibold text-slate-500">
                Belum ada data transaksi atau laporan keuangan. Klik tombol "Tambah Catatan Keuangan" untuk membuat catatan baru.
            </div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[1180px] text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="w-28 py-4 px-5 whitespace-nowrap">KODE / REF</th>
                        <th class="w-40 py-4 px-5 whitespace-nowrap">TANGGAL</th>
                        <th class="w-40 py-4 px-5">KATEGORI</th>
                        <th class="min-w-72 py-4 px-5">KETERANGAN &amp; SUMBER</th>
                        <th class="w-40 py-4 px-5 whitespace-nowrap">TIPE</th>
                        <th class="w-36 py-4 px-5 text-right whitespace-nowrap">NOMINAL</th>
                        <th class="w-28 py-4 px-5 text-center whitespace-nowrap">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium">
                    @forelse($reportItems as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-5 font-bold text-slate-700 whitespace-nowrap">
                            {{ $item->kode }}
                        </td>
                        <td class="py-4 px-5 text-slate-500 whitespace-nowrap">
                            {{ is_string($item->tanggal) ? $item->tanggal : $item->tanggal->format('d M Y H:i') }}
                        </td>
                        <td class="py-4 px-5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200/60">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="py-4 px-5 font-bold leading-5 text-slate-900">
                            {{ $item->keterangan }}
                        </td>
                        <td class="py-4 px-5 whitespace-nowrap">
                            @if($item->tipe === 'Pemasukan')
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-md">
                                    <i data-lucide="arrow-down-left" class="h-3 w-3"></i> Pemasukan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200/60 px-2 py-0.5 rounded-md">
                                    <i data-lucide="arrow-up-right" class="h-3 w-3"></i> Pengeluaran
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right font-black text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-5 text-center whitespace-nowrap">
                            <form action="{{ route('laporan.destroy', ['id' => $item->id, 'source' => $item->source]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data laporan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold text-rose-500 hover:text-rose-700 bg-transparent border-0 p-0 cursor-pointer transition-colors">
                                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-xs font-semibold text-slate-500">
                            Belum ada data transaksi atau laporan keuangan. Klik tombol "Tambah Catatan Keuangan" untuk membuat catatan baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Catatan Keuangan --}}
<div id="tambahLaporanModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
    <div class="relative w-full overflow-y-auto rounded-3xl border border-slate-100 bg-white p-6 shadow-xl sm:p-7" style="width: min(100%, 42rem); max-width: 42rem; max-height: 90vh; box-sizing: border-box;">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tambah Catatan Keuangan</h3>
                <p class="text-xs text-slate-400 font-medium">Catat pemasukan atau pengeluaran operasional manual</p>
            </div>
            <button onclick="closeModal('tambahLaporanModal')" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <form action="{{ route('laporan.store') }}" method="POST" class="mt-5 space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-xs font-bold text-slate-700">Tipe Transaksi</label>
                <select name="tipe" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue/20" required>
                    <option value="Pengeluaran">Pengeluaran (Biaya Operasional)</option>
                    <option value="Pemasukan">Pemasukan (Pendapatan Extra)</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold text-slate-700">Kategori</label>
                <select name="kategori" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue/20" required>
                    <option value="Operasional">Operasional Bengkel</option>
                    <option value="Gaji Karyawan">Gaji &amp; Bonus Karyawan</option>
                    <option value="Listrik & Air">Listrik, Air &amp; Utilitas</option>
                    <option value="Sewa Tempat">Sewa Tempat / Gedung</option>
                    <option value="Pembelian Tool">Pembelian Peralatan / Tool</option>
                    <option value="Pendapatan Extra">Pendapatan Extra / Servis Direct</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold text-slate-700">Keterangan / Deskripsi</label>
                <input type="text" name="keterangan" placeholder="Contoh: Bayar tagihan listrik PLN bulan ini" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue/20" required>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-700">Nominal (Rp)</label>
                    <input type="number" name="nominal" placeholder="0" min="0" step="1000" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue/20" required>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-700">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rubick-blue/20" required>
                </div>
            </div>

            <div class="mt-2 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <button type="button" onclick="closeModal('tambahLaporanModal')" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition-colors shadow-xs">
                    Simpan Catatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const reportCurrencyFormatter = new Intl.NumberFormat('id-ID');

    async function refreshReportTotals() {
        try {
            const response = await fetch(@json(route('laporan.summary')), {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store'
            });
            if (!response.ok) return;

            const totals = await response.json();
            document.getElementById('report-income-total').textContent = 'Rp ' + reportCurrencyFormatter.format(totals.pendapatan);
            document.getElementById('report-expense-total').textContent = 'Rp ' + reportCurrencyFormatter.format(totals.pengeluaran);

            const profitElement = document.getElementById('report-profit-total');
            profitElement.textContent = 'Rp ' + reportCurrencyFormatter.format(totals.laba_bersih);
            profitElement.classList.toggle('text-rose-600', totals.laba_bersih < 0);
            profitElement.classList.toggle('text-slate-900', totals.laba_bersih >= 0);
        } catch (error) {
            console.error('Gagal memperbarui ringkasan keuangan.', error);
        }
    }

    window.setInterval(refreshReportTotals, 10000);

    function openPayrollModal() {
        document.querySelector('#tambahLaporanModal select[name="tipe"]').value = 'Pengeluaran';
        document.querySelector('#tambahLaporanModal select[name="kategori"]').value = 'Gaji Karyawan';
        openModal('tambahLaporanModal');
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
