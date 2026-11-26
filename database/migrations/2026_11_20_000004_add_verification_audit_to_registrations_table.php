<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan audit verifikasi dan perluas status yang didukung.
     */
    public function up(): void
    {
        $this->dropStatusCheckConstraint();

        Schema::table('registrations', function (Blueprint $table) {
            // String lebih portabel daripada enum ketika status bertambah
            // (misalnya 'dibatalkan' pada tahap berikutnya).
            $table->string('status_verifikasi')
                ->default('menunggu_verifikasi')
                ->change();

            $table->foreignId('verified_by')
                ->nullable()
                ->after('status_verifikasi')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')
                ->nullable()
                ->after('verified_by');
        });
    }

    /**
     * Kembalikan kolom status ke enum semula.
     *
     * Pastikan tidak ada baris berstatus 'dibatalkan' sebelum rollback.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn('verified_at');
        });

        $this->dropStatusCheckConstraint();

        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['menunggu_verifikasi', 'diterima', 'ditolak'])
                ->default('menunggu_verifikasi')
                ->change();
        });
    }

    /**
     * Lepas check constraint enum bawaan PostgreSQL sebelum kolom diubah.
     *
     * PostgreSQL menyimpan enum sebagai varchar + check constraint yang
     * tidak ikut berubah saat `change()` dijalankan.
     */
    private function dropStatusCheckConstraint(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE registrations DROP CONSTRAINT IF EXISTS registrations_status_verifikasi_check'
            );
        }
    }
};
