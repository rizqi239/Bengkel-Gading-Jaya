<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;

uses(RefreshDatabase::class);

function mockGoogleLogin(string $email, string $name = 'Google User'): void
{
    $googleUser = Mockery::mock(GoogleUser::class);
    $googleUser->shouldReceive('getEmail')->andReturn($email);
    $googleUser->shouldReceive('getName')->andReturn($name);

    $provider = Mockery::mock();
    $provider->shouldReceive('redirectUrl')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($provider);
}

it('continues new Google users to role selection', function () {
    mockGoogleLogin(' New-User@Gmail.com ', 'New Google User');

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('register'));
    $response->assertSessionHas('google_registration', [
        'email' => 'new-user@gmail.com',
        'name' => 'New Google User',
    ]);
    expect(User::count())->toBe(0);
    expect(Auth::check())->toBeFalse();
});

it('shows verified Google identity and role selection on the registration form', function () {
    mockGoogleLogin('new-user@gmail.com', 'New Google User');

    $this->get(route('google.callback'))
        ->assertRedirect(route('register'));

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Akun Google terverifikasi')
        ->assertSee('New Google User')
        ->assertSee('new-user@gmail.com')
        ->assertSee('Pilih role')
        ->assertDontSee('name="password" required', false);
});

it('creates and signs in a Google account after the user selects a role', function () {
    mockGoogleLogin('new-user@gmail.com', 'New Google User');

    $this->get(route('google.callback'))
        ->assertRedirect(route('register'));

    $this->post(route('register'), [
        'name' => 'Forged Name',
        'email' => 'forged@example.com',
        'role' => 'Kasir',
        'password' => 'ignored-password',
        'password_confirmation' => 'ignored-password',
    ])->assertRedirect(route('dashboard'));

    $user = User::first();
    expect(User::count())->toBe(1)
        ->and($user->name)->toBe('New Google User')
        ->and($user->email)->toBe('new-user@gmail.com')
        ->and($user->role)->toBe('Kasir')
        ->and($user->email_verified_at)->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});

it('does not allow a Google registration to assign an unsupported role', function () {
    mockGoogleLogin('new-user@gmail.com');
    $this->get(route('google.callback'));

    $this->from(route('register'))
        ->post(route('register'), ['role' => 'Admin'])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors('role');

    expect(User::count())->toBe(0);
    $this->assertGuest();
});

it('does not keep a previous login when starting Google registration', function () {
    $previousUser = User::create([
        'name' => 'Pengguna Terdaftar',
        'email' => 'registered@gmail.com',
        'password' => 'password',
    ]);
    $this->actingAs($previousUser);
    mockGoogleLogin('new-user@gmail.com');

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('register'));
    $response->assertSessionHas('google_registration.email', 'new-user@gmail.com');
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

    mockGoogleLogin('REGISTERED@gmail.com');

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});
