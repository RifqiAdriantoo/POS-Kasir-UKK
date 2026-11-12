<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Produk;

/**
 * DatabaseSeeder
 * 
 * Mengisi data awal untuk keperluan pengujian dan operasional aplikasi RestoKita,
 * termasuk akun pengguna default (Kasir & Admin) serta katalog menu awal.
 * 
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Seed Akun Pengguna Tunggal (Kasir RestoKita)
        if (User::count() === 0) {
            User::create([
                'name' => 'Kasir RestoKita',
                'email' => 'kasir@restokita.com',
                'password' => Hash::make('password123'),
            ]);
        }

        // 2. Seed Katalog Produk Awal (Jika masih kosong)
        if (Produk::count() === 0) {
            $menus = [
                ['nama_produk' => 'Nasi Goreng Spesial', 'kategori' => 'makanan', 'harga' => 25000, 'stok' => 30],
                ['nama_produk' => 'Ayam Bakar Madu', 'kategori' => 'makanan', 'harga' => 28000, 'stok' => 25],
                ['nama_produk' => 'Mie Goreng Seafood', 'kategori' => 'makanan', 'harga' => 22000, 'stok' => 20],
                ['nama_produk' => 'Es Teh Manis Jumbo', 'kategori' => 'minuman', 'harga' => 5000, 'stok' => 50],
                ['nama_produk' => 'Es Kopi Susu Gula Aren', 'kategori' => 'minuman', 'harga' => 18000, 'stok' => 40],
                ['nama_produk' => 'Matcha Latte Ice', 'kategori' => 'minuman', 'harga' => 20000, 'stok' => 20],
            ];

            foreach ($menus as $m) {
                Produk::create($m);
            }
        }
    }
}
