<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_tte_ds_uji', function (Blueprint $table) {
            $table->longText('kirim_body')->nullable()->after('kirim_pesan');
            $table->longText('sign_body')->nullable()->after('sign_pesan');
            $table->longText('unduh_body')->nullable()->after('unduh_pesan');
        });
    }

    public function down(): void
    {
        Schema::table('etam_tte_ds_uji', function (Blueprint $table) {
            $table->dropColumn(['kirim_body', 'sign_body', 'unduh_body']);
        });
    }
};
