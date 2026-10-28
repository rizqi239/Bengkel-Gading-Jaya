<?php

use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role')->nullable();
        $table->rememberToken();
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

    Schema::create('site_settings', function (Blueprint $table) {
        $table->string('name')->primary();
        $table->text('value')->nullable();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('site_settings');
    Schema::dropIfExists('stocks');
    Schema::dropIfExists('users');
});

test('it creates a product and shows it in the stock list', function () {
    $this->withoutVite();

    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'Pemilik Toko',
    ]);

    $this->actingAs($user)
        ->get(route('stock.create'))
        ->assertOk()
        ->assertSee('Form Tambah Produk');

    $this->actingAs($user)
        ->post(route('stock.store'), [
            'name' => 'Oli Mesin',
            'category' => 'Oli',
            'quantity' => 5,
            'price' => 85000,
        ])
        ->assertRedirect(route('stock.index'));

    $stock = Stock::firstOrFail();
    expect($stock->code)->toBe('SP-001');
    expect($stock->name)->toBe('Oli Mesin');
    expect($stock->category)->toBe('Oli');
    expect($stock->quantity)->toBe(5);

    $this->actingAs($user)
        ->get(route('stock.index'))
        ->assertOk()
        ->assertSee('Oli Mesin');
});

test('it shows validation errors when product name is missing', function () {
    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'Pemilik Toko',
    ]);

    $this->actingAs($user)
        ->from(route('stock.create'))
        ->post(route('stock.store'), [
            'category' => 'Oli',
        ])
        ->assertRedirect(route('stock.create'))
        ->assertSessionHasErrors('name');

    expect(Stock::count())->toBe(0);
});

test('it uploads stock photo to configured storage disk', function () {
    $disk = config('filesystems.default');
    Storage::fake($disk);

    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'Pemilik Toko',
    ]);

    $file = UploadedFile::fake()->image('kampas_rem.jpg');

    $response = $this->actingAs($user)->post(route('stock.store'), [
        'name' => 'Kampas Rem Depan',
        'category' => 'Kampas Rem',
        'quantity' => 10,
        'price' => 75000,
        'photo' => $file,
    ]);

    $response->assertRedirect(route('stock.index'));

    $stock = Stock::first();
    expect($stock)->not->toBeNull();
    expect($stock->name)->toBe('Kampas Rem Depan');
    expect($stock->photo)->not->toBeNull();
    expect($stock->photo)->toStartWith('data:image/');
    expect($stock->photo_url)->toBe(route('stock.photo', $stock));
});

test('it updates stock quantity and photo together', function () {
    $user = User::create([
        'name' => 'Owner Test',
        'email' => 'owner@test.com',
        'password' => bcrypt('password'),
        'role' => 'Pemilik Toko',
    ]);

    $stock = Stock::create([
        'code' => 'SP-001',
        'name' => 'Kampas Rem',
        'category' => 'Kampas Rem',
        'quantity' => 5,
        'price' => 75000,
    ]);

    $response = $this->actingAs($user)->put(route('stock.update', $stock), [
        'code' => $stock->code,
        'name' => $stock->name,
        'category' => $stock->category,
        'quantity' => 12,
        'price' => $stock->price,
        'photo' => UploadedFile::fake()->image('kampas_rem_baru.jpg'),
    ]);

    $response->assertRedirect(route('stock.index'));

    $stock->refresh();
    expect($stock->quantity)->toBe(12);
    expect($stock->photo)->toStartWith('data:image/');
    expect($stock->photo_url)->toBe(route('stock.photo', $stock));
});

test('it increases stock quantity', function () {
    $user = User::create([
        'name' => 'Owner Test',
        'email' => 'owner@test.com',
        'password' => bcrypt('password'),
        'role' => 'Pemilik Toko',
    ]);

    $stock = Stock::create([
        'code' => 'SP-001',
        'name' => 'Oli Mesin',
        'category' => 'Oli',
        'quantity' => 5,
        'price' => 85000,
    ]);

    $this->actingAs($user)
        ->post(route('stock.increase', $stock), ['quantity' => 3])
        ->assertRedirect(route('stock.index'));

    expect($stock->fresh()->quantity)->toBe(8);
});
