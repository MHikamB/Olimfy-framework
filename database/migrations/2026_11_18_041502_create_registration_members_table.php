<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->enum('peran', ['ketua', 'anggota_1', 'anggota_2']);
            $table->string('email');
            $table->string('no_hp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_members');
    }
};
