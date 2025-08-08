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
        Schema::create('etam_pendidikan', function (Blueprint $table) {
            $table->increments('id'); // Kolom id dengan auto increment
            $table->string('kode', 255); // Kolom kode dengan type varchar 255
            $table->string('name', 255); // Kolom name dengan type varchar 255
            $table->integer('id_alternate')->unsigned(); // Kolom id_alternate dengan type int 11
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_pendidikan');
    }
};
