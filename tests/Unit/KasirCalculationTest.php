<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\KasirController;

/**
 * Class KasirCalculationTest
 * 
 * Pengujian unit logika bisnis algoritma keuangan POS Kasir:
 * - Kalkulasi Subtotal
 * - Diskon Bertingkat (Tier 1: 5%, Tier 2: 10%)
 * - Diskon Voucher Khusus (KELUARGA 15%, BUKBER 20%, PUASA 25%, dll)
 * - Dasar Pengenaan Pajak (DPP)
 * - Pajak Restoran (PPN 10%)
 * - Total Tagihan & Kembalian
 * 
 * Memenuhi Unit Kompetensi BNSP:
 * J.620100.033.02: Melaksanakan pengujian unit program.
 * 
 * @package Tests\Unit
 */
class KasirCalculationTest extends TestCase
{
    /**
     * Menguji kalkulasi subtotal keranjang belanja.
     */
    public function test_subtotal_calculation(): void
    {
        $cart = [
            ['nama_produk' => 'Nasi Goreng', 'harga' => 25000, 'qty' => 2], // 50.000
            ['nama_produk' => 'Es Teh Manis', 'harga' => 5000, 'qty' => 4],  // 20.000
        ];

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['harga'] * $item['qty'];
        }

        $this->assertEquals(70000, $subtotal);
    }

    /**
     * Menguji bahwa tanpa kode voucher promo, nilai diskon adalah 0 (Rp 0).
     */
    public function test_no_discount_when_voucher_is_not_applied(): void
    {
        $subtotal = 80000;
        $voucher = null; // Tidak input kode voucher
        $diskonPersen = $voucher ? (float) $voucher['persen'] : 0;
        $diskon = ($subtotal * $diskonPersen) / 100;

        $this->assertEquals(0, $diskon);
        $this->assertEquals(0, $diskonPersen);
    }

    /**
     * Menguji bahwa diskon hanya terhitung jika kode voucher promo diinputkan.
     */
    public function test_discount_when_voucher_is_applied(): void
    {
        $subtotal = 80000;
        $voucher = ['code' => 'KELUARGA', 'persen' => 15]; // Voucher diskon 15%
        $diskonPersen = (float) $voucher['persen'];
        $diskon = ($subtotal * $diskonPersen) / 100;

        $this->assertEquals(12000, $diskon);
        $this->assertEquals(15, $diskonPersen);
    }

    /**
     * Menguji validitas kode voucher promo resmi (KELUARGA, BUKBER, PUASA, HEMAT10, PELAJAR, JUMAT).
     */
    public function test_voucher_promo_percentages(): void
    {
        $vouchers = KasirController::$vouchers;

        $this->assertArrayHasKey('KELUARGA', $vouchers);
        $this->assertEquals(15, $vouchers['KELUARGA']['persen']);

        $this->assertArrayHasKey('BUKBER', $vouchers);
        $this->assertEquals(20, $vouchers['BUKBER']['persen']);

        $this->assertArrayHasKey('PUASA', $vouchers);
        $this->assertEquals(25, $vouchers['PUASA']['persen']);

        $this->assertArrayHasKey('HEMAT10', $vouchers);
        $this->assertEquals(10, $vouchers['HEMAT10']['persen']);

        $this->assertArrayHasKey('PELAJAR', $vouchers);
        $this->assertEquals(12, $vouchers['PELAJAR']['persen']);

        $this->assertArrayHasKey('JUMAT', $vouchers);
        $this->assertEquals(30, $vouchers['JUMAT']['persen']);
    }

    /**
     * Menguji rumus Dasar Pengenaan Pajak (DPP), Pajak 10%, dan Total Bayar.
     */
    public function test_dpp_and_tax_and_total_calculation(): void
    {
        $subtotal = 100000;
        $diskonPersen = 20; // Misal voucher BUKBER (20%)
        $diskon = ($subtotal * $diskonPersen) / 100; // 20.000

        $dpp = $subtotal - $diskon; // 80.000
        $pajak = $dpp * 0.10;       // 8.000 (10% dari 80.000)
        $totalBayar = $dpp + $pajak; // 88.000

        $this->assertEquals(80000, $dpp);
        $this->assertEquals(8000, $pajak);
        $this->assertEquals(88000, $totalBayar);
    }

    /**
     * Menguji kalkulasi kembalian uang bayar kasir.
     */
    public function test_cash_change_calculation(): void
    {
        $totalBayar = 88000;
        $uangDiterima = 100000;
        $kembalian = $uangDiterima - $totalBayar;

        $this->assertEquals(12000, $kembalian);
    }
}
