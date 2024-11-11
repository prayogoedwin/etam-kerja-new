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
            $table->unsignedBigInteger('acc_by')->nullable()->change();
            $table->string('acc_by_role', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_lowongan', function (Blueprint $table) {
            $table->unsignedBigInteger('acc_by')->nullable(false)->change();
            $table->string('acc_by_role', 50)->nullable(false)->change();
        });
    }
};
