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
        Schema::create('etam_pencari_keahlian', function (Blueprint $table) {
            $table->increments('id'); // Primary key with auto-increment
            $table->unsignedBigInteger('user_id'); // Foreign key from users table
            $table->string('keahlian')->nullable(); // Keahlian column, nullable
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at for soft deletes

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_pencari_keahlian');
    }
};
