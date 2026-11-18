<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->string('kategori');
            $table->string('jenjang');
            $table->enum('mode_pelaksanaan', ['Online', 'Offline', 'Hybrid']);
            $table->unsignedInteger('biaya_pendaftaran')->default(0);
            $table->unsignedInteger('kuota')->default(50);
            $table->unsignedInteger('kuota_terisi')->default(0);
            $table->date('deadline_pendaftaran');
            $table->text('deskripsi');
            $table->string('poster_path')->nullable();
            $table->enum('status', ['Aktif', 'Tutup'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
