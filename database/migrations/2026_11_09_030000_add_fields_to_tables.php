<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk menambahkan kolom kategori pada tabel produks
 * dan kolom informasi pembayaran pada tabel transaksis.
 * 
 * @package Database\Migrations
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('produks') && !Schema::hasColumn('produks', 'kategori')) {
            Schema::table('produks', function (Blueprint $table) {
                $table->string('kategori', 50)->default('makanan')->after('nama_produk');
            });
        }

        if (Schema::hasTable('transaksis')) {
            Schema::table('transaksis', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksis', 'kode_voucher')) {
                    $table->string('kode_voucher', 50)->nullable()->after('diskon');
                }
                if (!Schema::hasColumn('transaksis', 'uang_bayar')) {
                    $table->decimal('uang_bayar', 10, 2)->nullable()->after('total_bayar');
                }
                if (!Schema::hasColumn('transaksis', 'kembalian')) {
                    $table->decimal('kembalian', 10, 2)->nullable()->after('uang_bayar');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('produks') && Schema::hasColumn('produks', 'kategori')) {
            Schema::table('produks', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }

        if (Schema::hasTable('transaksis')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('transaksis', 'kode_voucher')) $columns[] = 'kode_voucher';
                if (Schema::hasColumn('transaksis', 'uang_bayar')) $columns[] = 'uang_bayar';
                if (Schema::hasColumn('transaksis', 'kembalian')) $columns[] = 'kembalian';
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
