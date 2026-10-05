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
        Schema::create('etam_pengawasan_k3_spt', function (Blueprint $table) {
            $table->id(); // int 11, primary key, auto increment

            // Foreign keys
            $table->unsignedBigInteger('ajuan_id');
            $table->unsignedBigInteger('pengawas_id');

            // Detail SPT
            $table->string('nomor_spt', 255);
            $table->text('uraian_tugas');
            $table->date('tanggal_tugas');
            $table->text('lokasi');

            // Audit trails
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Definisi Constraint Foreign Key
            $table->foreign('ajuan_id')->references('id')->on('etam_pengawasan_k3_ajuan')->onDelete('cascade');
            $table->foreign('pengawas_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_pengawasan_k3_spt');
    }
};
