<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel detail_transaksis
 * Mendukung relasi 1:M antara Transaksi dan Detail Transaksi
 * sesuai panduan MUK UKK 2026.
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
        if (Schema::hasTable('detail_transaksis')) {
            Schema::dropIfExists('detail_transaksis');
        }

        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id();
            $table->integer('transaksi_id')->index();
            $table->integer('produk_id')->nullable()->index();
            $table->string('nama_produk', 100);
            $table->decimal('harga', 10, 2);
            $table->integer('qty');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
