@extends('layouts.app')

@section('title', 'Pembelian / Restock Barang')
@section('page_title', 'Pembelian & Restock Barang')

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 sm:p-6 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Order</p>
            <h4 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-800">{{ $totalOrders }}</h4>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 sm:p-6 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Order Menunggu</p>
            <h4 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-800">{{ $waitingOrders }}</h4>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 sm:p-6 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Nilai Belanja</p>
            <h4 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-800">Rp {{ number_format($totalValue, 0, ',', '.') }}</h4>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white shadow-xs overflow-hidden">
        <div class="border-b border-slate-100 p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h4 class="text-lg font-bold text-slate-800">Daftar Pembelian</h4>
                <p class="text-xs text-slate-500">Pantau restock barang dari supplier</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <form action="{{ route('pembelian.index') }}" method="GET" class="flex items-center gap-2">
                    <label for="produk" class="sr-only">Filter produk</label>
                    <select id="produk" name="produk" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="" {{ $produkFilter === '' ? 'selected' : '' }}>Semua Produk</option>
                        <option value="oli" {{ $produkFilter === 'oli' ? 'selected' : '' }}>Oli</option>
                        <option value="kampas_rem" {{ $produkFilter === 'kampas_rem' ? 'selected' : '' }}>Kampas Rem</option>
                        <option value="ban" {{ $produkFilter === 'ban' ? 'selected' : '' }}>Ban</option>
                    </select>
                </form>
                <a href="{{ route('pembelian.export', ['produk' => $produkFilter]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100">
                    <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                    <span>Unduh Excel</span>
                </a>
                <a href="{{ route('pembelian.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-indigo-500 transition-colors shadow-xs">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span>Buat Order</span>
                </a>
            </div>
        </div>
        
        <!-- Mobile Card View -->
        <div class="md:hidden p-4 space-y-4">
            @forelse($pembelians as $pembelian)
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="shrink-0">
                            <h5 class="text-sm font-bold text-[#1e40af]">
                                <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($pembelian->supplier_address ?: $pembelian->supplier_name) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 hover:underline" title="Buka lokasi supplier di Google Maps">
                                    {{ $pembelian->supplier_name }}
                                    <i data-lucide="map-pin" class="h-3.5 w-3.5"></i>
                                </a>
                            </h5>
                            <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $pembelian->barang }}</p>
                        </div>
                        <div>
                            @if($pembelian->status === 'Menunggu')
                                <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 border border-amber-200">Menunggu</span>
                            @elseif($pembelian->status === 'Diterima')
                                <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">Diterima</span>
                            @else
                                <span class="rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 border border-rose-200">Dibatalkan</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs border-t border-slate-200/60 pt-4">
                        <div class="bg-white p-3 rounded-xl border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Total Harga</span>
                            <p class="font-bold text-slate-900 mt-0.5">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Jumlah & Tgl</span>
                            <p class="font-bold text-slate-700 mt-0.5 truncate">{{ $pembelian->jumlah }} &bull; {{ $pembelian->tanggal_pesan->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1 sm:flex sm:items-center sm:justify-end">
                        <a href="{{ route('pembelian.show', $pembelian) }}" class="inline-flex w-full items-center justify-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 sm:w-auto">
                            <i data-lucide="eye" class="h-3.5 w-3.5"></i> Show
                        </a>
                        <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($pembelian->supplier_address ?: $pembelian->supplier_name) }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center gap-1 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 sm:w-auto" title="Lihat lokasi supplier di Google Maps">
                            <i data-lucide="map-pin" class="h-3.5 w-3.5"></i> Lihat
                        </a>
                        <a href="{{ route('pembelian.edit', $pembelian) }}" class="inline-flex w-full items-center justify-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-600 hover:bg-indigo-100 sm:w-auto">
                            <i data-lucide="edit-3" class="h-3.5 w-3.5"></i> Edit
                        </a>
                        <form action="{{ route('pembelian.destroy', $pembelian) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-1 rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-100 sm:w-auto">
                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-500 text-xs font-medium">Belum ada data pembelian.</div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/70 text-xs font-bold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4">Barang</th>
                        <th class="px-6 py-4">Jumlah</th>
                        <th class="px-6 py-4">Total Harga</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Pesan</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembelians as $pembelian)
                        <tr class="hover:bg-slate-50/40">
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($pembelian->supplier_address ?: $pembelian->supplier_name) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-indigo-700 hover:underline" title="Buka lokasi supplier di Google Maps">
                                    {{ $pembelian->supplier_name }}
                                    <i data-lucide="map-pin" class="h-3.5 w-3.5"></i>
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ $pembelian->barang }}</td>
                            <td class="px-6 py-4">{{ $pembelian->jumlah }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($pembelian->status === 'Menunggu')
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
                                @elseif($pembelian->status === 'Diterima')
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Diterima</span>
                                @else
                                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $pembelian->tanggal_pesan->format('d M Y') }}</td>
                            <td class="px-6 py-4 flex items-center gap-2">
                                <a href="{{ route('pembelian.show', $pembelian) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                                    <i data-lucide="eye" class="h-3.5 w-3.5"></i> Show
                                </a>
                                <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($pembelian->supplier_address ?: $pembelian->supplier_name) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100" title="Lihat lokasi supplier di Google Maps">
                                    <i data-lucide="map-pin" class="h-3.5 w-3.5"></i> Lihat
                                </a>
                                <a href="{{ route('pembelian.edit', $pembelian) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-xs">Edit</a>
                                <form action="{{ route('pembelian.destroy', $pembelian) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">Belum ada data pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembelians->hasPages())
            <div class="border-t border-slate-100 p-4 flex justify-center">
                {{ $pembelians->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
