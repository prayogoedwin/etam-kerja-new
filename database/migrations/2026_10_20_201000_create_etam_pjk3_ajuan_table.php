<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_pjk3_ajuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penyedia_id');
            $table->date('tanggal_ajuan');
            $table->tinyInteger('status_verifikasi')->default(0)->comment('0 menunggu, 1 acc, 2 revisi, 3 tolak');
            $table->text('alasan')->nullable();
            $table->json('log_verifikasi')->nullable();
            $table->date('masa_berlaku')->nullable();
            $table->json('dokumen')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('penyedia_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_pjk3_ajuan');
    }
};
