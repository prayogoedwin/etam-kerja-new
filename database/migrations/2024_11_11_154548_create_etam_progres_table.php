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
        Schema::create('etam_progres', function (Blueprint $table) {
            $table->id(); // id int 11 primary_key autoincrement
            $table->integer('kode')->nullable(); // kode int 11 nullable
            $table->string('name', 25)->nullable(); // name varchar 25 nullable
            $table->string('modul', 255)->nullable(); // modul varchar 255 nullable
            $table->timestamps(); // created_at & updated_at otomatis dibuat
            $table->softDeletes(); // deleted_at timestamp untuk soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_progres');
    }
};
