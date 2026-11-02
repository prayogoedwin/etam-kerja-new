<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_blk_pelatihan_kompetensi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blk_pelatihan_id');
            $table->string('tipe', 20);
            $table->string('nama', 500);
            $table->string('kode', 100)->default('-');
            $table->unsignedInteger('urutan')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['blk_pelatihan_id', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_blk_pelatihan_kompetensi');
    }
};
