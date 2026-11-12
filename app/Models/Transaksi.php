<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Transaksi
 * 
 * Model untuk merepresentasikan transaksi penjualan kasir di RestoKita.
 * Menyimpan ringkasan finansial: subtotal, diskon, pajak, total bayar,
 * serta informasi uang tunai dan kembalian.
 * 
 * @property int $id
 * @property \Carbon\Carbon|string $tanggal
 * @property float $subtotal
 * @property float $diskon
 * @property string|null $kode_voucher
 * @property float $pajak
 * @property float $total_bayar
 * @property float|null $uang_bayar
 * @property float|null $kembalian
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @package App\Models
 */
class Transaksi extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * 
     * @var string
     */
    protected $table = 'transaksis';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal',
        'subtotal',
        'diskon',
        'kode_voucher',
        'pajak',
        'total_bayar',
        'uang_bayar',
        'kembalian',
    ];

    /**
     * Relasi ke rincian detail pesanan (One-to-Many).
     * 
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }
}
