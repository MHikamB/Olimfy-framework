<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel master role.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_role')->unique();
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel master role.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
