@extends('layouts.app')

@section('title', 'Stok Barang')
@section('page_title', 'Stok Barang')

@section('content')
<div class="space-y-8">
    {{-- Alert Error Notification --}}
    @if(session('error'))
    <div class="rounded-2xl bg-rose-50 border border-rose-200/80 p-4 text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="h-5 w-5 text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
    </div>
    @endif
    @if(session('success'))
    <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50 p-4 text-xs font-bold text-emerald-800">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="rounded-2xl bg-rose-50 border border-rose-200/80 p-4 text-rose-800 text-xs font-bold">
        {{ $errors->first() }}
    </div>
    @endif

    @php
        $currentStockUserRole = strtolower(Illuminate\Support\Facades\Auth::check() ? (Illuminate\Support\Facades\Auth::user()->role ?? '') : '');
        $isStockEditRestricted = in_array($currentStockUserRole, ['mekanik', 'kasir']);
        $stockCategories = $stocks->pluck('category')
            ->filter(fn ($category) => is_string($category) && trim($category) !== '')
            ->mapWithKeys(fn ($category) => [strtolower(trim($category)) => trim($category)])
            ->sort();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="box" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Jenis Barang</p>
                <h4 class="text-2xl font-bold text-slate-800">{{ $stocks->count() }} <span class="text-xs text-slate-400 font-medium">Item</span></h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="package-x" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Habis</p>
                <h4 class="text-2xl font-bold text-slate-800">{{ $stocks->where('quantity', 0)->count() }} <span class="text-xs text-rose-500 font-medium">Item</span></h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <i data-lucide="alert-circle" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Menipis</p>
                <h4 class="text-2xl font-bold text-slate-800">{{ $stocks->where('quantity', '<=', 5)->count() }} <span class="text-xs text-amber-700 font-medium">Item</span></h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                <input type="text" placeholder="Cari nama sparepart, kode barang, kategori..." class="w-full rounded-2xl border border-slate-200 pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <select id="stockCategoryFilter" class="rounded-2xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua Kategori</option>
                    @foreach($stockCategories as $categoryValue => $categoryLabel)
                        <option value="{{ $categoryValue }}">{{ $categoryLabel }}</option>
                    @endforeach
                </select>

                <a href="{{ route('stock.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-indigo-500 shadow-sm">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span>Tambah Barang</span>
                </a>
            </div>
        </div>

        <div class="md:hidden p-4 space-y-4">
            @forelse($stocks as $stock)
                <div data-stock-filter-item data-stock-category="{{ strtolower(trim($stock->category ?? '')) }}" class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-3xl bg-slate-100 border border-slate-200">
                            @if($stock->photo)
                                <img src="{{ $stock->photo_url }}" alt="{{ $stock->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-slate-400">No Foto</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ preg_replace('/^SP_(\d+)$/i', 'SP-$1', $stock->code) }}</p>
                                    <h3 class="mt-1 text-base font-bold text-slate-900">{{ $stock->name }}</h3>
                                </div>
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">{{ $stock->category ?? 'Umum' }}</span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                <div class="rounded-2xl bg-white p-3 border border-slate-100">
                                    <p class="text-[10px] uppercase tracking-wide text-slate-400">Stok</p>
                                    <p class="mt-2 font-semibold text-slate-800">{{ $stock->quantity ?? 0 }} Pcs</p>
                                </div>
                                <div class="rounded-2xl bg-white p-3 border border-slate-100">
                                    <p class="text-[10px] uppercase tracking-wide text-slate-400">Harga</p>
                                    <p class="mt-2 font-semibold text-slate-800">Rp {{ number_format($stock->price ?? 0,0,',','.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($isStockEditRestricted)
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-400 border border-slate-200/80 cursor-not-allowed" title="Role Mekanik dan Kasir tidak diizinkan untuk mengedit stok">
                                <i data-lucide="lock" class="h-3.5 w-3.5"></i> Terkunci
                            </span>
                        @else
                            <a href="{{ route('stock.edit', $stock) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-600 transition-colors hover:bg-indigo-50">Edit</a>
                            <button type="button" data-increase-url="{{ route('stock.increase', $stock) }}" data-stock-name="{{ $stock->name }}" data-stock-quantity="{{ $stock->quantity ?? 0 }}" class="stock-increase-button inline-flex items-center justify-center rounded-full border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-600 transition-colors hover:bg-emerald-50">Tambah Stok</button>
                            <form action="{{ route('stock.destroy', $stock) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-500 transition-colors hover:bg-rose-50">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-500">
                    Belum ada data stok barang.
                </div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-400 uppercase">
                        <th class="py-4 px-6">Kode</th>
                        <th class="py-4 px-6">Foto</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6 text-center">Stok</th>
                        <th class="py-4 px-6">Harga Jual</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @if($stocks->count())
                        @foreach($stocks as $stock)
                            <tr data-stock-filter-item data-stock-category="{{ strtolower(trim($stock->category ?? '')) }}" class="hover:bg-slate-50/40 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-800">{{ preg_replace('/^SP_(\d+)$/i', 'SP-$1', $stock->code) }}</td>
                                <td class="py-4 px-6">
                                    @if($stock->photo)
                                        <img src="{{ $stock->photo_url }}" alt="{{ $stock->name }}" class="h-16 w-16 rounded-lg object-cover border border-slate-100">
                                    @else
                                        <div class="h-16 w-16 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-sm text-slate-400">No Foto</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">{{ $stock->name }}</td>
                                <td class="py-4 px-6">{{ $stock->category ?? '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $stock->quantity <= 5 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">{{ $stock->quantity ?? 0 }} Pcs</span>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-700">Rp {{ number_format($stock->price ?? 0,0,',','.') }}</td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    @if($isStockEditRestricted)
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-400 border border-slate-200/80 cursor-not-allowed" title="Role Mekanik dan Kasir tidak diizinkan untuk mengedit stok">
                                            <i data-lucide="lock" class="h-3 w-3"></i> Terkunci
                                        </span>
                                    @else
                                        <a href="{{ route('stock.edit', $stock) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">Edit</a>
                                        <button type="button" data-increase-url="{{ route('stock.increase', $stock) }}" data-stock-name="{{ $stock->name }}" data-stock-quantity="{{ $stock->quantity ?? 0 }}" class="stock-increase-button text-xs font-semibold text-emerald-600 hover:text-emerald-800 hover:underline">Tambah Stok</button>
                                        <form action="{{ route('stock.destroy', $stock) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700 hover:underline">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-800">-</td>
                            <td class="py-4 px-6">
                                <div class="h-16 w-16 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-sm text-slate-400">No Foto</div>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-800">Belum ada data</td>
                            <td class="py-4 px-6">-</td>
                            <td class="py-4 px-6 text-center">-</td>
                            <td class="py-4 px-6 font-semibold text-slate-700">-</td>
                            <td class="py-4 px-6 text-center space-x-2">-</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="stockIncreaseModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="stockIncreaseTitle">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6" style="width: min(100%, 28rem); box-sizing: border-box;">
        <div class="mb-5 flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <i data-lucide="package-plus" class="h-5 w-5"></i>
                </div>
                <div>
                    <h2 id="stockIncreaseTitle" class="text-lg font-bold text-slate-800">Tambah Stok</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Masukkan jumlah barang yang baru masuk</p>
                </div>
            </div>
            <button type="button" id="stockIncreaseClose" class="rounded-lg p-1 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700" aria-label="Tutup">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="mb-5 flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Barang</p>
                <p id="stockIncreaseName" class="mt-1 truncate text-sm font-bold text-slate-800"></p>
            </div>
            <div class="shrink-0 text-right">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Stok Saat Ini</p>
                <p id="stockIncreaseCurrent" class="mt-1 text-sm font-bold text-slate-700"></p>
            </div>
        </div>
        <form id="stockIncreaseForm" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="stockIncreaseQuantity" class="mb-2 block text-sm font-semibold text-slate-700">Jumlah yang ditambahkan</label>
                <div class="relative">
                    <input id="stockIncreaseQuantity" name="quantity" type="number" min="1" step="1" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-16 text-lg font-semibold text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="0">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-400">pcs</span>
                </div>
                <p id="stockIncreasePreview" class="mt-2 min-h-5 text-sm font-semibold text-emerald-700"></p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" id="stockIncreaseCancel" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-50">Batal</button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Simpan Stok
                </button>
            </div>
        </form>
    </div>
</div>

<div id="photoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="relative max-w-xl w-full mx-4">
        <button id="photoModalClose" class="absolute -top-3 -right-3 bg-white rounded-full p-2 shadow-md">✕</button>
        <div class="bg-white rounded-xl overflow-hidden">
            <img id="photoModalImg" src="" alt="" class="w-full h-auto object-contain bg-slate-100">
            <div id="photoModalCaption" class="p-3 text-sm text-slate-600"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function(){
        const categoryFilter = document.getElementById('stockCategoryFilter');
        categoryFilter.addEventListener('change', function(){
            const selectedCategory = categoryFilter.value;
            document.querySelectorAll('[data-stock-filter-item]').forEach(function(item){
                item.classList.toggle('hidden', selectedCategory !== '' && item.dataset.stockCategory !== selectedCategory);
            });
        });

        const stockModal = document.getElementById('stockIncreaseModal');
        const stockForm = document.getElementById('stockIncreaseForm');
        const stockName = document.getElementById('stockIncreaseName');
        const stockCurrent = document.getElementById('stockIncreaseCurrent');
        const stockQuantity = document.getElementById('stockIncreaseQuantity');
        const stockPreview = document.getElementById('stockIncreasePreview');
        let currentQuantity = 0;

        stockQuantity.addEventListener('input', function(){
            const addedQuantity = Number(stockQuantity.value);
            stockPreview.textContent = addedQuantity > 0
                ? 'Stok setelah ditambahkan: ' + (currentQuantity + addedQuantity) + ' pcs'
                : '';
        });

        function closeStockModal() {
            stockModal.classList.remove('flex');
            stockModal.classList.add('hidden');
        }

        document.querySelectorAll('.stock-increase-button').forEach(function(button){
            button.addEventListener('click', function(){
                stockForm.action = button.dataset.increaseUrl;
                stockName.textContent = button.dataset.stockName;
                currentQuantity = Number(button.dataset.stockQuantity);
                stockCurrent.textContent = 'Stok saat ini: ' + currentQuantity + ' pcs';
                stockQuantity.value = '';
                stockPreview.textContent = '';
                stockModal.classList.remove('hidden');
                stockModal.classList.add('flex');
                stockQuantity.focus();
            });
        });

        document.getElementById('stockIncreaseClose').addEventListener('click', closeStockModal);
        document.getElementById('stockIncreaseCancel').addEventListener('click', closeStockModal);
        stockModal.addEventListener('click', function(event){
            if (event.target === stockModal) closeStockModal();
        });

        const modal = document.getElementById('photoModal');
        const modalImg = document.getElementById('photoModalImg');
        const modalCaption = document.getElementById('photoModalCaption');
        const closeBtn = document.getElementById('photoModalClose');

        function openModal(src, alt) {
            modalImg.src = src;
            modalImg.alt = alt || '';
            modalCaption.textContent = alt || '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            modalImg.src = '';
            modalImg.alt = '';
            modalCaption.textContent = '';
        }

        document.addEventListener('click', function(e){
            const btn = e.target.closest('button[data-photo-src]');
            if (btn) {
                const src = btn.getAttribute('data-photo-src');
                const alt = btn.getAttribute('data-photo-alt');
                openModal(src, alt);
            }
        });

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        modal?.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
    })();
</script>
@endsection
