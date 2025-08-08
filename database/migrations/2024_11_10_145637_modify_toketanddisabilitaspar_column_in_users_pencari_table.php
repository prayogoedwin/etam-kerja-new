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
        Schema::table('users_pencari', function (Blueprint $table) {
            $table->string('toket')->nullable()->change();
            $table->string('disabilitas')->nullable()->change();
            $table->string('jenis_disabilitas')->nullable()->change();
            $table->string('keterangan_disabilitas')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_pencari', function (Blueprint $table) {
            $table->string('toket')->nullable(false)->change();
            $table->string('disabilitas')->nullable(false)->change();
            $table->string('jenis_disabilitas')->nullable(false)->change();
            $table->string('keterangan_disabilitas')->nullable(false)->change();
        });
    }
};
