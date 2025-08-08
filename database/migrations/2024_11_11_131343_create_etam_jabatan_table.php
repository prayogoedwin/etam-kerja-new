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
        Schema::create('etam_jabatan', function (Blueprint $table) {
            $table->id(); // id INT(11) PRIMARY KEY AUTO_INCREMENT
            $table->string('nama', 255)->nullable(); // nama VARCHAR(255) NULLABLE
            $table->timestamps(); // created_at & updated_at TIMESTAMP
            $table->softDeletes(); // deleted_at TIMESTAMP for soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_jabatan');
    }
};
