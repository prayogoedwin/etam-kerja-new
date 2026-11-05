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
        Schema::create('etam_hi_pp_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('ajuan_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('role', 50)->nullable();
            $table->string('action', 100);
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('created_at')->nullable();

            // FK ke ajuan (kalau ajuan dihapus, log tetap disimpan)
            $table->index('ajuan_id');
            $table->index('action');
            $table->index(['ajuan_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_hi_pp_logs');
    }
};
