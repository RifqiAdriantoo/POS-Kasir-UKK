<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Produk
 * 
 * Model untuk merepresentasikan entitas menu/produk makanan dan minuman
 * di sistem POS RestoKita.
 * 
 * @property int $id
 * @property string $nama_produk
 * @property string $kategori
 * @property string|null $foto
 * @property float $harga
 * @property int $stok
 * @property-read string|null $foto_url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @package App\Models
 */
class Produk extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * 
     * @var string
     */
    protected $table = 'produks';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_produk',
        'kategori',
        'foto',
        'harga',
        'stok',
    ];

    /**
     * Relasi ke detail transaksi (One-to-Many).
     * 
     * @return HasMany
     */
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'produk_id');
    }

    /**
     * Accessor untuk mendapatkan URL foto produk yang di-upload.
     * Mengembalikan URL foto jika ada di folder public/uploads/produk, atau null jika belum ada.
     * 
     * @return string|null
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto && file_exists(public_path('uploads/produk/' . $this->foto))) {
            return asset('uploads/produk/' . $this->foto);
        }

        return null;
    }
}
