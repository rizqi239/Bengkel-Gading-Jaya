@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page_title', 'Tambah Produk Baru')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h4 class="text-lg font-bold text-slate-800">Form Tambah Produk</h4>
                <p class="text-xs text-slate-500">Isi data produk baru untuk stok bengkel</p>
            </div>
            <a href="{{ route('stock.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Kembali</a>
        </div>

        <form class="space-y-5" method="POST" action="{{ route('stock.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kode Barang</label>
                    <input type="text" value="{{ $code }}" readonly class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-600">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</label>
                    <select name="category" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="Oli" @selected(old('category', 'Oli') === 'Oli')>Oli</option>
                        <option value="Air Radiator" @selected(old('category') === 'Air Radiator')>Air Radiator</option>
                        <option value="Minyak Rem" @selected(old('category') === 'Minyak Rem')>Minyak Rem</option>
                        <option value="Kampas Rem" @selected(old('category') === 'Kampas Rem')>Kampas Rem</option>
                        <option value="Ban" @selected(old('category') === 'Ban')>Ban</option>
                        <option value="Aki" @selected(old('category') === 'Aki')>Aki</option>
                        <option value="Busi" @selected(old('category') === 'Busi')>Busi</option>
                        <option value="Filter Oli" @selected(old('category') === 'Filter Oli')>Filter Oli</option>
                        <option value="Filter Udara" @selected(old('category') === 'Filter Udara')>Filter Udara</option>
                        <option value="Rantai dan Gear" @selected(old('category') === 'Rantai dan Gear')>Rantai dan Gear</option>
                        <option value="Aksesoris Lainnya" @selected(old('category') === 'Aksesoris Lainnya')>Aksesoris Lainnya</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Produk / Sparepart</label>
                <input name="name" type="text" value="{{ old('name') }}" required maxlength="255" placeholder="Masukkan nama produk..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Stok</label>
                    <input name="quantity" type="number" min="0" value="{{ old('quantity') }}" placeholder="Jumlah produk" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    @error('quantity')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Harga Jual (Rp)</label>
                    <input name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" placeholder="Harga rupiah" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    @error('price')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Foto Barang</label>
                <div class="flex items-center gap-4">
                    <input id="photoInput" name="photo" type="file" accept="image/*" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none">
                    <div id="photoPreview" class="h-16 w-16 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-xs text-slate-400">Preview</div>
                </div>
                @error('photo')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('stock.index') }}" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    if (photoInput) {
        photoInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) {
                photoPreview.innerHTML = 'Preview';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(evt) {
                photoPreview.innerHTML = `<img src="${evt.target.result}" class="h-16 w-16 object-cover rounded-lg" alt="preview">`;
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection
