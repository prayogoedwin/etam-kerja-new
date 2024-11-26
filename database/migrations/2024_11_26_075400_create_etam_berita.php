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
        Schema::create('etam_berita', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama infografis
            $table->string('cover')->nullable(); // Cover image (nullable, karena bisa tidak ada)
            $table->text('description')->nullable(); // Description (nullable)
            $table->integer('like_count')->default(0); // Like count, default to 0
            $table->integer('shared_count')->default(0); // Shared count, default to 0
            $table->boolean('status')->default(1); // Status field, default to 1 (Aktif)
            $table->unsignedBigInteger('phcreated_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Menambahkan kolom deleted_at untuk soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_berita');
    }
};
