<?php

use App\Models\KasirTransaction;

it('shows financial summary from completed transactions', function () {
    KasirTransaction::create([
        'customer_name' => 'Budi',
        'transaction_type' => 'Servis',
        'plate_number' => 'B 1234 ABC',
        'vehicle_model' => 'Honda Beat',
        'items' => [['name' => 'Oli', 'quantity' => 1, 'price' => 150000]],
        'subtotal' => 150000,
        'discount' => 0,
        'tax' => 0,
        'total' => 150000,
        'payment_method' => 'Tunai',
        'status' => 'Selesai',
    ]);

    KasirTransaction::create([
        'customer_name' => 'Andi',
        'transaction_type' => 'Servis',
        'plate_number' => 'B 9999 XYZ',
        'vehicle_model' => 'Yamaha Mio',
        'items' => [['name' => 'Filter', 'quantity' => 1, 'price' => 50000]],
        'subtotal' => 50000,
        'discount' => 0,
        'tax' => 0,
        'total' => 50000,
        'payment_method' => 'Tunai',
        'status' => 'Draft',
    ]);

    $response = $this->get(route('laporan'));

    $response->assertOk();
    $response->assertSee('Rp 150.000');
    $response->assertSee('1');
});
