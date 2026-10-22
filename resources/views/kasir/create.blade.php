@extends('layouts.app')

@section('title', 'Buat Transaksi')
@section('page_title', 'Buat Transaksi')

@section('content')
<div class="space-y-8">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col gap-2 mb-6">
            <h3 class="text-xl font-bold text-slate-900">Form Transaksi Kasir</h3>
            <p class="text-sm text-slate-500">Isi detail transaksi servis, penjualan sparepart, atau servis + sparepart.</p>
        </div>

        <form action="{{ route('kasir.store') }}" method="POST" class="space-y-6">
            @csrf

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
                    <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" type="text" placeholder="Nama pelanggan" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="transaction_type">Jenis Servis</label>
                    <select id="transaction_type" name="transaction_type" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="Servis + Sparepart" {{ old('transaction_type') === 'Servis + Sparepart' ? 'selected' : '' }}>Servis + Sparepart</option>
                        <option value="Servis Mesin" {{ old('transaction_type') === 'Servis Mesin' ? 'selected' : '' }}>Servis Mesin</option>
                        <option value="Tune Up" {{ old('transaction_type') === 'Tune Up' ? 'selected' : '' }}>Tune Up</option>
                        <option value="Service CVT" {{ old('transaction_type') === 'Service CVT' ? 'selected' : '' }}>Service CVT</option>
                        <option value="BoreUp" {{ old('transaction_type') === 'BoreUp' ? 'selected' : '' }}>BoreUp</option>
                        <option value="Penjualan Barang" {{ old('transaction_type') === 'Penjualan Barang' ? 'selected' : '' }}>Penjualan Barang</option>
                    </select>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Harga Jenis Servis</p>
                        <p id="service-price-value" class="mt-2 text-3xl font-bold text-slate-900">Rp 0</p>
                    </div>
                    <p id="service-price-description" class="text-sm text-slate-500">Pilih jenis servis untuk melihat harga.</p>
                </div>
            </div>
            <input type="hidden" id="service_price" name="service_price" value="{{ old('service_price', 0) }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="plate_number">Nomor Plat</label>
                    <input id="plate_number" name="plate_number" value="{{ old('plate_number') }}" type="text" placeholder="B 1234 ABC" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="vehicle_model">Model Kendaraan</label>
                    <input id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model') }}" type="text" placeholder="Contoh: Avanza / Vario" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3" placeholder="Contoh: Tambahkan deskripsi tambahan..." class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">{{ old('description') }}</textarea>
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

                    @if(old('items'))
                        @foreach(old('items') as $index => $item)
                            @include('kasir.partials.item-row', ['index' => $index, 'item' => $item])
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="max-w-xs">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="total">Total Harga (Rp)</label>
                    <input id="total" name="total" value="{{ old('total', 0) }}" type="number" min="0" step="0.01" readonly class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-lg font-bold text-slate-900 focus:outline-none">
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="payment_method">Metode Pembayaran</label>
                        <select id="payment_method" name="payment_method" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="Tunai" {{ old('payment_method') === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="Dana" {{ old('payment_method') === 'Dana' ? 'selected' : '' }}>Dana</option>
                            <option value="GoPay" {{ old('payment_method') === 'GoPay' ? 'selected' : '' }}>GoPay</option>
                            <option value="Bank BCA" {{ old('payment_method') === 'Bank BCA' ? 'selected' : '' }}>Bank BCA</option>
                            <option value="Bank Mandiri" {{ old('payment_method') === 'Bank Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="status">Status Transaksi</label>
                        <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="Draft" {{ old('status') === 'Draft' ? 'selected' : '' }}>Antri</option>
                            <option value="Selesai" {{ old('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ old('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('kasir.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-2xl transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-2xl transition-colors">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let itemIndex = {{ count(old('items', [])) }};

    function addItemRow(item = {}) {
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
        document.getElementById('service_price').value = servicePrice.toFixed(2);

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

    function getServicePrice(type) {
        const prices = {
            'Servis + Sparepart': 10000,
            'Servis Mesin': 1000000,
            'Tune Up': 500000,
            'Service CVT': 65000,
            'BoreUp': 2000000,
            'Penjualan Barang': 0
        };

        return prices[type] ?? 0;
    }

    function updateServicePriceCard() {
        const typeSelect = document.getElementById('transaction_type');
        const priceValue = document.getElementById('service-price-value');
        const priceDescription = document.getElementById('service-price-description');
        const selectedType = typeSelect.value;
        const price = getServicePrice(selectedType);

        priceValue.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price);

        if (price > 0) {
            priceDescription.textContent = `Harga untuk ${selectedType} ditetapkan.`;
        } else {
            priceDescription.textContent = 'Pilih jenis servis untuk melihat harga.';
        }
    }

    document.addEventListener('change', function (event) {
        if (event.target.matches('.item-stock-select') || event.target.matches('.item-quantity-input')) {
            updateItemRow(event);
        }

        if (event.target.matches('#discount') || event.target.matches('#tax')) {
            calculateAndUpdateTotal();
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
