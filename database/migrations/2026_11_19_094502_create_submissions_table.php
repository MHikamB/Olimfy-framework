<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('registration_id')->unique()->constrained('registrations')->cascadeOnDelete();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->string('judul_karya');
            $table->text('deskripsi_karya');
            $table->string('link_karya')->nullable();
            $table->string('file_karya')->nullable();
            $table->enum('status', ['dikirim', 'dinilai', 'finalis'])->default('dikirim');
            $table->unsignedSmallInteger('nilai')->nullable();
            $table->text('catatan_juri')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
