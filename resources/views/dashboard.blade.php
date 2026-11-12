@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_heading', 'Dashboard Restoran')
@section('page_subheading', 'Ringkasan operasional penjualan dan analitik inventaris RestoKita')

@section('content')
<div class="row g-3 mb-4">
    <!-- Card 1: Omzet Hari Ini -->
    <div class="col-sm-6 col-xl-3">
        <div class="modern-card p-3 p-xl-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Pendapatan Hari Ini</span>
                <div class="p-2 bg-success-subtle rounded-3 text-success">
                    <i class="bi bi-cash-coin fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</h3>
            <small class="text-success"><i class="bi bi-calendar-check me-1"></i> Hari ini</small>
        </div>
    </div>

    <!-- Card 2: Transaksi Hari Ini -->
    <div class="col-sm-6 col-xl-3">
        <div class="modern-card p-3 p-xl-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Transaksi Hari Ini</span>
                <div class="p-2 bg-primary-subtle rounded-3 text-primary">
                    <i class="bi bi-receipt fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $transaksiHariIni }} Pesanan</h3>
            <small class="text-muted">Total struk tercatat hari ini</small>
        </div>
    </div>

    <!-- Card 3: Total Omzet Keseluruhan -->
    <div class="col-sm-6 col-xl-3">
        <div class="modern-card p-3 p-xl-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Total Seluruh Omzet</span>
                <div class="p-2 bg-warning-subtle rounded-3 text-warning-emphasis">
                    <i class="bi bi-wallet2 fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
            <small class="text-muted">Dari {{ $totalTransaksi }} total transaksi</small>
        </div>
    </div>

    <!-- Card 4: Total Menu Aktif -->
    <div class="col-sm-6 col-xl-3">
        <div class="modern-card p-3 p-xl-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Total Menu Resto</span>
                <div class="p-2 bg-info-subtle rounded-3 text-info">
                    <i class="bi bi-box-seam fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $totalProduk }} Menu</h3>
            <small class="text-muted">Makanan, minuman & snack</small>
        </div>
    </div>
</div>

<!-- Warning Low Stock Alert -->
@if($stokMenipis->isNotEmpty())
<div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning-emphasis"></i>
        <div>
            <strong>Perhatian:</strong> Ada <strong>{{ $stokMenipis->count() }} menu</strong> dengan stok menipis (≤ 5 porsi):
            <span class="text-muted ms-1">
                @foreach($stokMenipis as $sm)
                    <span class="badge bg-warning text-dark">{{ $sm->nama_produk }} ({{ $sm->stok }})</span>
                @endforeach
            </span>
        </div>
    </div>
    <a href="{{ route('produk.index') }}" class="btn btn-sm btn-dark rounded-pill px-3">
        Kelola Stok
    </a>
</div>
@endif

<!-- Quick Access Banner -->
<div class="modern-card p-4 mb-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0d9488 100%);">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold mb-2">Siap Melayani Pelanggan Restoran?</h4>
            <p class="text-secondary small mb-0 text-white-50">
                Gunakan modul POS Kasir dengan fitur keranjang instan, diskon voucher promo bertingkat (10% - 30%), kalkulator kembalian, dan cetak struk thermal.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('kasir.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                <i class="bi bi-calculator me-1"></i> Buka Layar Kasir
            </a>
        </div>
    </div>
</div>

<!-- Tables Section: 5 Transaksi Terkini & Top Selling Menu -->
<div class="row g-4">
    <!-- Transaksi Terkini -->
    <div class="col-lg-8">
        <div class="modern-card p-3 p-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-1 text-primary"></i> 5 Transaksi Terkini
                </h6>
                <a href="{{ route('transaksi.index') }}" class="small text-decoration-none text-primary fw-semibold">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>No. Struk</th>
                            <th>Waktu</th>
                            <th>Subtotal</th>
                            <th>Diskon</th>
                            <th>Pajak (10%)</th>
                            <th>Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($transaksiTerbaru as $t)
                        <tr>
                            <td class="fw-bold text-primary">#{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y, H:i') }}</td>
                            <td>Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                            <td>
                                @if($t->diskon > 0)
                                    <span class="text-danger">- Rp {{ number_format($t->diskon, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($t->pajak, 0, ',', '.') }}</td>
                            <td class="fw-bold text-dark">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada transaksi yang tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Selling Menu -->
    <div class="col-lg-4">
        <div class="modern-card p-3 p-lg-4">
            <h6 class="fw-bold mb-3 text-dark">
                <i class="bi bi-trophy-fill text-warning me-1"></i> Menu Terlaris
            </h6>

            @if($topMenu->isEmpty())
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i>
                    Belum ada menu yang terjual.
                </div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($topMenu as $index => $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border rounded-circle" style="width: 24px; height: 24px; line-height: 18px; text-align: center;">
                                {{ $index + 1 }}
                            </span>
                            <span class="fw-semibold text-dark text-truncate" style="max-width: 170px;">
                                {{ $item->nama_produk }}
                            </span>
                        </div>
                        <span class="badge bg-teal text-white rounded-pill px-2 py-1" style="background-color: var(--primary);">
                            {{ $item->total_terjual }} porsi
                        </span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
