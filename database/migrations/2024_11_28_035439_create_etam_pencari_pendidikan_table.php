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
        Schema::create('etam_pencari_pendidikan', function (Blueprint $table) {
            $table->id(); // id, int 11, primary key auto increment
            $table->unsignedBigInteger('user_id'); // user_id, int 11
            $table->unsignedBigInteger('pendidikan_id'); // pendidikan_id, int 11
            $table->unsignedBigInteger('jurusan_id'); // jurusan_id, int 11
            $table->string('instansi', 255); // instansi, varchar 255
            $table->smallInteger('tahun'); // tahun, smallint 4
            $table->timestamps(); // created_at and updated_at columns
        });

        // Optionally, add foreign key constraints
        Schema::table('etam_pencari_pendidikan', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('pendidikan_id')->references('id')->on('pendidikans')->onDelete('cascade');
            // $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_pencari_pendidikan');
    }
};
