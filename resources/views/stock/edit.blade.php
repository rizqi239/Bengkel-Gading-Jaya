@extends('layouts.app')

@section('title', 'Edit Barang')
@section('page_title', 'Edit Data Barang')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h4 class="text-lg font-bold text-slate-800">Form Edit Barang</h4>
                <p class="text-xs text-slate-500">Perbarui informasi stok barang yang dipilih</p>
            </div>
            <a href="{{ route('stock.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Kembali</a>
        </div>

        <form class="space-y-5" method="POST" action="{{ route('stock.update', $stock) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kode Barang</label>
                    <input name="code" type="text" value="{{ old('code', $stock->code) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</label>
                    <select name="category" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="Oli" {{ old('category', $stock->category) === 'Oli' ? 'selected' : '' }}>Oli</option>
                        <option value="Air Radiator" {{ old('category', $stock->category) === 'Air Radiator' ? 'selected' : '' }}>Air Radiator</option>
                        <option value="Minyak Rem" {{ old('category', $stock->category) === 'Minyak Rem' ? 'selected' : '' }}>Minyak Rem</option>
                        <option value="Kampas Rem" {{ old('category', $stock->category) === 'Kampas Rem' ? 'selected' : '' }}>Kampas Rem</option>
                        <option value="Ban" {{ old('category', $stock->category) === 'Ban' ? 'selected' : '' }}>Ban</option>
                        <option value="Aki" {{ old('category', $stock->category) === 'Aki' ? 'selected' : '' }}>Aki</option>
                        <option value="Busi" {{ old('category', $stock->category) === 'Busi' ? 'selected' : '' }}>Busi</option>
                        <option value="Filter Oli" {{ old('category', $stock->category) === 'Filter Oli' ? 'selected' : '' }}>Filter Oli</option>
                        <option value="Filter Udara" {{ old('category', $stock->category) === 'Filter Udara' ? 'selected' : '' }}>Filter Udara</option>
                        <option value="Rantai dan Gear" {{ old('category', $stock->category) === 'Rantai dan Gear' ? 'selected' : '' }}>Rantai dan Gear</option>
                        <option value="Aksesoris Lainnya" {{ old('category', $stock->category) === 'Aksesoris Lainnya' ? 'selected' : '' }}>Aksesoris Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Barang / Sparepart</label>
                <input name="name" type="text" value="{{ old('name', $stock->name) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Stok</label>
                    <input name="quantity" type="number" min="0" value="{{ old('quantity', $stock->quantity) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Harga Jual (Rp)</label>
                    <input name="price" type="number" min="0" step="0.01" value="{{ old('price', $stock->price) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Foto Barang</label>
                <div class="flex items-center gap-4">
                    <input id="photoInput" name="photo" type="file" accept="image/*" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none">
                    <div id="photoPreview" class="h-16 w-16 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-xs text-slate-400">
                        @if($stock->photo)
                            <img src="{{ $stock->photo_url }}" alt="{{ $stock->name }}" class="h-16 w-16 object-cover rounded-lg">
                        @else
                            Preview
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('stock.index') }}" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
