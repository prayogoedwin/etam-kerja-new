<?php

namespace App\Models\HI\PP;

use Illuminate\Database\Eloquent\Model;

class EtamHiPpJenisajuan extends Model
{
    protected $table = 'etam_hi_pp_jenisajuan';
    protected $primaryKey = 'id';

    public $timestamps = false; // tabel tidak punya created_at / updated_at

    protected $guarded = [];

    // ================= Relasi =================

    /**
     * Satu jenis ajuan bisa dipakai oleh banyak ajuan (one-to-many).
     */
    public function ajuans()
    {
        return $this->hasMany(EtamHiPpAjuan::class, 'jenis_ajuan', 'id');
    }
}
