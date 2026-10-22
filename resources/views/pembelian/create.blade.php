@extends('layouts.app')

@section('title', 'Buat Pembelian Baru')
@section('page_title', 'Buat Pembelian Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="rounded-2xl border border-slate-100 bg-white p-8 shadow-sm">
        <form action="{{ route('pembelian.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Supplier -->
                <div>
                    <label for="supplier_name" class="block text-sm font-semibold text-slate-800 mb-2">Nama Supplier</label>
                    <input type="text" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" placeholder="PT Sumber Jaya / CV Auto Parts" class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('supplier_name') ? 'border-rose-500' : 'border-slate-200' }}">
                    @error('supplier_name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="supplier_address" class="block text-sm font-semibold text-slate-800 mb-2">Alamat Supplier (Opsional)</label>
                    <textarea id="supplier_address" name="supplier_address" rows="2" placeholder="Axc Summarecon Bekasi, Blok VE 11, RT.003/RW.002, Marga Mulya, Bekasi Utara, Jawa Barat 17143" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('supplier_address') }}</textarea>
                    @error('supplier_address')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barang -->
                <div>
                    <label for="barang" class="block text-sm font-semibold text-slate-800 mb-2">Nama Barang</label>
                    <select id="barang" name="barang" class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('barang') ? 'border-rose-500' : 'border-slate-200' }}">
                        <option value="" disabled {{ old('barang') ? '' : 'selected' }}>Pilih nama barang</option>
                        @foreach (['Ban', 'Minyak Rem', 'Oli Mesin', 'Kampas Rem', 'Busi', 'Filter Oli', 'Filter Udara', 'Coolant', 'Rantai Motor', 'Gear Set'] as $namaBarang)
                            <option value="{{ $namaBarang }}" {{ old('barang') === $namaBarang ? 'selected' : '' }}>{{ $namaBarang }}</option>
                        @endforeach
                    </select>
                    @error('barang')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah & Harga -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="jumlah" class="block text-sm font-semibold text-slate-800 mb-2">Jumlah</label>
                        <input type="text" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" placeholder="20 botol / 15 pcs / 10 box" class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('jumlah') ? 'border-rose-500' : 'border-slate-200' }}">
                        @error('jumlah')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="harga_satuan" class="block text-sm font-semibold text-slate-800 mb-2">Harga Satuan (Rp)</label>
                        <input type="number" id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan') }}" placeholder="50000" step="0.01" class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('harga_satuan') ? 'border-rose-500' : 'border-slate-200' }}">
                        @error('harga_satuan')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal_pesan" class="block text-sm font-semibold text-slate-800 mb-2">Tanggal Pesan</label>
                        <input type="date" id="tanggal_pesan" name="tanggal_pesan" value="{{ old('tanggal_pesan', now()->format('Y-m-d')) }}" class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('tanggal_pesan') ? 'border-rose-500' : 'border-slate-200' }}">
                        @error('tanggal_pesan')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_terima" class="block text-sm font-semibold text-slate-800 mb-2">Tanggal Terima (Opsional)</label>
                        <input type="date" id="tanggal_terima" name="tanggal_terima" value="{{ old('tanggal_terima') }}" class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('tanggal_terima') ? 'border-rose-500' : 'border-slate-200' }}">
                        @error('tanggal_terima')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-800 mb-2">Status</label>
                    <select id="status" name="status" class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('status') ? 'border-rose-500' : 'border-slate-200' }}">
                        <option value="Menunggu" {{ old('status') === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Diterima" {{ old('status') === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="Dibatalkan" {{ old('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-800 mb-2">Keterangan (Opsional)</label>
                    <textarea id="keterangan" name="keterangan" rows="3" placeholder="Catatan tambahan untuk pembelian ini..." class="w-full rounded-xl border px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 {{ $errors->has('keterangan') ? 'border-rose-500' : 'border-slate-200' }}">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-between gap-4">
                <a href="{{ route('pembelian.index') }}" class="rounded-xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Kembali
                </a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-8 py-3 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">
                    Simpan Pembelian
                </button>
            </div>
        </form>
    </div>
</div>
@endsection