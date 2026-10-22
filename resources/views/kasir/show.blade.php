@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page_title', 'Detail Transaksi')

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800">{{ session('error') }}</div>
    @endif
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Transaksi #{{ $kasir->id }}</h3>
                <p class="text-sm text-slate-500">{{ $kasir->created_at ? $kasir->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($kasir->status !== 'Dibatalkan')
                    <form action="{{ route('kasir.update-status', $kasir) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Selesai">
                        <button type="submit" @disabled($kasir->status === 'Selesai') class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold text-white transition-colors {{ $kasir->status === 'Selesai' ? 'cursor-not-allowed bg-emerald-300' : 'bg-emerald-600 hover:bg-emerald-500' }}">
                            <i data-lucide="check" class="h-4 w-4"></i>
                            <span>Selesai</span>
                        </button>
                    </form>
                    <form action="{{ route('kasir.update-status', $kasir) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Draft">
                        <button type="submit" @disabled($kasir->status === 'Draft') class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-colors {{ $kasir->status === 'Draft' ? 'cursor-not-allowed bg-amber-100 text-amber-400' : 'bg-amber-500 text-white hover:bg-amber-400' }}">
                            <i data-lucide="clock-3" class="h-4 w-4"></i>
                            <span>Belum</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('kasir.print', $kasir) }}" class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-500">
                    <i data-lucide="printer" class="h-4 w-4"></i>
                    <span>Cetak</span>
                </a>
                <a href="{{ route('kasir.edit', $kasir) }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500">
                    <i data-lucide="edit" class="h-4 w-4"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('kasir.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <h4 class="text-sm font-bold text-slate-800 mb-3">Informasi Pelanggan</h4>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-slate-500">Nama Pelanggan</span>
                        <p class="font-semibold text-slate-800">{{ $kasir->customer_name ?? 'Pelanggan Umum' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Jenis Transaksi</span>
                        <p class="font-semibold text-slate-800">{{ $kasir->transaction_type }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Nomor Plat</span>
                        <p class="font-semibold text-slate-800">{{ $kasir->plate_number ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Model Kendaraan</span>
                        <p class="font-semibold text-slate-800">{{ $kasir->vehicle_model ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Deskripsi</span>
                        <p class="whitespace-pre-wrap font-semibold text-slate-800">{{ $kasir->description ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <h4 class="text-sm font-bold text-slate-800 mb-3">Informasi Pembayaran</h4>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-slate-500">Metode Pembayaran</span>
                        <p class="font-semibold text-slate-800">{{ $kasir->payment_method }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Status</span>
                        <p>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold border {{ $kasir->status === 'Selesai' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : ($kasir->status === 'Draft' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-rose-500 bg-rose-50 text-rose-700') }}">
                                {{ $kasir->status }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <h4 class="text-sm font-bold text-slate-800 mb-3">Item Transaksi</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                            <th class="py-3 px-4">Nama Item</th>
                            <th class="py-3 px-4 text-right">Jumlah</th>
                            <th class="py-3 px-4 text-right">Harga Satuan</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($kasir->items as $item)
                            <tr class="hover:bg-white">
                                <td class="py-4 px-4">{{ $item['name'] ?? 'Item' }}</td>
                                <td class="py-4 px-4 text-right">{{ $item['quantity'] ?? 1 }}</td>
                                <td class="py-4 px-4 text-right">Rp {{ number_format(($item['price'] ?? 0) / max($item['quantity'] ?? 1, 1), 0, ',', '.') }}</td>
                                <td class="py-4 px-4 text-right font-semibold text-slate-800">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 px-4 text-center text-slate-500">Tidak ada item dalam transaksi ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 w-full md:w-96">
                <div class="flex items-center justify-between text-lg font-bold text-slate-900">
                    <span>Total Transaksi</span>
                    <span>Rp {{ number_format($kasir->total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection