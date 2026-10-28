<?php

use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role')->nullable();
        $table->string('whatsapp')->nullable();
        $table->string('avatar_path')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('site_settings', function (Blueprint $table) {
        $table->string('name')->primary();
        $table->text('value')->nullable();
        $table->timestamps();
    });

    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->string('name');
        $table->string('category')->nullable();
        $table->integer('quantity')->default(0);
        $table->decimal('price', 12, 2)->default(0);
        $table->text('photo')->nullable();
        $table->timestamps();
    });

    Schema::create('kasir_transactions', function (Blueprint $table) {
        $table->id();
        $table->string('customer_name')->nullable();
        $table->json('items')->nullable();
        $table->decimal('total', 12, 2)->default(0);
        $table->string('status')->nullable();
        $table->timestamps();
    });

    Schema::create('absensis', function (Blueprint $table) {
        $table->id();
    });

    Schema::create('pembelians', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('pembelians');
    Schema::dropIfExists('absensis');
    Schema::dropIfExists('kasir_transactions');
    Schema::dropIfExists('stocks');
    Schema::dropIfExists('site_settings');
    Schema::dropIfExists('users');
});

test('dashboard renders the authenticated product photo endpoint', function () {
    $this->withoutVite();

    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'admin-product-photo@test.com',
        'password' => bcrypt('password'),
    ]);

    $photo = 'data:image/jpeg;base64,ZmFrZS1pbWFnZQ==';
    Stock::create([
        'code' => 'SP-001',
        'name' => 'Oli Mesin',
        'category' => 'Oli',
        'quantity' => 5,
        'price' => 85000,
        'photo' => $photo,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('stock.photo', Stock::firstOrFail()), false)
        ->assertDontSee($photo, false);
});

test('dashboard renders top best seller photo showcase and modal', function () {
    $this->withoutVite();

    $user = User::create([
        'name' => 'Admin Test 2',
        'email' => 'admin-top-photo@test.com',
        'password' => bcrypt('password'),
    ]);

    $photo = 'data:image/jpeg;base64,ZmFrZS1pbWFnZQ==';
    $stock = Stock::create([
        'code' => 'SP-010',
        'name' => 'Oli Mesin Matic GMJ',
        'category' => 'Oli',
        'quantity' => 15,
        'price' => 95000,
        'photo' => $photo,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('FOTO PRODUK')
        ->assertSee('#1 Terlaris Minggu Ini')
        ->assertSee('dashboardPhotoModal')
        ->assertSee(route('stock.photo', $stock), false);
});

test('product photo endpoint serves the saved image bytes', function () {
    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'admin-product-photo-endpoint@test.com',
        'password' => bcrypt('password'),
    ]);

    $bytes = 'valid-image-bytes';
    $stock = Stock::create([
        'code' => 'SP-002',
        'name' => 'Filter Oli',
        'category' => 'Filter',
        'quantity' => 3,
        'price' => 25000,
        'photo' => 'data:image/jpeg;base64,' . base64_encode($bytes),
    ]);

    $this->actingAs($user)
        ->get(route('stock.photo', $stock))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertContent($bytes);
});

test('product photo endpoint requires authentication', function () {
    $stock = Stock::create([
        'code' => 'SP-003',
        'name' => 'Busi',
        'category' => 'Busi',
        'quantity' => 2,
        'price' => 15000,
        'photo' => 'data:image/jpeg;base64,' . base64_encode('valid-image-bytes'),
    ]);

    $this->get(route('stock.photo', $stock))
        ->assertRedirect(route('login'));
});
