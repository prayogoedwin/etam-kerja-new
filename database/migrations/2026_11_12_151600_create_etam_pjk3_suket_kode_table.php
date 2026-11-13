<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_pjk3_suket_kode', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perusahaan_id');
            $table->string('kode', 20)->unique();
            $table->timestamp('berlaku_hingga');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['perusahaan_id', 'berlaku_hingga']);
            $table->foreign('perusahaan_id')->references('id')->on('users');
        });

        Schema::table('etam_pjk3_suket', function (Blueprint $table) {
            $table->unsignedBigInteger('kode_id')->nullable()->after('perusahaan_id');
            $table->foreign('kode_id')->references('id')->on('etam_pjk3_suket_kode');
        });
    }

    public function down(): void
    {
        Schema::table('etam_pjk3_suket', function (Blueprint $table) {
            $table->dropForeign(['kode_id']);
            $table->dropColumn('kode_id');
        });
        Schema::dropIfExists('etam_pjk3_suket_kode');
    }
};
