@php
    $item = $item ?? [];
    $rowIndex = $index ?? '__index__';
    $itemStockId = $item['stock_id'] ?? '';
    $itemQuantity = $item['quantity'] ?? 1;
    $itemPrice = $item['price'] ?? '';
    $itemName = $item['name'] ?? '';
@endphp

<div class="grid grid-cols-12 gap-3 items-end rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-xs">
    <input type="hidden" name="items[{{ $rowIndex }}][name]" class="item-name-input" value="{{ $itemName }}">

    <div class="col-span-12 md:col-span-4">
        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Item Sparepart</label>
        <select name="items[{{ $rowIndex }}][stock_id]" class="item-stock-select w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <option value="">Pilih item dari stok</option>
            @foreach($stocks as $stock)
                <option value="{{ $stock->id }}" data-price="{{ $stock->price }}" data-name="{{ $stock->name }}" @if((string) $stock->id === (string) $itemStockId) selected @endif>{{ $stock->code }} — {{ $stock->name }} ({{ $stock->quantity }} tersedia)</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-12 md:col-span-3">
        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Nama Item</label>
        <input type="text" readonly class="item-name-display w-full rounded-2xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-xs sm:text-sm text-slate-700" value="{{ $itemName }}" placeholder="Nama item otomatis">
    </div>
    <div class="col-span-6 md:col-span-1">
        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Jumlah</label>
        <input name="items[{{ $rowIndex }}][quantity]" type="number" min="1" class="item-quantity-input w-full rounded-2xl border border-slate-200 px-3 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" value="{{ $itemQuantity }}">
    </div>
    <div class="col-span-6 md:col-span-2">
        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Harga Total</label>
        <input name="items[{{ $rowIndex }}][price]" type="number" min="0" step="1000" class="item-price-input w-full rounded-2xl border border-slate-200 px-3 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" value="{{ $itemPrice }}" placeholder="0" readonly>
    </div>
    <div class="col-span-12 md:col-span-2">
        <button type="button" onclick="removeItemRow(this)" class="w-full rounded-2xl bg-rose-50 px-3 py-2.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition-colors">Hapus Item</button>
    </div>
</div>
