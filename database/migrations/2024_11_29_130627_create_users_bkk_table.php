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
        Schema::create('users_bkk', function (Blueprint $table) {
            $table->increments('id'); // Primary key auto_increment
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->string('no_sekolah', 100)->nullable();
            $table->unsignedTinyInteger('id_sekolah'); // int(2)
            $table->string('name', 100);
            $table->string('website', 50)->nullable();
            $table->string('alamat', 255);
            $table->unsignedMediumInteger('id_provinsi'); // int(4)
            $table->unsignedMediumInteger('id_kota'); // int(4)
            $table->unsignedMediumInteger('id_kecamatan'); // int(4)
            $table->string('kodepos', 20)->nullable();
            $table->string('nama_bkk', 100)->nullable();
            $table->string('no_bkk', 100)->nullable();
            $table->date('tanggal_aktif_bkk')->nullable();
            $table->date('tanggal_non_aktif_bkk')->nullable();
            $table->string('telpon', 20)->nullable();
            $table->string('hp', 20)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->text('foto')->nullable();
            $table->timestamp('tanggal_register')->nullable();
            $table->unsignedTinyInteger('status_id'); // int(1)
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_bkk');
    }
};
