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
        Schema::create('etam_bkk_kategori', function (Blueprint $table) {
            $table->increments('id'); // Primary key auto_increment
            $table->string('name', 255); // Kolom name
            $table->timestamps(); // Kolom created_at & updated_at
            $table->softDeletes(); // Kolom deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_bkk_kategori');
    }
};
