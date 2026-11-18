<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->string('kode_invoice')->unique();
            $table->unsignedInteger('nominal');
            $table->string('metode_pembayaran')->default('QRIS / Virtual Account');
            $table->enum('status_pembayaran', ['menunggu_pembayaran', 'lunas'])->default('menunggu_pembayaran');
            $table->timestamp('waktu_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
