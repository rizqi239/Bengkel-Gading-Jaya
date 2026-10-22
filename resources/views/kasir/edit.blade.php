@extends('layouts.app')

@section('title', 'Edit Transaksi')
@section('page_title', 'Edit Transaksi')

@section('content')
<div class="space-y-8">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col gap-2 mb-6">
            <h3 class="text-xl font-bold text-slate-900">Edit Transaksi Kasir</h3>
            <p class="text-sm text-slate-500">Perbarui detail transaksi sebelum menyimpan.</p>
        </div>

        <form action="{{ route('kasir.update', $kasir) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="customer_name">Pelanggan</label>
                    <input id="customer_name" name="customer_name" value="{{ old('customer_name', $kasir->customer_name) }}" type="text" placeholder="Nama pelanggan" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="transaction_type">Jenis Transaksi</label>
                    <select id="transaction_type" name="transaction_type" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="Servis + Sparepart" {{ old('transaction_type', $kasir->transaction_type) === 'Servis + Sparepart' ? 'selected' : '' }}>Servis + Sparepart</option>
                        <option value="Servis Mesin" {{ old('transaction_type', $kasir->transaction_type) === 'Servis Mesin' ? 'selected' : '' }}>Servis Mesin</option>
                        <option value="Tune Up" {{ old('transaction_type', $kasir->transaction_type) === 'Tune Up' ? 'selected' : '' }}>Tune Up</option>
                        <option value="Service CVT" {{ old('transaction_type', $kasir->transaction_type) === 'Service CVT' ? 'selected' : '' }}>Service CVT</option>
                        <option value="BoreUp" {{ old('transaction_type', $kasir->transaction_type) === 'BoreUp' ? 'selected' : '' }}>BoreUp</option>
                        <option value="Penjualan Barang" {{ old('transaction_type', $kasir->transaction_type) === 'Penjualan Barang' ? 'selected' : '' }}>Penjualan Barang</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="plate_number">Nomor Plat</label>
                    <input id="plate_number" name="plate_number" value="{{ old('plate_number', $kasir->plate_number) }}" type="text" placeholder="B 1234 ABC" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="vehicle_model">Model Kendaraan</label>
                    <input id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model', $kasir->vehicle_model) }}" type="text" placeholder="Contoh: Avanza / Vario" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3" placeholder="Contoh: Tambahkan deskripsi tambahan..." class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">{{ old('description', $kasir->description) }}</textarea>
                <p class="mt-2 text-xs text-slate-500">Deskripsi tambahan akan disimpan bersama transaksi.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h5 class="text-sm font-semibold text-slate-800">Item Transaksi</h5>
                        <p class="text-xs text-slate-500">Pilih item sparepart dari stok, kemudian masukkan jumlah.</p>
                    </div>
                    <button type="button" onclick="addItemRow()" class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Tambah Item</button>
                </div>

                <div id="items-wrapper" class="space-y-4">
                    <template id="item-row-template">
                        @include('kasir.partials.item-row')
                    </template>

                    @php $items = old('items', $kasir->items ?? []); @endphp
                    @foreach($items as $index => $item)
                        @include('kasir.partials.item-row', ['index' => $index, 'item' => $item])
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="max-w-xs">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="total">Total Harga (Rp)</label>
                    <input id="total" name="total" value="{{ old('total', $kasir->total) }}" type="number" min="0" step="0.01" readonly class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-lg font-bold text-slate-900 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="payment_method">Metode Pembayaran</label>
                    <select id="payment_method" name="payment_method" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="Tunai" {{ old('payment_method', $kasir->payment_method) === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="Dana" {{ old('payment_method', $kasir->payment_method) === 'Dana' ? 'selected' : '' }}>Dana</option>
                        <option value="GoPay" {{ old('payment_method', $kasir->payment_method) === 'GoPay' ? 'selected' : '' }}>GoPay</option>
                        <option value="Bank BCA" {{ old('payment_method', $kasir->payment_method) === 'Bank BCA' ? 'selected' : '' }}>Bank BCA</option>
                        <option value="Bank Mandiri" {{ old('payment_method', $kasir->payment_method) === 'Bank Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="status">Status Transaksi</label>
                    <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="Draft" {{ old('status', $kasir->status) === 'Draft' ? 'selected' : '' }}>Antri</option>
                        <option value="Selesai" {{ old('status', $kasir->status) === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Dibatalkan" {{ old('status', $kasir->status) === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>



            <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('kasir.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-2xl transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-2xl transition-colors">Perbarui Transaksi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let itemIndex = {{ count(old('items', $kasir->items ?? [])) }};

    function addItemRow() {
        const template = document.getElementById('item-row-template').innerHTML;
        const rowHtml = template.replace(/__index__/g, itemIndex);
        document.getElementById('items-wrapper').insertAdjacentHTML('beforeend', rowHtml);
        itemIndex++;
    }

    function removeItemRow(button) {
        button.closest('.grid').remove();
        calculateAndUpdateTotal();
    }

    function calculateItemTotal(row) {
        const stockSelect = row.querySelector('.item-stock-select');
        const priceInput = row.querySelector('.item-price-input');
        const quantityInput = row.querySelector('.item-quantity-input');
        const nameInput = row.querySelector('.item-name-input');
        const nameDisplay = row.querySelector('.item-name-display');

        if (!stockSelect || !priceInput || !quantityInput || !nameInput) {
            return;
        }

        const selectedOption = stockSelect.selectedOptions[0];
        const unitPrice = Number(selectedOption?.dataset.price || 0);
        const quantity = Number(quantityInput.value || 0);
        const name = selectedOption?.dataset.name || '';

        nameInput.value = name;
        if (nameDisplay) {
            nameDisplay.value = name;
        }
        priceInput.value = unitPrice * quantity;
    }

    function updateItemRow(event) {
        const row = event.target.closest('.grid');
        if (!row) return;
        calculateItemTotal(row);
        calculateAndUpdateTotal();
    }

    function updateAllSelectedItemPrices() {
        document.querySelectorAll('#items-wrapper .grid').forEach(row => {
            calculateItemTotal(row);
        });
    }

    function calculateAndUpdateTotal() {
        let itemTotal = 0;
        document.querySelectorAll('#items-wrapper .item-price-input').forEach(priceInput => {
            itemTotal += Number(priceInput.value || 0);
        });

        const servicePrice = getServicePrice(document.getElementById('transaction_type').value);
        const subtotal = itemTotal + servicePrice;

        const subtotalEl = document.getElementById('subtotal');
        if (subtotalEl) {
            subtotalEl.value = subtotal.toFixed(2);
        }
        const servicePriceEl = document.getElementById('service_price');
        if (servicePriceEl) {
            servicePriceEl.value = servicePrice.toFixed(2);
        }

        const discountEl = document.getElementById('discount');
        const discount = discountEl ? Number(discountEl.value || 0) : 0;
        const taxEl = document.getElementById('tax');
        const tax = taxEl ? Number(taxEl.value || 0) : 0;
        const total = Math.max(0, subtotal - discount + tax);

        const totalEl = document.getElementById('total');
        if (totalEl) {
            totalEl.value = total.toFixed(2);
        }
    }

    document.addEventListener('change', function (event) {
        if (event.target.matches('.item-stock-select') || event.target.matches('.item-quantity-input') || event.target.matches('#discount') || event.target.matches('#tax')) {
            updateItemRow(event);
        }

        if (event.target.matches('#transaction_type')) {
            updateServicePriceCard();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        if (itemIndex === 0) {
            addItemRow();
        }
        updateAllSelectedItemPrices();
        calculateAndUpdateTotal();
        updateServicePriceCard();
    });
</script>
@endsection
