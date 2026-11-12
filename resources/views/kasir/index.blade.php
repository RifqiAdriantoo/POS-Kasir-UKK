@extends('layouts.app')

@section('title', 'Kasir POS')
@section('page_heading', 'Point of Sale (POS) Kasir')
@section('page_subheading', 'Layar transaksi kasir, perhitungan diskon bertingkat/voucher, dan cetak struk')

@push('styles')
<style>
    .menu-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--card-border);
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -6px rgba(13, 148, 136, 0.15);
        border-color: #99f6e4;
    }
    .menu-image-box {
        position: relative;
        width: 100%;
        height: 140px;
        overflow: hidden;
        border-radius: 12px;
        background-color: #f1f5f9;
    }
    .menu-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .menu-card:hover .menu-img {
        transform: scale(1.06);
    }
    .btn-add-menu {
        border-radius: 10px;
        font-weight: 600;
        background-color: var(--primary-light);
        color: var(--primary-dark);
        border: 1px solid rgba(13, 148, 136, 0.2);
        transition: all 0.2s ease;
    }
    .btn-add-menu:hover {
        background-color: var(--primary);
        color: #ffffff;
    }
    .cart-sticky-panel {
        position: sticky;
        top: 85px;
    }
    .cart-item-row {
        background: #f8fafc;
        border-radius: 12px;
        padding: 0.75rem 0.9rem;
        margin-bottom: 0.65rem;
        border: 1px solid #f1f5f9;
    }
    .btn-qty-stepper {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: bold;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        text-decoration: none;
    }
    .btn-qty-stepper:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }
    .qty-badge {
        min-width: 28px;
        text-align: center;
        font-weight: 700;
    }
    .promo-chip {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 0.3rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .promo-chip:hover {
        background: #0f172a;
        color: #ffffff;
        transform: translateY(-2px);
    }
    .voucher-active-box {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #10b981;
        border-radius: 14px;
        padding: 0.75rem 1rem;
    }
    .total-box {
        background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%);
        border: 1px solid #99f6e4;
        border-radius: 14px;
        padding: 1rem;
    }
    .total-amount {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--primary-dark);
    }
    .quick-cash-btn {
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.25rem 0.5rem;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #334155;
        cursor: pointer;
    }
    .quick-cash-btn:hover {
        background: #e2e8f0;
    }
    .receipt-card {
        max-width: 380px;
        margin: 0 auto;
        background: #ffffff;
        border: 2px dashed #94a3b8;
        border-radius: 10px;
        padding: 1.5rem;
        font-family: 'Courier New', Courier, monospace;
        color: #1e293b;
    }
    .receipt-dashed-line {
        border-top: 1px dashed #64748b;
        margin: 0.8rem 0;
    }
    @media print {
        body * { visibility: hidden; }
        #printReceiptArea, #printReceiptArea * { visibility: visible; }
        #printReceiptArea { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- KOLOM KIRI: DAFTAR MENU -->
    <div class="col-lg-7 col-xl-8">
        <div class="modern-card p-3 p-lg-4">
            <!-- Search & Filter Tabs -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 filter-btn active" data-category="all">
                        Semua Menu ({{ count($produks) }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-btn" data-category="makanan">
                        🍜 Makanan
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-btn" data-category="minuman">
                        🥤 Minuman
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-btn" data-category="snack">
                        🍟 Snack
                    </button>
                </div>

                <div class="position-relative" style="min-width: 240px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="menuSearchInput" class="form-control form-control-sm ps-5 rounded-pill shadow-none" placeholder="Cari nama menu...">
                </div>
            </div>

            <!-- Grid Menu Cards -->
            <div class="row g-3" id="menuGridContainer">
                @forelse($produks as $p)
                    @php
                        $namaLower = strtolower($p->nama_produk);
                        $category = $p->kategori ?? (str_contains($namaLower, 'es') || str_contains($namaLower, 'teh') || str_contains($namaLower, 'kopi') ? 'minuman' : 'makanan');
                    @endphp

                    <div class="col-sm-6 col-md-6 col-xl-4 menu-item-card" data-name="{{ strtolower($p->nama_produk) }}" data-category="{{ $category }}">
                        <div class="menu-card h-100 p-2 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Image Container -->
                                <div class="menu-image-box mb-2">
                                    <img src="{{ $p->foto_url }}" alt="{{ $p->nama_produk }}" class="menu-img" loading="lazy">
                                    <!-- Category Pill -->
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-dark bg-opacity-75 rounded-pill px-2 py-1 shadow-sm" style="font-size: 0.7rem;">
                                            {{ $category == 'minuman' ? '🥤 Minuman' : ($category == 'snack' ? '🍟 Snack' : '🍜 Makanan') }}
                                        </span>
                                    </div>
                                    <!-- Stock Status Badge -->
                                    <div class="position-absolute top-0 end-0 m-2">
                                        @if($p->stok <= 0)
                                            <span class="badge bg-danger rounded-pill px-2 py-1 shadow-sm">Habis</span>
                                        @elseif($p->stok <= 5)
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 shadow-sm">Sisa {{ $p->stok }}</span>
                                        @else
                                            <span class="badge bg-success rounded-pill px-2 py-1 shadow-sm">Stok: {{ $p->stok }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="px-2 pt-1">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $p->nama_produk }}">{{ $p->nama_produk }}</h6>
                                    <div class="fw-bold text-teal fs-6 mb-2" style="color: var(--primary-dark);">
                                        Rp {{ number_format($p->harga, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="p-2 pt-0">
                                @if($p->stok > 0)
                                    <a href="{{ route('cart.add', $p->id) }}" class="btn btn-add-menu w-100 py-2 d-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-plus-circle-fill"></i> Tambah
                                    </a>
                                @else
                                    <button class="btn btn-secondary w-100 py-2 opacity-50" disabled>Habis</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-center text-muted">
                        Belum ada menu yang tersedia.
                    </div>
                @endforelse
            </div>

            <div id="noMenuFound" class="text-center py-5 d-none">
                <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                <h6 class="text-muted fw-bold">Menu tidak ditemukan</h6>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: KERANJANG, VOUCHER, & CHECKOUT -->
    <div class="col-lg-5 col-xl-4">
        <div class="modern-card p-3 p-lg-4 cart-sticky-panel">
            <!-- Header Cart -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-cart3 text-primary"></i> Pesanan
                    </h5>
                    @php
                        $totalItemsCount = 0;
                        foreach($cart as $c) { $totalItemsCount += $c['qty']; }
                    @endphp
                    <span class="badge bg-primary rounded-pill px-2 py-1">{{ $totalItemsCount }} item</span>
                </div>

                @if(!empty($cart))
                    <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1" onclick="return confirm('Kosongkan keranjang?')" style="font-size: 0.75rem;">
                        <i class="bi bi-trash3"></i> Kosongkan
                    </a>
                @endif
            </div>

            <!-- Cart Items List -->
            <div class="cart-items-container mb-3" style="max-height: 250px; overflow-y: auto;">
                @if(empty($cart))
                    <div class="text-center py-4">
                        <i class="bi bi-cart-x fs-2 text-muted d-block mb-1"></i>
                        <small class="text-muted">Keranjang masih kosong</small>
                    </div>
                @else
                    @foreach($cart as $id => $item)
                        <div class="cart-item-row">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div style="max-width: 65%;">
                                    <div class="fw-bold text-dark small text-truncate">{{ $item['nama_produk'] }}</div>
                                    <small class="text-muted">@ Rp {{ number_format($item['harga'], 0, ',', '.') }}</small>
                                </div>
                                <div class="text-end fw-bold text-teal small" style="color: var(--primary-dark);">
                                    Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1 border-top border-light-subtle">
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('cart.minus', $id) }}" class="btn-qty-stepper"><i class="bi bi-dash"></i></a>
                                    <span class="qty-badge text-dark small">{{ $item['qty'] }}</span>
                                    <a href="{{ route('cart.plus', $id) }}" class="btn-qty-stepper"><i class="bi bi-plus"></i></a>
                                </div>
                                <a href="{{ route('cart.remove', $id) }}" class="text-danger small text-decoration-none">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- VOUCHER PROMO & DISKON BERTINGKAT -->
            <div class="mb-3">
                <label class="form-label fw-bold text-dark small mb-1 d-flex align-items-center gap-1">
                    <i class="bi bi-ticket-perforated-fill text-warning"></i> Kupon Diskon / Promo
                </label>

                @if($voucher)
                    <div class="voucher-active-box d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-success small">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ $voucher['nama'] }} ({{ $voucher['persen'] }}%)
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">
                                Kode: <strong>{{ $voucher['code'] }}</strong>
                            </small>
                        </div>
                        <a href="{{ route('voucher.remove') }}" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                            Batal
                        </a>
                    </div>
                @else
                    <form action="{{ route('voucher.apply') }}" method="POST" class="mb-2">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="text" name="voucher_code" class="form-control text-uppercase fw-semibold" placeholder="Ketik kode kupon..." autocomplete="off">
                            <button class="btn btn-dark fw-bold px-3" type="submit">Terapkan</button>
                        </div>
                    </form>

                    <!-- Quick Chips -->
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($availableVouchers as $code => $v)
                            <button type="button" class="promo-chip" onclick="applyQuickVoucher('{{ $code }}')">
                                <i class="bi {{ $v['icon'] }}"></i> {{ $code }} ({{ $v['persen'] }}%)
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- SUMMARY BREAKDOWN -->
            <div class="bg-light rounded-4 p-3 mb-3 border">
                <div class="d-flex justify-content-between small text-secondary mb-1">
                    <span>Subtotal:</span>
                    <span class="fw-semibold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="d-flex justify-content-between small mb-1 {{ $diskon > 0 ? 'text-success fw-semibold' : 'text-muted' }}">
                    <span>Diskon {{ $diskonPersen > 0 ? "({$diskonPersen}%)" : '(0%)' }}:</span>
                    <span>{{ $diskon > 0 ? '- Rp ' . number_format($diskon, 0, ',', '.') : 'Rp 0' }}</span>
                </div>

                <div class="d-flex justify-content-between small text-secondary mb-2">
                    <span>Pajak Resto (10%):</span>
                    <span class="fw-semibold text-dark">+ Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                </div>

                <div class="total-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Total Tagihan</small>
                            <div class="total-amount" id="displayTotalBayar" data-total="{{ $totalBayar }}">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </div>
                        </div>
                        <span class="badge bg-teal rounded-pill px-2 py-1 text-white" style="background-color: var(--primary);">
                            Siap Bayar
                        </span>
                    </div>
                </div>
            </div>

            <!-- PAYMENT FORM -->
            <form action="{{ route('cart.checkout') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold text-dark small mb-0">Uang Diterima (Rp)</label>
                        <span class="text-muted" style="font-size: 0.72rem;">Nominal Cepat:</span>
                    </div>

                    <input type="number" name="uang_bayar" id="uangBayarInput" class="form-control form-control-lg fw-bold rounded-3 mb-2" placeholder="0" {{ empty($cart) ? 'disabled' : '' }}>

                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <button type="button" class="quick-cash-btn" onclick="setCashAmount({{ $totalBayar }})">Uang Pas</button>
                        <button type="button" class="quick-cash-btn" onclick="setCashAmount(50000)">50.000</button>
                        <button type="button" class="quick-cash-btn" onclick="setCashAmount(100000)">100.000</button>
                        <button type="button" class="quick-cash-btn" onclick="setCashAmount(200000)">200.000</button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border">
                        <small class="text-secondary fw-semibold">Kembalian:</small>
                        <span class="fw-bold fs-6 text-dark" id="displayKembalian">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-3 rounded-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" {{ empty($cart) ? 'disabled' : '' }}>
                    <i class="bi bi-printer-fill fs-5"></i> Proses Transaksi & Cetak Struk
                </button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL STRUK POP-UP -->
@if(session('struk'))
@php $struk = session('struk'); @endphp
<div class="modal fade show d-block" id="receiptModal" tabindex="-1" style="background: rgba(0,0,0,0.65);" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-receipt text-primary"></i> Struk Pembayaran Sukses</h6>
                <a href="{{ route('kasir.index') }}" class="btn-close" aria-label="Close"></a>
            </div>
            <div class="modal-body p-4" id="printReceiptArea">
                <div class="receipt-card">
                    <div class="text-center mb-2">
                        <h5 class="fw-bold mb-0">RESTOKITA</h5>
                        <small class="d-block text-muted">Jl. Pendidikan No. 45, Kota Sukses</small>
                        <small class="d-block text-muted">Telp: (021) 8899-7766</small>
                    </div>

                    <div class="receipt-dashed-line"></div>

                    <div style="font-size: 0.8rem;" class="mb-2">
                        <div>No. Struk : #{{ $struk['id'] }}</div>
                        <div>Tanggal   : {{ $struk['tanggal'] }}</div>
                        <div>Kasir     : {{ Auth::check() ? Auth::user()->name : 'Kasir Utama' }}</div>
                    </div>

                    <div class="receipt-dashed-line"></div>

                    <!-- Items Table -->
                    <div class="mb-2" style="font-size: 0.82rem;">
                        @foreach($struk['items'] as $item)
                        <div class="d-flex justify-content-between">
                            <span>{{ $item['nama_produk'] }}</span>
                            <span>Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            {{ $item['qty'] }} x Rp {{ number_format($item['harga'], 0, ',', '.') }}
                        </div>
                        @endforeach
                    </div>

                    <div class="receipt-dashed-line"></div>

                    <!-- Calculations -->
                    <div style="font-size: 0.82rem;">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($struk['subtotal'], 0, ',', '.') }}</span>
                        </div>

                        @if($struk['diskon'] > 0)
                        <div class="d-flex justify-content-between text-success">
                            <span>Diskon ({{ $struk['diskonPersen'] }}%)</span>
                            <span>- Rp {{ number_format($struk['diskon'], 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <span>Pajak Resto (10%)</span>
                            <span>Rp {{ number_format($struk['pajak'], 0, ',', '.') }}</span>
                        </div>

                        <div class="receipt-dashed-line"></div>

                        <div class="d-flex justify-content-between fw-bold fs-6">
                            <span>TOTAL BAYAR</span>
                            <span>Rp {{ number_format($struk['total_bayar'], 0, ',', '.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Uang Diterima</span>
                            <span>Rp {{ number_format($struk['uang_bayar'], 0, ',', '.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Kembalian</span>
                            <span>Rp {{ number_format($struk['kembalian'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="receipt-dashed-line"></div>

                    <div class="text-center mt-3" style="font-size: 0.75rem;">
                        <div class="fw-bold">TERIMA KASIH ATAS KUNJUNGAN ANDA!</div>
                        <small class="text-muted">Simpan struk ini sebagai bukti pembayaran yang sah.</small>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 d-flex justify-content-between no-print">
                <a href="{{ route('kasir.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Transaksi Baru
                </a>
                <button type="button" class="btn btn-dark rounded-pill px-4 d-flex align-items-center gap-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Hidden Form for quick voucher apply -->
<form id="quickVoucherForm" action="{{ route('voucher.apply') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="voucher_code" id="quickVoucherInput">
</form>
@endsection

@push('scripts')
<script>
    // Live Search
    const searchInput = document.getElementById('menuSearchInput');
    const menuItems = document.querySelectorAll('.menu-item-card');
    const noMenuFound = document.getElementById('noMenuFound');

    if (searchInput) {
        searchInput.addEventListener('input', filterMenus);
    }

    let currentCategory = 'all';
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => {
                b.classList.remove('btn-dark', 'active');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-dark', 'active');
            currentCategory = this.getAttribute('data-category');
            filterMenus();
        });
    });

    function filterMenus() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        menuItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const category = item.getAttribute('data-category');

            const matchesSearch = name.includes(query);
            const matchesCategory = (currentCategory === 'all' || category === currentCategory);

            if (matchesSearch && matchesCategory) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (visibleCount === 0) {
            noMenuFound.classList.remove('d-none');
        } else {
            noMenuFound.classList.add('d-none');
        }
    }

    function applyQuickVoucher(code) {
        document.getElementById('quickVoucherInput').value = code;
        document.getElementById('quickVoucherForm').submit();
    }

    // Kembalian Calculator
    const totalBayarEl = document.getElementById('displayTotalBayar');
    const uangBayarInput = document.getElementById('uangBayarInput');
    const displayKembalian = document.getElementById('displayKembalian');

    function calculateKembalian() {
        if (!totalBayarEl || !uangBayarInput) return;
        const total = parseFloat(totalBayarEl.getAttribute('data-total')) || 0;
        const cash = parseFloat(uangBayarInput.value) || 0;

        if (cash <= 0) {
            displayKembalian.textContent = 'Rp 0';
            displayKembalian.className = 'fw-bold fs-6 text-dark';
            return;
        }

        const change = cash - total;
        if (change >= 0) {
            displayKembalian.textContent = 'Rp ' + Math.round(change).toLocaleString('id-ID');
            displayKembalian.className = 'fw-bold fs-6 text-success';
        } else {
            const shortage = Math.abs(change);
            displayKembalian.textContent = 'Kurang Rp ' + Math.round(shortage).toLocaleString('id-ID');
            displayKembalian.className = 'fw-bold fs-6 text-danger';
        }
    }

    if (uangBayarInput) {
        uangBayarInput.addEventListener('input', calculateKembalian);
    }

    function setCashAmount(amount) {
        if (uangBayarInput) {
            uangBayarInput.value = amount;
            calculateKembalian();
        }
    }
</script>
@endpush
