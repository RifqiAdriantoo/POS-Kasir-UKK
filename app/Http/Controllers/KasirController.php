<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Carbon\Carbon;

/**
 * Class KasirController
 * 
 * Mengelola seluruh alur operasional modul Point of Sale (POS) Kasir,
 * meliputi penambahan pesanan ke keranjang, kalkulasi keuangan
 * (subtotal, diskon bertingkat / voucher, dan pajak resto 10%),
 * penyimpanan transaksi relasional, dan pengurangan inventaris stok.
 * 
 * Memenuhi Standar Kompetensi BNSP Pemrogram Junior (Junior Coder) 2026.
 * 
 * @package App\Http\Controllers
 */
class KasirController extends Controller
{
    /**
     * Daftar Kode Voucher & Diskon Promo yang Tersedia di Sistem.
     * 
     * @var array<string, array<string, mixed>>
     */
    public static array $vouchers = [
        'KELUARGA' => [
            'nama' => 'Diskon Keluarga',
            'persen' => 15,
            'deskripsi' => 'Potongan 15% untuk makan bersama keluarga',
            'badge' => 'primary',
            'icon' => 'bi-people-fill'
        ],
        'BUKBER' => [
            'nama' => 'Diskon Buka Bersama',
            'persen' => 20,
            'deskripsi' => 'Potongan 20% spesial momen buka puasa bersama',
            'badge' => 'success',
            'icon' => 'bi-moon-stars-fill'
        ],
        'PUASA' => [
            'nama' => 'Berkah Ramadhan',
            'persen' => 25,
            'deskripsi' => 'Potongan 25% promo berkah puasa',
            'badge' => 'warning',
            'icon' => 'bi-brightness-high-fill'
        ],
        'HEMAT10' => [
            'nama' => 'Promo Hemat Harian',
            'persen' => 10,
            'deskripsi' => 'Diskon hemat 10% setiap hari',
            'badge' => 'info',
            'icon' => 'bi-tag-fill'
        ],
        'PELAJAR' => [
            'nama' => 'Diskon Pelajar / Mahasiswa',
            'persen' => 12,
            'deskripsi' => 'Potongan 12% khusus pelajar & mahasiswa',
            'badge' => 'danger',
            'icon' => 'bi-mortarboard-fill'
        ],
        'JUMAT' => [
            'nama' => 'Jumat Berkah',
            'persen' => 30,
            'deskripsi' => 'Diskon istimewa 30% setiap hari Jumat',
            'badge' => 'dark',
            'icon' => 'bi-stars'
        ],
    ];

