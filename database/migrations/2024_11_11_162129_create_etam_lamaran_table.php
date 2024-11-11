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
        Schema::create('etam_lamaran', function (Blueprint $table) {
            $table->id(); // id int 11 primary_key autoincrement
            $table->integer('pencari_id'); // pencari_id int 11
            $table->integer('lowongan_id'); // lowongan_id int 11
            $table->integer('kabkota_penempatan_id'); // kabkota_penempatan_id int 11
            $table->integer('progres_id'); // progres_id int 11
            $table->text('keterangan'); // keterangan text
            $table->timestamps(); // created_at & updated_at otomatis dibuat
            $table->softDeletes(); // deleted_at timestamp untuk soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_lamaran');
    }
};
