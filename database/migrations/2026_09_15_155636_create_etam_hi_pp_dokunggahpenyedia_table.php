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
        Schema::create('etam_hi_pp_dokunggahpenyedia', function (Blueprint $table) {
            $table->increments('id'); // INT AUTO_INCREMENT PRIMARY KEY

            $table->integer('ajuan_id')->unsigned();
            $table->integer('syaratdokumen_id')->unsigned();
            $table->text('path_dokumen');
            $table->integer('created_by')->unsigned();

            $table->timestamps();      // created_at & updated_at (nullable)
            $table->softDeletes();     // deleted_at (nullable)

            // Foreign Key (opsional tapi disarankan)
            $table->foreign('ajuan_id')
                  ->references('id')->on('etam_hi_pp_jenisajuan')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('syaratdokumen_id')
                  ->references('id')->on('etam_hi_pp_syaratdokumen')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Index untuk mempercepat query
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_hi_pp_dokunggahpenyedia');
    }
};
