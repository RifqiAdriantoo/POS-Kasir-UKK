<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;

/**
 * Class TransaksiController
 * 
 * Mengelola fitur Riwayat Transaksi Penjualan, pelacakan histori pesanan,
 * detail belanja, dan fungsi pencetakan ulang struk kasir.
 * 
 * @package App\Http\Controllers
 */
class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar riwayat transaksi dengan opsi filter tanggal dan pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Transaksi::with('details')->orderBy('tanggal', 'desc');

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [
                $request->input('start_date') . ' 00:00:00',
                $request->input('end_date') . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->input('start_date'));
        }

        $transaksis = $query->paginate(15)->withQueryString();

        // Ringkasan Total Riwayat Terfilter
        $totalNominal = $transaksis->sum('total_bayar');

        return view('transaksi.index', compact('transaksis', 'totalNominal'));
    }

    /**
     * Menampilkan detail item transaksi (JSON / Modal).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $transaksi = Transaksi::with('details')->findOrFail($id);

        return response()->json([
            'success'   => true,
            'transaksi' => $transaksi,
        ]);
    }

    /**
     * Menampilkan halaman khusus cetak struk thermal untuk transaksi tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function cetakStruk(int $id)
    {
        $transaksi = Transaksi::with('details')->findOrFail($id);

        return view('transaksi.struk', compact('transaksi'));
    }
}
