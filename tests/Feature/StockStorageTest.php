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
        $table->string('photo')->nullable();
        $table->timestamps();
    });
});

afterEach(function () {
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
    expect($stock->photo_url)->toBe($stock->photo);
});
