<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Carbon\Carbon;

/**
 * Class DashboardController
 * 
 * Mengelola penyajian data ringkasan analitik dan statistik operasional restoran
 * untuk ditampilkan pada halaman utama Dashboard POS RestoKita.
 * 
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan statistik penjualan dan inventaris menu.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $today = Carbon::today();

        // 1. Statistik Finansial & Transaksi
        $omzetHariIni = Transaksi::whereDate('tanggal', $today)->sum('total_bayar');
        $transaksiHariIni = Transaksi::whereDate('tanggal', $today)->count();
        $totalOmzet = Transaksi::sum('total_bayar');
        $totalTransaksi = Transaksi::count();

        // 2. Statistik Produk & Menu
        $totalProduk = Produk::count();
        $stokMenipis = Produk::where('stok', '<=', 5)->get();

        // 3. 5 Transaksi Terkini
        $transaksiTerbaru = Transaksi::with('details')
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        // 4. Menu Terlaris (Top Selling Items)
        $topMenu = DetailTransaksi::selectRaw('nama_produk, SUM(qty) as total_terjual')
            ->groupBy('nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'omzetHariIni',
            'transaksiHariIni',
            'totalOmzet',
            'totalTransaksi',
            'totalProduk',
            'stokMenipis',
            'transaksiTerbaru',
            'topMenu'
        ));
    }
}
