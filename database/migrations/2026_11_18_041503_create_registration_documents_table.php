<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->enum('jenis_dokumen', ['ktm_ketua', 'ktm_anggota', 'proposal']);
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_documents');
    }
};
