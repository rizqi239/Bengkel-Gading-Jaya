@extends('layouts.app')

@section('title', 'Cetak Transaksi')

@section('content')
<div class="space-y-6">
    <div class="no-print flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Cetak Transaksi #{{ $kasir->id }}</h3>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500 shadow-xs cursor-pointer">
                <i data-lucide="printer" class="h-4 w-4"></i>
                <span>Cetak Sekarang</span>
            </button>
            <a href="{{ route('kasir.show', $kasir) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300 shadow-xs">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="print-area bg-white rounded-lg border border-slate-200 p-8" style="max-width: 800px; margin: 0 auto;">
        <!-- Header -->
        <div class="mb-8 text-center border-b border-slate-300 pb-4">
            <h1 class="text-2xl font-bold text-slate-900">BENGKEL Gading Jaya</h1>
            <p class="text-sm text-slate-600">Jl. Contoh No. 123, Kota</p>
            <p class="text-sm text-slate-600">Telp: 0812-3456-7890</p>
        </div>

        <!-- Invoice Title -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-slate-900">INVOICE / BUKTI TRANSAKSI</h2>
            <p class="text-sm text-slate-600">#{{ str_pad($kasir->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <!-- Transaction Info -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <p class="text-slate-500">Tanggal</p>
                <p class="font-semibold text-slate-900">{{ $kasir->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-slate-500">Jam</p>
                <p class="font-semibold text-slate-900">{{ $kasir->created_at->format('H:i:s') }}</p>
            </div>
            <div>
                <p class="text-slate-500">Jenis Transaksi</p>
                <p class="font-semibold text-slate-900">{{ $kasir->transaction_type }}</p>
            </div>
            <div>
                <p class="text-slate-500">Status</p>
                <p class="font-semibold text-slate-900">{{ $kasir->status }}</p>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="mb-6 border-t border-slate-300 pt-4">
            <h3 class="font-bold text-slate-900 mb-2">DATA PELANGGAN</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500">Nama Pelanggan</p>
                    <p class="font-semibold text-slate-900">{{ $kasir->customer_name ?? 'Pelanggan Umum' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Nomor Plat</p>
                    <p class="font-semibold text-slate-900">{{ $kasir->plate_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Model Kendaraan</p>
                    <p class="font-semibold text-slate-900">{{ $kasir->vehicle_model ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-6 border-t border-slate-300 pt-4">
            <h3 class="font-bold text-slate-900 mb-3">DETAIL ITEM</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-300">
                        <th class="text-left py-2 font-bold text-slate-900">Item</th>
                        <th class="text-right py-2 font-bold text-slate-900">Qty</th>
                        <th class="text-right py-2 font-bold text-slate-900">Harga</th>
                        <th class="text-right py-2 font-bold text-slate-900">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kasir->items as $item)
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-900">{{ $item['name'] ?? 'Item' }}</td>
                            <td class="text-right py-2 text-slate-900">{{ $item['quantity'] }}</td>
                            <td class="text-right py-2 text-slate-900">Rp {{ number_format($item['price'] / ($item['quantity'] ?? 1), 0, ',', '.') }}</td>
                            <td class="text-right py-2 font-semibold text-slate-900">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-500">Tidak ada item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Payment Summary -->
        <div class="mb-6 border-t border-slate-300 pt-4">
            <div class="flex justify-end mb-3">
                <div class="w-64">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($kasir->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-300">
                        <span class="text-lg font-bold text-slate-900">Total</span>
                        <span class="text-lg font-bold text-slate-900">Rp {{ number_format($kasir->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-sm">
                <p class="text-slate-500">Metode Pembayaran</p>
                <p class="font-semibold text-slate-900">{{ $kasir->payment_method }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center border-t border-slate-300 pt-4 text-xs text-slate-500">
            <p>Terima kasih telah berbelanja di Bengkel GMJ</p>
            <p>Cetak tanggal: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        aside,
        header,
        nav,
        #sidebar-backdrop,
        #profile-modal,
        .no-print {
            display: none !important;
        }

        body, html {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            min-height: auto !important;
            height: auto !important;
            overflow: visible !important;
        }

        main {
            padding: 0 !important;
            margin: 0 !important;
            overflow: visible !important;
            display: block !important;
        }

        .space-y-6 {
            margin: 0 !important;
            padding: 0 !important;
        }

        .print-area {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 auto !important;
            width: 100% !important;
            max-width: 100% !important;
            page-break-inside: avoid;
        }
    }
</style>
@endsection
