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
        Schema::create('etam_marital', function (Blueprint $table) {
            $table->char('id', 1)->primary(); // Kolom id dengan tipe char(1) sebagai primary key
            $table->string('name', 255); // Kolom name dengan tipe varchar 255
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_marital');
    }
};
