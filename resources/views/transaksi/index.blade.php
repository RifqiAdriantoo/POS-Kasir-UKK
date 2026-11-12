@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page_heading', 'Riwayat Transaksi Penjualan')
@section('page_subheading', 'Daftar seluruh transaksi yang telah disimpan, rincian menu pesanan, dan cetak ulang struk')

@section('content')
<!-- Filter & Summary Bar -->
<div class="modern-card p-3 p-lg-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <!-- Date Filter Form -->
        <form action="{{ route('transaksi.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <div>
                <label class="form-label small fw-bold text-muted mb-0">Dari Tanggal:</label>
                <input type="date" name="start_date" class="form-control form-control-sm rounded-pill" value="{{ request('start_date') }}">
            </div>
            <div>
                <label class="form-label small fw-bold text-muted mb-0">Sampai Tanggal:</label>
                <input type="date" name="end_date" class="form-control form-control-sm rounded-pill" value="{{ request('end_date') }}">
            </div>
            <div class="align-self-end">
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">
                    <i class="bi bi-filter"></i> Filter
                </button>
                @if(request('start_date') || request('end_date'))
                <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Quick Summary Badge -->
        <div class="text-md-end bg-light p-2 px-3 rounded-4 border">
            <small class="text-muted d-block" style="font-size: 0.75rem;">Total Nilai Transaksi Terfilter:</small>
            <span class="fs-5 fw-bold text-teal" style="color: var(--primary-dark);">
                Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </span>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="modern-card p-3 p-lg-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>No. Struk</th>
                    <th>Waktu Transaksi</th>
                    <th>Subtotal</th>
                    <th>Diskon</th>
                    <th>Pajak (10%)</th>
                    <th>Total Bayar</th>
                    <th>Pembayaran</th>
                    <th class="text-end" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $t)
                <tr>
                    <td class="fw-bold text-primary">#{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="small fw-semibold text-dark">{{ \Carbon\Carbon::parse($t->tanggal)->isoFormat('D MMMM Y') }}</div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($t->tanggal)->format('H:i') }} WIB</small>
                    </td>
                    <td>Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                    <td>
                        @if($t->diskon > 0)
                            <span class="text-danger fw-semibold">- Rp {{ number_format($t->diskon, 0, ',', '.') }}</span>
                            @if($t->kode_voucher)
                                <small class="d-block badge bg-success-subtle text-success border border-success-subtle rounded-pill">{{ $t->kode_voucher }}</small>
                            @endif
                        @else
                            <span class="text-muted">Rp 0</span>
                        @endif
                    </td>
                    <td>+ Rp {{ number_format($t->pajak, 0, ',', '.') }}</td>
                    <td>
                        <div class="fw-bold text-dark fs-6">
                            Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="small">
                        @if($t->uang_bayar)
                            <div>Bayar: Rp {{ number_format($t->uang_bayar, 0, ',', '.') }}</div>
                            <small class="text-muted">Kembali: Rp {{ number_format($t->kembalian, 0, ',', '.') }}</small>
                        @else
                            <span class="text-muted">Lunas</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <!-- Detail Modal Trigger -->
                            <button type="button" class="btn btn-outline-secondary rounded-start-pill px-2" title="Lihat Item Pesanan" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $t->id }}">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                            <!-- Print Thermal Receipt -->
                            <a href="{{ route('transaksi.cetak', $t->id) }}" target="_blank" class="btn btn-outline-dark rounded-end-pill px-2" title="Cetak Ulang Struk">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>

                        <!-- Modal Detail Transaksi -->
                        <div class="modal fade" id="modalDetail{{ $t->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i class="bi bi-receipt text-primary me-1"></i> Rincian Struk #{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="d-flex justify-content-between small text-muted mb-3 pb-2 border-bottom">
                                            <span>Tanggal: {{ \Carbon\Carbon::parse($t->tanggal)->isoFormat('D MMMM Y, HH:mm') }} WIB</span>
                                            <span class="badge bg-success-subtle text-success">Lunas</span>
                                        </div>

                                        <h6 class="fw-bold small text-dark mb-2">Item Menu yang Dipesan:</h6>
                                        <ul class="list-group list-group-flush mb-3">
                                            @forelse($t->details as $d)
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $d->nama_produk }}</div>
                                                    <small class="text-muted">{{ $d->qty }} x Rp {{ number_format($d->harga, 0, ',', '.') }}</small>
                                                </div>
                                                <span class="fw-bold text-teal" style="color: var(--primary-dark);">
                                                    Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                                                </span>
                                            </li>
                                            @empty
                                            <li class="list-group-item text-muted text-center py-2">
                                                Detail item tidak tercatat.
                                            </li>
                                            @endforelse
                                        </ul>

                                        <!-- Summary -->
                                        <div class="bg-light p-3 rounded-3 small border">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Subtotal:</span>
                                                <span class="fw-semibold">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                            @if($t->diskon > 0)
                                            <div class="d-flex justify-content-between text-danger mb-1">
                                                <span>Diskon {{ $t->kode_voucher ? "({$t->kode_voucher})" : '' }}:</span>
                                                <span>- Rp {{ number_format($t->diskon, 0, ',', '.') }}</span>
                                            </div>
                                            @endif
                                            <div class="d-flex justify-content-between text-secondary mb-2">
                                                <span>Pajak (10%):</span>
                                                <span>+ Rp {{ number_format($t->pajak, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-2 text-dark">
                                                <span>Total Tagihan:</span>
                                                <span>Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                        <a href="{{ route('transaksi.cetak', $t->id) }}" target="_blank" class="btn btn-dark rounded-pill px-4">
                                            <i class="bi bi-printer me-1"></i> Cetak Struk
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        Tidak ada transaksi yang tercatat dalam periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($transaksis->hasPages())
    <div class="mt-4 d-flex justify-content-end">
        {{ $transaksis->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
