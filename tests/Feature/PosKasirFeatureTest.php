<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;

/**
 * Class PosKasirFeatureTest
 * 
 * Pengujian integrasi modul-modul sistem POS RestoKita:
 * - Autentikasi Login Pengguna (Kasir & Admin)
 * - Proteksi Halaman Dashboard, Kasir, Manajemen Produk, Riwayat Transaksi
 * - CRUD Produk Menu Makanan & Minuman
 * - Alur Checkout Transaksi, Pengurangan Stok, dan Relasi Detail Transaksi
 * 
 * Memenuhi Unit Kompetensi BNSP:
 * - J.620100.009.01: Menggunakan spesifikasi program.
 * - J.620100.016.01: Menulis kode sesuai guidelines dan best practices.
 * - J.620100.033.02: Melaksanakan pengujian unit program.
 * 
 * @package Tests\Feature
 */
class PosKasirFeatureTest extends TestCase
{
    /**
     * Memastikan tamu (guest) yang belum login diarahkan ke halaman login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $responseKasir = $this->get('/kasir');
        $responseKasir->assertRedirect('/login');
    }

    /**
     * Menguji pengguna dapat login dengan kredensial yang valid.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::first();

        $response = $this->post('/login', [
            'email'    => $user ? $user->email : 'kasir@restokita.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /**
     * Memastikan seluruh halaman utama dapat diakses dengan sukses (HTTP 200) setelah login.
     */
    public function test_all_main_pages_are_accessible_when_authenticated(): void
    {
        $user = User::first() ?? User::factory()->create();

        $responseDashboard = $this->actingAs($user)->get('/dashboard');
        $responseDashboard->assertStatus(200);

        $responseKasir = $this->actingAs($user)->get('/kasir');
        $responseKasir->assertStatus(200);

        $responseProduk = $this->actingAs($user)->get('/produk');
        $responseProduk->assertStatus(200);

        $responseTransaksi = $this->actingAs($user)->get('/transaksi');
        $responseTransaksi->assertStatus(200);
    }

    /**
     * Menguji penambahan menu baru (Create Produk).
     */
    public function test_can_create_new_product(): void
    {
        $user = User::first() ?? User::factory()->create();
        $uniqueName = 'Menu Uji ' . rand(100, 999);

        $response = $this->actingAs($user)->post('/produk', [
            'nama_produk' => $uniqueName,
            'kategori'    => 'makanan',
            'harga'       => 35000,
            'stok'        => 50,
        ]);

        $response->assertRedirect('/produk');
        $this->assertDatabaseHas('produks', [
            'nama_produk' => $uniqueName,
            'harga'       => 35000,
            'stok'        => 50,
        ]);
    }

    /**
     * Menguji alur belanja di kasir: simpan transaksi, relasi detail transaksi, dan pengurangan stok.
     */
    public function test_checkout_saves_transaction_and_reduces_stock(): void
    {
        $user = User::first() ?? User::factory()->create();

        // 1. Buat produk pengujian
        $produk = Produk::create([
            'nama_produk' => 'Ayam Uji Spesial ' . rand(10, 99),
            'kategori'    => 'makanan',
            'harga'       => 20000,
            'stok'        => 10,
        ]);

        // 2. Set sesi keranjang belanja
        $cart = [
            $produk->id => [
                'id'          => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'harga'       => $produk->harga,
                'qty'         => 3,
                'stok_max'    => $produk->stok,
            ]
        ];

        // 3. Eksekusi checkout dengan simulasi session
        $response = $this->actingAs($user)->withSession([
            'cart' => $cart,
            'voucher' => [
                'code'   => 'KELUARGA',
                'nama'   => 'Diskon Keluarga',
                'persen' => 15,
                'badge'  => 'primary'
            ]
        ])->post('/kasir/checkout', [
            'uang_bayar' => 60000
        ]);

        $response->assertRedirect('/kasir');
        $response->assertSessionHas('success');
        $response->assertSessionHas('struk');

        // 4. Verifikasi stok produk berkurang (10 - 3 = 7)
        $this->assertEquals(7, $produk->fresh()->stok);

        // 5. Verifikasi transaksi tersimpan di database
        $this->assertDatabaseHas('transaksis', [
            'kode_voucher' => 'KELUARGA',
        ]);

        // 6. Verifikasi detail transaksi tersimpan
        $this->assertDatabaseHas('detail_transaksis', [
            'produk_id'   => $produk->id,
            'nama_produk' => $produk->nama_produk,
            'qty'         => 3,
        ]);
    }
}
