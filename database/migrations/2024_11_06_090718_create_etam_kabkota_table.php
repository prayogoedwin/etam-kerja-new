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
        Schema::create('etam_kabkota', function (Blueprint $table) {
            $table->char('id', 4)->primary();             // Kolom id dengan tipe char(4) sebagai primary key
            $table->char('province_id', 2);               // Kolom province_id dengan tipe char(2) sebagai foreign key
            $table->string('name', 255);                  // Kolom name dengan tipe varchar(255)
            $table->string('kantor', 255);                // Kolom kantor dengan tipe varchar(255)
            $table->string('alamat', 255);                // Kolom alamat dengan tipe varchar(255)
            $table->string('telp', 255);                  // Kolom telp dengan tipe varchar(255)
            $table->string('email', 255);                 // Kolom email dengan tipe varchar(255)
            $table->string('web', 255);                   // Kolom web dengan tipe varchar(255)
            $table->string('icon', 255);                  // Kolom icon dengan tipe varchar(255)
            $table->timestamps();                         // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_kabkota');
    }
};
