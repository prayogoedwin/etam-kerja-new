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
        Schema::create('etam_jurusan', function (Blueprint $table) {
            $table->increments('id'); // Kolom id dengan auto increment
            $table->string('nama', 255); // Kolom nama dengan type varchar 255
            $table->string('id_pendidikans', 255); // Kolom id_pendidikans dengan type varchar 255
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_jurusan');
    }
};
