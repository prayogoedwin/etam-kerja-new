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
        Schema::create('etam_infografis', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama infografis
            $table->string('path_file'); // Path file
            $table->boolean('status')->default(1); // Status aktif (1) atau tidak aktif (0)
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('etam_infografis');
    }
};
