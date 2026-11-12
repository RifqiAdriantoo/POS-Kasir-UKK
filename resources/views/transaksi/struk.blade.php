<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }} - RestoKita</title>
    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f5f9;
            padding: 2rem 1rem;
            font-family: 'Courier New', Courier, monospace;
            color: #0f172a;
        }

        .thermal-receipt {
            max-width: 380px;
            margin: 0 auto;
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .receipt-dashed {
            border-top: 1px dashed #64748b;
            margin: 0.8rem 0;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .thermal-receipt {
                box-shadow: none;
                border: none;
                max-width: 100%;
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="text-center mb-3 no-print">
        <button class="btn btn-dark rounded-pill px-4 fw-bold me-2" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak Struk
        </button>
        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            Kembali
        </a>
    </div>

    <div class="thermal-receipt">
        <div class="text-center mb-2">
            <h4 class="fw-bold mb-0">RESTOKITA</h4>
            <small class="d-block text-muted">Jl. Pendidikan No. 45, Kota Sukses</small>
            <small class="d-block text-muted">Telp: (021) 8899-7766</small>
        </div>

        <div class="receipt-dashed"></div>

        <div style="font-size: 0.82rem;" class="mb-2">
            <div>No. Struk : #{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div>Waktu     : {{ \Carbon\Carbon::parse($transaksi->tanggal)->isoFormat('D MMMM Y, HH:mm') }} WIB</div>
            <div>Kasir     : Kasir Resto</div>
        </div>

        <div class="receipt-dashed"></div>

        <div class="mb-2" style="font-size: 0.82rem;">
            @foreach($transaksi->details as $d)
            <div class="d-flex justify-content-between">
                <span>{{ $d->nama_produk }}</span>
                <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="text-muted" style="font-size: 0.72rem;">
                {{ $d->qty }} x Rp {{ number_format($d->harga, 0, ',', '.') }}
            </div>
            @endforeach
        </div>

        <div class="receipt-dashed"></div>

        <div style="font-size: 0.82rem;">
            <div class="d-flex justify-content-between">
                <span>Subtotal</span>
                <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
            </div>

            @if($transaksi->diskon > 0)
            <div class="d-flex justify-content-between text-success">
                <span>Diskon {{ $transaksi->kode_voucher ? "({$transaksi->kode_voucher})" : '' }}</span>
                <span>- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="d-flex justify-content-between">
                <span>Pajak Resto (10%)</span>
                <span>Rp {{ number_format($transaksi->pajak, 0, ',', '.') }}</span>
            </div>

            <div class="receipt-dashed"></div>

            <div class="d-flex justify-content-between fw-bold fs-6">
                <span>TOTAL BAYAR</span>
                <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
            </div>

            @if($transaksi->uang_bayar)
            <div class="d-flex justify-content-between">
                <span>Uang Diterima</span>
                <span>Rp {{ number_format($transaksi->uang_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Kembalian</span>
                <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="receipt-dashed"></div>

        <div class="text-center mt-3" style="font-size: 0.75rem;">
            <div class="fw-bold">TERIMA KASIH ATAS KUNJUNGAN ANDA!</div>
            <small class="text-muted">Makanan & Minuman yang sudah dibeli tidak dapat ditukar.</small>
        </div>
    </div>

</body>
</html>
