<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan relasi role_id ke tabel users.
     *
     * Kolom enum `role` sengaja dipertahankan agar kode lama yang membaca
     * $user->role tetap berjalan selama masa transisi. Kedua kolom
     * disinkronkan melalui event `saving` pada model User.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->nullOnDelete();
        });
    }

    /**
     * Hapus relasi role_id dari tabel users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
