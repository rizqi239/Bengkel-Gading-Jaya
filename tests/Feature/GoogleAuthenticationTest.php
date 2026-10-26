<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;

uses(RefreshDatabase::class);

function mockGoogleLogin(string $email): void
{
    $googleUser = Mockery::mock(GoogleUser::class);
    $googleUser->shouldReceive('getEmail')->andReturn($email);

    $provider = Mockery::mock();
    $provider->shouldReceive('redirectUrl')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($provider);
}

it('rejects Google accounts that are not registered', function () {
    mockGoogleLogin('new-user@gmail.com');

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors([
        'email' => 'Akun Google ini belum terdaftar. Silakan daftar terlebih dahulu.',
    ]);
    $response->assertSessionHas('google_account_unregistered', true);
    expect(User::count())->toBe(0);
    expect(Auth::check())->toBeFalse();
});

it('shows a registration popup with a link to register after an unregistered Google login', function () {
    mockGoogleLogin('new-user@gmail.com');

    $this->get(route('google.callback'))
        ->assertRedirect(route('login'));

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Akun belum terdaftar')
        ->assertSee(route('register'));
});

it('does not keep a previous login when an unregistered Google account is used', function () {
    $previousUser = User::create([
        'name' => 'Pengguna Terdaftar',
        'email' => 'registered@gmail.com',
        'password' => 'password',
    ]);
    $this->actingAs($previousUser);
    mockGoogleLogin('new-user@gmail.com');

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('does not create or authenticate users for unknown email and password logins', function () {
    $response = $this->from(route('login'))->post(route('login'), [
        'email' => 'new-user@gmail.com',
        'password' => 'any-password',
    ]);

    $response->assertRedirect(route('login'));
    expect(User::count())->toBe(0);
    $this->assertGuest();
});

it('allows registered Google accounts to sign in', function () {
    $user = User::create([
        'name' => 'Pengguna Terdaftar',
        'email' => 'registered@gmail.com',
        'password' => 'password',
    ]);

    mockGoogleLogin('registered@gmail.com');

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});
