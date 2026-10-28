<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\BrandingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root URL (/) - Redirect ke halaman login terlebih dahulu jika belum login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Auth Routes (Guest Access)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman Utama & Fitur Aplikasi (Wajib Login / Auth Protected)
Route::middleware('auth')->group(function () {
    // Halaman Profil & Notifikasi Pengguna
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::post('/settings/logo', [BrandingController::class, 'updateLogo'])->name('settings.logo.update');
    Route::get('/notifikasi', function () {
        return view('notifikasi');
    })->name('notifikasi');

    // Route Utama Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::get('/dashboard/export-image', [DashboardController::class, 'exportImage'])->name('dashboard.export-image');

    // Absensi resource routes (index, create, store, edit, update, destroy)
    Route::resource('absensi', AbsensiController::class)->except(['show']);

    // Stock resource routes (index, create, store, edit, update, destroy)
    Route::get('/stock/{stock}/photo', [StockController::class, 'photo'])->name('stock.photo');
    Route::resource('stock', StockController::class)->except(['show']);
    Route::post('/stock/{stock}/increase', [StockController::class, 'increase'])->name('stock.increase');

    // Kasir & Print Invoice
    Route::get('/kasir/export', [KasirController::class, 'export'])->name('kasir.export');
    Route::resource('kasir', KasirController::class);
    Route::patch('/kasir/{kasir}/status', [KasirController::class, 'updateStatus'])->name('kasir.update-status');
    Route::get('/kasir/{kasir}/print', [KasirController::class, 'print'])->name('kasir.print');

    // Riwayat & Export
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/export', [RiwayatController::class, 'export'])->name('riwayat.export');

    // Pembelian Supplier
    Route::get('/pembelian/export', [PembelianController::class, 'export'])->name('pembelian.export');
    Route::resource('pembelian', PembelianController::class);
    Route::patch('/pembelian/{pembelian}/complete', [PembelianController::class, 'complete'])->name('pembelian.complete');

    // Laporan Resource Routes
    Route::get('/laporan/summary', [LaporanController::class, 'summary'])->name('laporan.summary');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
});