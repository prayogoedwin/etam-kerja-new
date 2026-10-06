<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_integrasi', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('client_id', 64)->unique();
            $table->string('api_key', 128)->unique();
            $table->string('access_token', 80)->nullable();
            $table->string('refresh_token', 80)->nullable();
            $table->timestamp('access_token_expires_at')->nullable();
            $table->timestamp('refresh_token_expires_at')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=aktif,0=nonaktif');
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('access_token');
            $table->index('refresh_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_integrasi');
    }
};
