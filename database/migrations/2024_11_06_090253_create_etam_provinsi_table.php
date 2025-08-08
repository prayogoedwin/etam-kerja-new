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
        Schema::create('etam_provinsi', function (Blueprint $table) {
            $table->char('id', 2)->primary(); // Kolom id dengan tipe char(2) sebagai primary key
            $table->string('name', 255);      // Kolom name dengan tipe varchar(255)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_provinsi');
    }
};
