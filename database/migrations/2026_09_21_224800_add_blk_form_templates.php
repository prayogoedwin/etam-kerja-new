<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_blk_form', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blk_id');
            $table->string('jenis', 20);
            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['blk_id', 'jenis']);
        });

        Schema::create('etam_blk_form_pertanyaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('form_id');
            $table->unsignedInteger('urutan')->default(1);
            $table->unsignedInteger('jenis_pertanyaan')->default(1);
            $table->text('pertanyaan')->nullable();
            $table->text('pilihan')->nullable();
            $table->unsignedTinyInteger('wajib')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('form_id');
        });

        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->unsignedInteger('wawancara_form_id')->nullable()->after('template_sertifikat');
            $table->unsignedInteger('pretest_form_id')->nullable()->after('wawancara_form_id');
        });

        Schema::table('etam_blk_pelatihan_jawaban', function (Blueprint $table) {
            $table->string('jenis', 20)->nullable()->after('blk_pelatihan_id');
            $table->unsignedInteger('form_id')->nullable()->after('jenis');
            $table->unsignedInteger('form_pertanyaan_id')->nullable()->after('form_id');
            $table->unsignedInteger('perusahaan_peserta_id')->nullable()->after('blk_peserta_id');
            $table->unsignedInteger('jenis_pertanyaan')->nullable()->after('pencari_id');
            $table->unsignedInteger('urutan')->nullable()->after('jenis_pertanyaan');
            $table->unsignedTinyInteger('wajib')->default(0)->after('urutan');
            $table->timestamp('submitted_at')->nullable()->after('jawaban');

            $table->index(['blk_pelatihan_id', 'jenis', 'blk_peserta_id'], 'blk_jwbn_pelatihan_jenis_peserta');
            $table->index(['blk_pelatihan_id', 'jenis', 'perusahaan_peserta_id'], 'blk_jwbn_pelatihan_jenis_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('etam_blk_pelatihan_jawaban', function (Blueprint $table) {
            $table->dropIndex('blk_jwbn_pelatihan_jenis_peserta');
            $table->dropIndex('blk_jwbn_pelatihan_jenis_perusahaan');
            $table->dropColumn([
                'jenis',
                'form_id',
                'form_pertanyaan_id',
                'perusahaan_peserta_id',
                'jenis_pertanyaan',
                'urutan',
                'wajib',
                'submitted_at',
            ]);
        });

        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->dropColumn(['wawancara_form_id', 'pretest_form_id']);
        });

        Schema::dropIfExists('etam_blk_form_pertanyaan');
        Schema::dropIfExists('etam_blk_form');
    }
};
