<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('etam_tte_ds_uji');
    }

    public function down(): void
    {
        // Tabel uji DS tidak dipakai lagi.
    }
};
