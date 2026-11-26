<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel master kategori lomba.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori')->unique();
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('ikon')->default('bi-tag');
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel master kategori lomba.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