    /**
     * Menampilkan antarmuka POS Kasir dengan katalog menu dan kalkulasi tagihan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $produks = Produk::orderBy('nama_produk', 'asc')->get();
        $cart = session()->get('cart', []);
        $voucher = session()->get('voucher', null);

        // 1. Perhitungan Subtotal Keranjang
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['harga'] * $item['qty'];
        }

        // 2. Perhitungan Diskon (Hanya aktif jika kasir memasukkan/menerapkan kode voucher promo)
        $diskon = 0;
        $diskonPersen = 0;
        $keteranganDiskon = 'Tidak ada diskon';

        if ($voucher) {
            // Diskon Berdasarkan Kode Voucher Promo yang Dipilih Kasir
            $diskonPersen = (float) $voucher['persen'];
            $diskon = ($subtotal * $diskonPersen) / 100;
            $keteranganDiskon = "Voucher {$voucher['nama']} ({$diskonPersen}%)";
        }

        // 3. Dasar Pengenaan Pajak (DPP) setelah potongan diskon
        $dpp = max(0, $subtotal - $diskon);

        // 4. Perhitungan Pajak Resto (PPN 10% dari DPP)
        $pajak = $subtotal > 0 ? ($dpp * 0.10) : 0;

        // 5. Total Tagihan Akhir
        $totalBayar = $dpp + $pajak;

        $availableVouchers = self::$vouchers;

        return view('kasir.index', compact(
            'produks',
            'cart',
            'subtotal',
            'diskon',
            'diskonPersen',
            'keteranganDiskon',
            'pajak',
            'totalBayar',
            'voucher',
            'availableVouchers'
        ));
    }

    /**
     * Menambahkan produk ke dalam keranjang pesanan kasir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToCart(int $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan!');
        }

        if ($produk->stok <= 0) {
            return redirect()->back()->with('error', "Stok untuk menu {$produk->nama_produk} sedang habis!");
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id]['qty'] + 1 > $produk->stok) {
                return redirect()->back()->with('error', "Jumlah pesanan {$produk->nama_produk} melebihi sisa stok ({$produk->stok})!");
            }
            $cart[$id]['qty']++;
        } else {
            $cart[$id] = [
                "id" => $produk->id,
                "nama_produk" => $produk->nama_produk,
                "harga" => (float) $produk->harga,
                "qty" => 1,
                "stok_max" => $produk->stok
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', "{$produk->nama_produk} ditambahkan ke keranjang!");
    }

    /**
     * Menambah kuantitas (+1) item pada keranjang.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function plusQty(int $id)
    {
        $cart = session()->get('cart', []);
        $produk = Produk::find($id);

        if (isset($cart[$id])) {
            if ($produk && $cart[$id]['qty'] + 1 > $produk->stok) {
                return redirect()->back()->with('error', "Stok maksimal {$produk->nama_produk} hanya {$produk->stok}!");
            }
            $cart[$id]['qty']++;
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    /**
     * Mengurangi kuantitas (-1) item pada keranjang.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function minusQty(int $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['qty']--;
            if ($cart[$id]['qty'] <= 0) {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    /**
     * Menghapus salah satu item dari keranjang pesanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromCart(int $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('info', 'Item telah dihapus dari keranjang.');
    }

    /**
     * Mengosongkan seluruh keranjang belanja dan mereset voucher diskon.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCart()
    {
        session()->forget('cart');
        session()->forget('voucher');
        return redirect()->back()->with('info', 'Keranjang pesanan telah dikosongkan.');
    }

    /**
     * Memvalidasi dan menerapkan kode voucher diskon ke sesi kasir.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyVoucher(Request $request)
    {
        $code = strtoupper(trim($request->input('voucher_code', '')));

        if (empty($code)) {
            return redirect()->back()->with('error', 'Silakan masukkan kode voucher terlebih dahulu!');
        }

        if (array_key_exists($code, self::$vouchers)) {
            $data = self::$vouchers[$code];
            session()->put('voucher', [
                'code' => $code,
                'nama' => $data['nama'],
                'persen' => $data['persen'],
                'deskripsi' => $data['deskripsi'],
                'badge' => $data['badge']
            ]);

            return redirect()->back()->with('success', "Voucher '{$data['nama']}' berhasil diterapkan! Potongan {$data['persen']}%.");
        }

        return redirect()->back()->with('error', "Kode voucher '{$code}' tidak valid atau belum terdaftar.");
    }

    /**
     * Menghapus penerapan kode voucher aktif.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeVoucher()
    {
        session()->forget('voucher');
        return redirect()->back()->with('info', 'Kode voucher diskon telah dibatalkan.');
    }

    /**
     * Memproses checkout transaksi kasir:
     * 1. Validasi saldo pembayaran & keranjang
     * 2. Simpan header transaksi ke database
     * 3. Simpan relasi detail item pesanan
     * 4. Kurangi stok produk secara otomatis
     * 5. Buat data struk thermal untuk modal cetak
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang belanja masih kosong! Silakan pilih menu terlebih dahulu.');
        }

        // 1. Hitung Subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['harga'] * $item['qty'];
        }

        // 2. Hitung Diskon (Hanya aktif jika kasir memasukkan voucher promo)
        $voucher = session()->get('voucher', null);
        $diskon = 0;
        $diskonPersen = 0;
        $kodeVoucher = null;

        if ($voucher) {
            $kodeVoucher = $voucher['code'];
            $diskonPersen = (float) $voucher['persen'];
            $diskon = ($subtotal * $diskonPersen) / 100;
        }

        // 3. DPP, Pajak 10%, dan Total Bayar
        $dpp = max(0, $subtotal - $diskon);
        $pajak = ($dpp * 0.10);
        $totalBayar = $dpp + $pajak;

        // 4. Kalkulasi Pembayaran Tunai & Kembalian
        $uangBayarRaw = $request->input('uang_bayar');
        $uangBayar = $uangBayarRaw ? (float) str_replace(['.', ',', ' '], '', $uangBayarRaw) : $totalBayar;

        if ($uangBayar < $totalBayar) {
            return redirect()->back()->with('error', 'Nominal uang yang dibayarkan kurang dari total tagihan!');
        }

        $kembalian = max(0, $uangBayar - $totalBayar);

        // 5. Simpan Header Transaksi
        $transaksi = Transaksi::create([
            'tanggal' => Carbon::now(),
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'kode_voucher' => $kodeVoucher,
            'pajak' => $pajak,
            'total_bayar' => $totalBayar,
            'uang_bayar' => $uangBayar,
            'kembalian' => $kembalian,
        ]);

        // 6. Simpan Detail Transaksi & Kurangi Stok Produk Master
        foreach ($cart as $id => $item) {
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id'    => $id,
                'nama_produk'  => $item['nama_produk'],
                'harga'        => $item['harga'],
                'qty'          => $item['qty'],
                'subtotal'     => $item['harga'] * $item['qty'],
            ]);

            $produk = Produk::find($id);
            if ($produk) {
                $produk->stok = max(0, $produk->stok - $item['qty']);
                $produk->save();
            }
        }

        // 7. Siapkan Data Struk Thermal untuk Pop-up / Cetak
        $struk = [
            'id' => str_pad($transaksi->id, 5, '0', STR_PAD_LEFT),
            'raw_id' => $transaksi->id,
            'tanggal' => Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB',
            'items' => $cart,
            'subtotal' => $subtotal,
            'voucher' => $voucher,
            'kode_voucher' => $kodeVoucher,
            'diskon' => $diskon,
            'diskonPersen' => $diskonPersen,
            'pajak' => $pajak,
            'total_bayar' => $totalBayar,
            'uang_bayar' => $uangBayar,
            'kembalian' => $kembalian
        ];

        // 8. Bersihkan Sesi Belanja
        session()->forget('cart');
        session()->forget('voucher');

        return redirect()->route('kasir.index')
            ->with('success', 'Transaksi berhasil diproses & disimpan ke database!')
            ->with('struk', $struk);
    }
}
