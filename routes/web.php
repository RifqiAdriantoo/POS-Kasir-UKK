<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;

/*
|--------------------------------------------------------------------------
| Web Routes - RestoKita POS Kasir Restoran (UKK 2026)
|--------------------------------------------------------------------------
|
| Rute aplikasi disusun modular mengikuti alur:
| 1. Autentikasi Pengguna (Login/Logout)
| 2. Dashboard Statistik (Wajib Login)
| 3. Manajemen Menu Produk CRUD (Wajib Login)
| 4. Transaksi POS Kasir (Wajib Login)
| 5. Riwayat & Pelaporan Transaksi (Wajib Login)
|
*/

// Redirect root ke Login (jika belum login) atau ke Dashboard (jika sudah login)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Autentikasi Pengguna (Tamu / Guest)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang Dilindungi (Wajib Login sebelum mengakses Dashboard dan Operasional POS)
Route::middleware('auth')->group(function () {
    // 1. Dashboard Statistik Restoran
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Modul Transaksi POS Kasir
    Route::prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/add/{id}', [KasirController::class, 'addToCart'])->name('cart.add');
        Route::get('/plus/{id}', [KasirController::class, 'plusQty'])->name('cart.plus');
        Route::get('/minus/{id}', [KasirController::class, 'minusQty'])->name('cart.minus');
        Route::get('/remove/{id}', [KasirController::class, 'removeFromCart'])->name('cart.remove');
        Route::get('/clear', [KasirController::class, 'clearCart'])->name('cart.clear');
        Route::post('/voucher/apply', [KasirController::class, 'applyVoucher'])->name('voucher.apply');
        Route::get('/voucher/remove', [KasirController::class, 'removeVoucher'])->name('voucher.remove');
        Route::match(['get', 'post'], '/checkout', [KasirController::class, 'checkout'])->name('cart.checkout');
    });

    // Fallback rute keranjang legacy
    Route::get('/add-to-cart/{id}', [KasirController::class, 'addToCart']);
    Route::match(['get', 'post'], '/checkout', [KasirController::class, 'checkout']);

    // 3. Modul Manajemen Produk (CRUD)
    Route::prefix('produk')->name('produk.')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('index');
        Route::post('/', [ProdukController::class, 'store'])->name('store');
        Route::put('/{id}', [ProdukController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('destroy');
    });

    // 4. Modul Riwayat Transaksi & Cetak Struk
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('index');
        Route::get('/{id}', [TransaksiController::class, 'show'])->name('show');
        Route::get('/{id}/cetak', [TransaksiController::class, 'cetakStruk'])->name('cetak');
    });
});
