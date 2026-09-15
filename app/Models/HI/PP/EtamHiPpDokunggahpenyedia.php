<?php

namespace App\Models\HI\PP;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamHiPpDokunggahpenyedia extends Model
{
    use SoftDeletes; // karena tabel punya kolom deleted_at

    protected $table = 'etam_hi_pp_dokunggahpenyedia';
    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ================= Relasi =================

    /**
     * Setiap dokumen terhubung ke satu ajuan (belongsTo).
     */
    public function ajuan()
    {
        return $this->belongsTo(EtamHiPpAjuan::class, 'ajuan_id', 'id');
    }

    /**
     * Setiap dokumen mengacu ke satu syarat dokumen (belongsTo).
     */
    public function syaratDokumen()
    {
        return $this->belongsTo(EtamHiPpSyaratdokumen::class, 'syaratdokumen_id', 'id');
    }

    /**
     * User yang mengunggah dokumen.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }
}
