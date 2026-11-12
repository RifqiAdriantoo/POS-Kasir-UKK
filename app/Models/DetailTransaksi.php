<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DetailTransaksi
 * 
 * Model untuk merepresentasikan rincian item pesanan dalam sebuah transaksi.
 * Memenuhi relasi 1:M antara Transaksi dan Detail Transaksi.
 * 
 * @property int $id
 * @property int $transaksi_id
 * @property int|null $produk_id
 * @property string $nama_produk
 * @property float $harga
 * @property int $qty
 * @property float $subtotal
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @package App\Models
 */
class DetailTransaksi extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * 
     * @var string
     */
    protected $table = 'detail_transaksis';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'nama_produk',
        'harga',
        'qty',
        'subtotal',
    ];

    /**
     * Relasi ke header transaksi (Many-to-One).
     * 
     * @return BelongsTo
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    /**
     * Relasi ke entitas produk master (Many-to-One).
     * 
     * @return BelongsTo
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
