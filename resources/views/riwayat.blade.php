@extends('layouts.app')

@section('title', 'Riwayat Transaksi & Servis')
@section('page_title', 'Riwayat Transaksi & Servis')

@section('content')
<div class="space-y-6 sm:space-y-8">
    {{-- Top Summary Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">TRANSAKSI SERVIS</span>
                <div class="h-8 w-8 rounded-xl bg-blue-50 text-rubick-blue flex items-center justify-center">
                    <i data-lucide="wrench" class="h-4 w-4"></i>
                </div>
            </div>
            <h4 class="text-lg sm:text-2xl font-black text-slate-900">{{ $totalServis }} <span class="text-xs font-semibold text-slate-400">Unit</span></h4>
            <p class="text-[11px] text-blue-600 font-bold mt-1">Perbaikan &amp; Jasa Servis</p>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">TOTAL PELANGGAN</span>
                <div class="h-8 w-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="users" class="h-4 w-4"></i>
                </div>
            </div>
            <h4 class="text-lg sm:text-2xl font-black text-slate-900">{{ $totalCustomers }} <span class="text-xs font-semibold text-slate-400">Orang</span></h4>
            <p class="text-[11px] text-purple-600 font-bold mt-1">Pelanggan Terdaftar</p>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">KENDARAAN TERLAYANI</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="car" class="h-4 w-4"></i>
                </div>
            </div>
            <h4 class="text-lg sm:text-2xl font-black text-slate-900">{{ $totalVehicles }} <span class="text-xs font-semibold text-slate-400">Model</span></h4>
            <p class="text-[11px] text-emerald-600 font-bold mt-1">Motor &amp; Mobil Terdata</p>
        </div>
    </div>

    {{-- Main Container --}}
    <div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-xs space-y-5">
        {{-- Header & Search/Filter Section --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h4 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>Daftar Riwayat Servis</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-rubick-blue border border-blue-200/80">
                        {{ $transactions->count() }} Record
                    </span>
                </h4>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Tinjau riwayat servis yang telah selesai</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form action="{{ route('riwayat') }}" method="GET" class="flex flex-1 min-w-0 gap-2">
                    <div class="relative flex-1 min-w-50">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pelanggan, plat, atau transaksi..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-xs font-semibold text-slate-700 outline-hidden focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-600/20">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-rubick-blue px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-blue-800 shadow-xs cursor-pointer">
                        <i data-lucide="search" class="h-3.5 w-3.5"></i>
                        <span>Cari</span>
                    </button>
                </form>

                <a href="{{ route('riwayat.export', request()->query()) }}" class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 transition-colors hover:bg-emerald-100 border border-emerald-200/80 shrink-0">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i>
                    <span>Ekspor CSV</span>
                </a>
            </div>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-3">
            @forelse($transactions as $transaction)
                @php
                    $isServis = in_array($transaction->transaction_type, $serviceTypes);
                @endphp
                <div class="rounded-2xl border {{ $isServis ? 'border-blue-200/60 bg-blue-50/30' : 'border-amber-200/60 bg-amber-50/30' }} p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            @if($isServis)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700 border border-blue-200">
                                    <i data-lucide="wrench" class="h-3 w-3"></i> Servis: {{ $transaction->transaction_type }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                    <i data-lucide="shopping-bag" class="h-3 w-3"></i> Pembelian Produk
                                </span>
                            @endif

                            <h5 class="font-bold text-slate-900 text-sm mt-1.5">{{ $transaction->customer_name ?? 'Pelanggan Umum' }}</h5>
                            <p class="text-xs text-slate-500 font-medium">
                                @if($isServis)
                                    {{ $transaction->plate_number ? $transaction->plate_number . ' • ' . $transaction->vehicle_model : 'Tanpa Plat' }}
                                @else
                                    <span class="text-slate-400 italic">Pembelian Sparepart Direct</span>
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-[10px] font-extrabold shrink-0 border border-emerald-200/80">
                            {{ $transaction->status }}
                        </span>
                    </div>

                    @if(!empty($transaction->items) && count($transaction->items) > 0)
                        <div class="text-[11px] bg-white p-2.5 rounded-xl border border-slate-100 text-slate-600">
                            <span class="font-bold text-slate-700 block mb-1">Item / Barang:</span>
                            <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                                @foreach(array_slice($transaction->items, 0, 3) as $item)
                                    <li>{{ $item['name'] ?? 'Item' }} (x{{ $item['quantity'] ?? 1 }})</li>
                                @endforeach
                                @if(count($transaction->items) > 3)
                                    <li class="italic text-slate-400">+{{ count($transaction->items) - 3 }} item lainnya</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <div class="flex items-center justify-between border-t border-slate-200/60 pt-2.5 text-xs">
                        <span class="text-slate-400 font-medium">{{ $transaction->created_at->format('d M Y H:i') }}</span>
                        <span class="font-black text-slate-900 text-sm">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-500 text-xs font-semibold bg-slate-50 rounded-2xl border border-slate-100">
                    Belum ada data riwayat transaksi yang sesuai.
                </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="py-3 px-4">NO</th>
                        <th class="py-3 px-4">TANGGAL</th>
                        <th class="py-3 px-4">KATEGORI &amp; JENIS</th>
                        <th class="py-3 px-4">PELANGGAN</th>
                        <th class="py-3 px-4">KENDARAAN &amp; PLAT</th>
                        <th class="py-3 px-4">RINCIAN ITEM</th>
                        <th class="py-3 px-4 text-right">TOTAL</th>
                        <th class="py-3 px-4 text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transactions as $transaction)
                        @php
                            $isServis = in_array($transaction->transaction_type, $serviceTypes);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-500">{{ $loop->iteration }}</td>
                            <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                {{ $transaction->created_at->format('d M Y') }}
                                <span class="block text-[10px] text-slate-400">{{ $transaction->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($isServis)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200/80">
                                        <i data-lucide="wrench" class="h-3.5 w-3.5 text-blue-600"></i> Servis
                                    </span>
                                    <span class="block text-[10px] font-bold text-slate-500 mt-1">{{ $transaction->transaction_type }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200/80">
                                        <i data-lucide="shopping-bag" class="h-3.5 w-3.5 text-amber-600"></i> Produk
                                    </span>
                                    <span class="block text-[10px] font-bold text-slate-400 mt-1">Penjualan Barang</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $transaction->customer_name ?? 'Pelanggan Umum' }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $transaction->payment_method }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($isServis && ($transaction->plate_number || $transaction->vehicle_model))
                                    <span class="font-bold text-slate-800 block">{{ $transaction->plate_number ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-500 font-medium">{{ $transaction->vehicle_model ?? '-' }}</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Direct Purchase</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                @if(!empty($transaction->items) && count($transaction->items) > 0)
                                    <div class="text-[11px] text-slate-600 truncate">
                                        @foreach(array_slice($transaction->items, 0, 2) as $item)
                                            <span class="inline-block bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md mr-1 mb-1 border border-slate-200/60">
                                                {{ $item['name'] ?? 'Item' }} (x{{ $item['quantity'] ?? 1 }})
                                            </span>
                                        @endforeach
                                        @if(count($transaction->items) > 2)
                                            <span class="text-[10px] text-slate-400 font-bold">+{{ count($transaction->items) - 2 }} barang</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-full">
                                    <i data-lucide="check-circle-2" class="h-3 w-3"></i> {{ $transaction->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-xs font-semibold text-slate-500">
                                Belum ada riwayat transaksi yang sesuai filter atau pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

