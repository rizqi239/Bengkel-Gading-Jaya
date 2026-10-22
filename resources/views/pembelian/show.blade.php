@extends('layouts.app')

@section('title', 'Detail Pembelian')
@section('page_title', 'Detail Pembelian')

@section('content')
<div class="max-w-3xl mx-auto">
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 p-6 flex items-start justify-between gap-4">
            <div>
                <h4 class="text-lg font-bold text-slate-800">{{ $pembelian->barang }}</h4>
                <p class="mt-1 text-sm text-slate-500">Detail order pembelian</p>
            </div>
            @if($pembelian->status === 'Menunggu')
                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
            @elseif($pembelian->status === 'Diterima')
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Diterima</span>
            @else
                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Dibatalkan</span>
            @endif
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 p-6">
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Supplier</dt>
                <dd class="mt-1 font-semibold text-slate-800">{{ $pembelian->supplier_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Alamat Supplier</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $pembelian->supplier_address ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Barang</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $pembelian->barang }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $pembelian->jumlah }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Harga Satuan</dt>
                <dd class="mt-1 text-sm text-slate-700">Rp {{ number_format($pembelian->harga_satuan, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Harga</dt>
                <dd class="mt-1 text-sm font-bold text-slate-800">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal Pesan</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $pembelian->tanggal_pesan->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal Terima</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $pembelian->tanggal_terima?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Keterangan</dt>
                <dd class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $pembelian->keterangan ?: '-' }}</dd>
            </div>
        </dl>

        <div class="border-t border-slate-100 p-6 flex items-center justify-between gap-3">
            <a href="{{ route('pembelian.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
            <div class="flex items-center gap-2">
                @if($pembelian->status === 'Menunggu')
                    <form action="{{ route('pembelian.complete', $pembelian) }}" method="POST" onsubmit="return confirm('Tandai pembelian ini sebagai selesai?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500">
                            <i data-lucide="check" class="h-4 w-4"></i> Selesai
                        </button>
                    </form>
                @endif
                <a href="{{ route('pembelian.edit', $pembelian) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                    <i data-lucide="edit-3" class="h-4 w-4"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection