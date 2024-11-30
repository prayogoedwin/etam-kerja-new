<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('etam_ak1', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); // Tenaga kerja yang mencetak
            $table->date('tanggal_cetak'); // Tanggal pencetakan AK1
            $table->date('berlaku_hingga'); // Masa berlaku AK1
            $table->enum('status_cetak', ['0', '1'])->default('0'); // 0 = Mandiri, 1 = Admin
            $table->unsignedBigInteger('dicetak_oleh')->nullable(); // Admin yang mencetak
            $table->string('qr')->nullable(); // Path QR code
            $table->string('unik_kode'); // MD5 dari id + datetime
            $table->timestamps();
            $table->softDeletes(); // Deleted_at for soft deletes

            // Relasi dengan tabel users
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dicetak_oleh')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_ak1');
    }
};
