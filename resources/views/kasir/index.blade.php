@extends('layouts.app')

@section('title', 'Kasir / Transaksi')
@section('page_title', 'Kasir & Transaksi')

@section('content')
<div class="space-y-8">
    {{-- Alert Error Notification --}}
    @if(session('error'))
    <div class="rounded-2xl bg-rose-50 border border-rose-200/80 p-4 text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs mb-4">
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="h-5 w-5 text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
    </div>
    @endif

    @php
        $currentKasirUserRole = strtolower(Illuminate\Support\Facades\Auth::check() ? (Illuminate\Support\Facades\Auth::user()->role ?? '') : '');
        $isKasirEditRestricted = in_array($currentKasirUserRole, ['mekanik', 'kasir']);
    @endphp
    <div class="flex flex-col gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h4 class="text-lg font-bold text-slate-800">Point of Sale</h4>
                    <p class="text-xs text-slate-500">Kelola transaksi jasa dan penjualan sparepart dengan cepat.</p>
                </div>
                <a href="{{ route('kasir.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span>Transaksi Baru</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-4 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h4 class="text-sm font-bold text-slate-800">Daftar Transaksi Terbaru</h4>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <form action="{{ route('kasir.index') }}" method="GET">
                        <label for="transactionTypeFilter" class="sr-only">Filter jenis transaksi</label>
                        <select id="transactionTypeFilter" name="type" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 sm:w-auto">
                            <option value="all" {{ $transactionType === 'all' ? 'selected' : '' }}>Semua Transaksi</option>
                            <option value="servis" {{ $transactionType === 'servis' ? 'selected' : '' }}>Servis</option>
                            <option value="produk" {{ $transactionType === 'produk' ? 'selected' : '' }}>Pembelian Produk</option>
                        </select>
                    </form>
                    <a href="{{ route('kasir.export', ['type' => $transactionType]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100">
                        <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                        <span>Unduh Excel (XLSX)</span>
                    </a>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-3 mt-4">
                @forelse($transactions as $transaction)
                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h5 class="font-bold text-slate-900 text-sm">{{ $transaction->customer_name ?? 'Pelanggan Umum' }}</h5>
                                <p class="text-xs text-slate-400 font-medium">{{ $transaction->plate_number ? $transaction->plate_number . ' • ' . $transaction->vehicle_model : 'Tanpa plat nomor' }}</p>
                            </div>
                            <span class="inline-flex rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-xs font-extrabold shrink-0">
                                {{ $transaction->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-200/60 pt-3">
                            <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400">Total Transaksi</span>
                                <p class="font-bold text-[#1e40af] text-sm mt-0.5">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400">Metode & Tipe</span>
                                <p class="font-bold text-slate-700 mt-0.5 truncate">{{ $transaction->transaction_type }} ({{ $transaction->payment_method }})</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-1.5 pt-1">
                            <a href="{{ route('kasir.show', $transaction) }}" class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100">Lihat</a>
                            <a href="{{ route('kasir.print', $transaction) }}" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-100">Cetak</a>
                            
                            @if($isKasirEditRestricted)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-400 border border-slate-200/80 cursor-not-allowed" title="Role Mekanik dan Kasir tidak diizinkan untuk mengedit">
                                    <i data-lucide="lock" class="h-3.5 w-3.5"></i> Terkunci
                                </span>
                            @else
                                <a href="{{ route('kasir.edit', $transaction) }}" class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100">Edit</a>
                                <form action="{{ route('kasir.destroy', $transaction) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-500 text-xs">Belum ada transaksi.</div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="mt-6 hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-slate-800">{{ $transaction->customer_name ?? 'Pelanggan Umum' }}</div>
                                    <div class="text-xs text-slate-400">{{ $transaction->plate_number ? $transaction->plate_number . ' • ' . $transaction->vehicle_model : '-' }}</div>
                                </td>
                                <td class="py-4 px-4">{{ $transaction->transaction_type }}</td>
                                <td class="py-4 px-4">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td class="py-4 px-4">{{ $transaction->payment_method }}</td>
                                <td class="py-4 px-4">{{ $transaction->status }}</td>
                                <td class="py-4 px-4 flex flex-wrap gap-2">
                                    <a href="{{ route('kasir.show', $transaction) }}" class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100">Lihat</a>
                                    <a href="{{ route('kasir.print', $transaction) }}" class="rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-600 hover:bg-green-100">Cetak</a>
                                    
                                    @if($isKasirEditRestricted)
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-400 border border-slate-200/80 cursor-not-allowed" title="Role Mekanik dan Kasir tidak diizinkan untuk mengedit">
                                            <i data-lucide="lock" class="h-3 w-3"></i> Terkunci
                                        </span>
                                    @else
                                        <a href="{{ route('kasir.edit', $transaction) }}" class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100">Edit</a>
                                        <form action="{{ route('kasir.destroy', $transaction) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-slate-500">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
