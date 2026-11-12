@extends('layouts.app')

@section('title', 'Manajemen Menu')
@section('page_heading', 'Manajemen Menu & Produk')
@section('page_subheading', 'Kelola inventaris makanan, minuman, harga satuan, dan kuota stok')

@section('content')
<div class="modern-card p-3 p-lg-4 mb-4">
    <!-- Header Action & Filters -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-box-seam me-1 text-primary"></i> Daftar Menu Restoran
            </h5>
            <small class="text-muted">Total menu terdaftar: <strong>{{ count($produks) }}</strong> item</small>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search & Filter Form -->
            <form action="{{ route('produk.index') }}" method="GET" class="d-flex gap-2">
                <select name="kategori" class="form-select form-select-sm rounded-pill shadow-none" onchange="this.form.submit()">
                    <option value="all">Semua Kategori</option>
                    <option value="makanan" {{ request('kategori') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="minuman" {{ request('kategori') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="snack" {{ request('kategori') == 'snack' ? 'selected' : '' }}>Snack</option>
                </select>

                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control rounded-start-pill shadow-none" placeholder="Cari menu..." value="{{ request('search') }}">
                    <button class="btn btn-dark rounded-end-pill px-3" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Tombol Tambah Menu Baru -->
            <button type="button" class="btn btn-teal text-white btn-sm rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                <i class="bi bi-plus-circle-fill"></i> Tambah Menu
            </button>
        </div>
    </div>

    <!-- Table of Products -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga Satuan</th>
                    <th>Status Stok</th>
                    <th class="text-end" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $index => $p)
                <tr>
                    <td class="text-muted small">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $p->foto_url }}" alt="{{ $p->nama_produk }}" class="rounded-3 shadow-sm border" style="width: 44px; height: 44px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark">{{ $p->nama_produk }}</div>
                                <small class="text-muted">ID: #{{ $p->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($p->kategori == 'minuman')
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1">
                                <i class="bi bi-cup-straw me-1"></i>Minuman
                            </span>
                        @elseif($p->kategori == 'snack')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">
                                <i class="bi bi-cookie me-1"></i>Snack
                            </span>
                        @else
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                <i class="bi bi-egg-fried me-1"></i>Makanan
                            </span>
                        @endif
                    </td>
                    <td class="fw-semibold text-teal" style="color: var(--primary-dark);">
                        Rp {{ number_format($p->harga, 0, ',', '.') }}
                    </td>
                    <td>
                        @if($p->stok <= 0)
                            <span class="badge bg-danger rounded-pill px-2 py-1">Habis (0)</span>
                        @elseif($p->stok <= 5)
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1">Sisa {{ $p->stok }}</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                {{ $p->stok }} Porsi
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-outline-secondary rounded-start-pill px-2" title="Edit Menu" data-bs-toggle="modal" data-bs-target="#modalEditProduk{{ $p->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <!-- Delete Button -->
                            <form action="{{ route('produk.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus menu {{ $p->nama_produk }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger rounded-end-pill px-2" title="Hapus Menu">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Modal Edit Produk -->
                        <div class="modal fade" id="modalEditProduk{{ $p->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i class="bi bi-pencil-square text-primary me-1"></i> Edit Menu: {{ $p->nama_produk }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('produk.update', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Menu Makanan / Minuman</label>
                                                <input type="text" name="nama_produk" class="form-control" value="{{ $p->nama_produk }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Kategori</label>
                                                <select name="kategori" class="form-select" required>
                                                    <option value="makanan" {{ $p->kategori == 'makanan' ? 'selected' : '' }}>Makanan</option>
                                                    <option value="minuman" {{ $p->kategori == 'minuman' ? 'selected' : '' }}>Minuman</option>
                                                    <option value="snack" {{ $p->kategori == 'snack' ? 'selected' : '' }}>Snack</option>
                                                </select>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label small fw-bold">Harga Satuan (Rp)</label>
                                                    <input type="number" name="harga" class="form-control" value="{{ (int)$p->harga }}" min="500" required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label small fw-bold">Jumlah Stok</label>
                                                    <input type="number" name="stok" class="form-control" value="{{ $p->stok }}" min="0" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-teal text-white rounded-pill px-4 fw-semibold" style="background-color: var(--primary);">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        Tidak ada data menu yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Produk Baru -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="bi bi-plus-circle text-primary me-1"></i> Tambah Menu Resto Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Menu Makanan / Minuman</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Nasi Goreng Gila" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kategori Menu</label>
                        <select name="kategori" class="form-select" required>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">Harga Satuan (Rp)</label>
                            <input type="number" name="harga" class="form-control" placeholder="25000" min="500" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">Stok Awal</label>
                            <input type="number" name="stok" class="form-control" placeholder="30" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal text-white rounded-pill px-4 fw-semibold" style="background-color: var(--primary);">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
