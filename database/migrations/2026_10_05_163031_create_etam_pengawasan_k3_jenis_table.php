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
        Schema::create('etam_pengawasan_k3_jenis', function (Blueprint $table) {
            $table->id(); // int 11, primary key, auto increment

            // Foreign key ke tabel etam_pengawasan_k3_kategori
            $table->foreignId('kategori_id')
                  ->constrained('etam_pengawasan_k3_kategori')
                  ->onDelete('cascade'); // Menghapus data jenis jika kategori induknya dihapus (opsional)

            $table->string('nama', 255);
            $table->text('keterangan')->nullable();
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_pengawasan_k3_jenis');
    }
};
