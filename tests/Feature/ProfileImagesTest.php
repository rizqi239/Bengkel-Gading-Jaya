<?php

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
        $table->string('avatar_path')->nullable();
        $table->rememberToken();
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
    Schema::dropIfExists('users');
});

function createImageTestUser(): User
{
    return User::create([
        'name' => 'Admin Test',
        'email' => 'admin-images@test.com',
        'password' => bcrypt('password'),
    ]);
}

test('profile avatar upload persists to the user account and appears on profile', function () {
    $this->withoutVite();
    Storage::fake('public');
    $user = createImageTestUser();

    $this->actingAs($user)
        ->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.png'),
        ])
        ->assertRedirect(route('profile'));

    $user->refresh();
    expect($user->avatar_path)->toStartWith('avatars/');
    Storage::disk('public')->assertExists($user->avatar_path);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee(Storage::disk('public')->url($user->avatar_path), false);
});

test('logo upload persists as a shared site setting and appears in the layout', function () {
    $this->withoutVite();
    Storage::fake('public');
    $user = createImageTestUser();

    $this->actingAs($user)
        ->post(route('settings.logo.update'), [
            'logo' => UploadedFile::fake()->image('logo.png'),
        ])
        ->assertRedirect();

    $logoPath = DB::table('site_settings')->where('name', 'logo_path')->value('value');
    expect($logoPath)->toStartWith('branding/');
    Storage::disk('public')->assertExists($logoPath);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee(Storage::disk('public')->url($logoPath), false);
});
