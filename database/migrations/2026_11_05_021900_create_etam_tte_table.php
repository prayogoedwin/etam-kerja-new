<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_tte', function (Blueprint $table) {
            $table->id();
            $table->string('nama_modul', 50);
            $table->string('nama_table', 100);
            $table->unsignedBigInteger('id_refrensi');
            $table->string('kolom_status_refrensi', 64);
            $table->string('id_struktur', 20)->nullable();
            $table->unsignedBigInteger('user_id_tte')->nullable();
            $table->string('judul', 255)->nullable();
            $table->string('path_file_before', 500)->nullable();
            $table->string('path_file_after', 500)->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'id_struktur']);
            $table->index(['nama_modul', 'nama_table', 'id_refrensi'], 'etam_tte_sumber_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_tte');
    }
};
