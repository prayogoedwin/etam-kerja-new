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
        Schema::create('etam_agama', function (Blueprint $table) {
            $table->increments('id'); // id int 11 auto_increment
            $table->string('name', 255); // name varchar 255
            $table->string('keterangan', 255)->nullable(); // keterangan varchar 255 (nullable)
            $table->timestamp('created_at')->useCurrent(); // created_at timestamp
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')); // updated_at timestamp on update current
            $table->softDeletes(); // deleted_at timestamp (soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_agama');
    }
};
