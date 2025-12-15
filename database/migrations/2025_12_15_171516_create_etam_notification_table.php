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
        Schema::create('etam_notification', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user')->nullable();
            $table->unsignedBigInteger('to_user');
            $table->string('table_target')->nullable();
            $table->unsignedBigInteger('id_target')->nullable();
            $table->string('url_redirection')->nullable();
            $table->boolean('is_open')->default(false);
            $table->boolean('is_email')->default(false);
            $table->boolean('is_whatsapp')->default(false);
            $table->text('info')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();

            // Index untuk performa query
            $table->index('to_user');
            $table->index('from_user');
            $table->index('is_open');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_notification');
    }
};