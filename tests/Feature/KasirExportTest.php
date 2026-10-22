<?php

use App\Http\Controllers\KasirController;
use App\Models\KasirTransaction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('kasir_transactions', function (Blueprint $table) {
        $table->id();
        $table->string('customer_name')->nullable();
        $table->string('transaction_type');
        $table->string('plate_number')->nullable();
        $table->string('vehicle_model')->nullable();
        $table->json('items')->nullable();
        $table->decimal('subtotal', 12, 2)->default(0);
        $table->decimal('discount', 12, 2)->default(0);
        $table->decimal('tax', 12, 2)->default(0);
        $table->decimal('total', 12, 2)->default(0);
        $table->string('payment_method');
        $table->string('status');
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('kasir_transactions');
    Schema::dropIfExists('users');
});

it('downloads kasir transactions as an xlsx workbook', function () {
    KasirTransaction::create([
        'customer_name' => 'Budi',
        'transaction_type' => 'Servis Mesin',
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

    $response = app(KasirController::class)->export(Request::create('/kasir/export', 'GET'));
    expect($response->headers->get('content-type'))
        ->toBe('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    expect($response->headers->get('content-disposition'))->toContain('.xlsx');

    $path = tempnam(sys_get_temp_dir(), 'kasir-export-');
    ob_start();
    $response->sendContent();
    file_put_contents($path, ob_get_clean());

    $spreadsheet = IOFactory::load($path);
    expect($spreadsheet->getActiveSheet()->getCell('A1')->getValue())->toBe('No. Transaksi');
    expect($spreadsheet->getActiveSheet()->getCell('A2')->getValue())->toBe(1);
    expect($spreadsheet->getActiveSheet()->getCell('C2')->getValue())->toBe('Budi');

    $spreadsheet->disconnectWorksheets();
    unlink($path);
});