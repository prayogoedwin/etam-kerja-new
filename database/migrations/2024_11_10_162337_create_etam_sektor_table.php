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
        Schema::create('etam_sektor', function (Blueprint $table) {
            $table->increments('id');  // id int 11 primary_key
            $table->string('kode', 5); // kode varchar 5
            $table->string('name', 25); // name varchar 25
            $table->timestamps(); // created_at, updated_at (otomatis menambahkan dua kolom ini)
            $table->softDeletes(); // deleted_at timestamp (untuk soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_sektor');
    }
};
