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
        Schema::table('etam_lowongan', function (Blueprint $table) {
            // Tambah kolom jobfair_id setelah tipe_lowongan
            $table->unsignedBigInteger('jobfair_id')
                ->nullable()
                ->after('tipe_lowongan')
                ->comment('ID Job Fair (null jika lowongan umum)');
            
            // Tambah foreign key constraint
            $table->foreign('jobfair_id')
                ->references('id')
                ->on('etam_job_fair')
                ->onUpdate('cascade')
                ->onDelete('set null');
            
            // Tambah index untuk performa query
            $table->index(['jobfair_id', 'tipe_lowongan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_lowongan', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['jobfair_id']);
            
            // Drop index
            $table->dropIndex(['jobfair_id', 'tipe_lowongan']);
            
            // Drop kolom
            $table->dropColumn('jobfair_id');
        });
    }
};