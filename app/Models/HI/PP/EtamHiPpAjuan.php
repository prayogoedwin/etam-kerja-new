<?php

namespace App\Models\HI\PP;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamHiPpAjuan extends Model
{
    use SoftDeletes;

    protected $table = 'etam_hi_pp_ajuan';
    protected $primaryKey = 'id';

    protected $guarded = []; // atau pakai $fillable sesuai kebutuhan

    protected $casts = [
        'tanggal'                 => 'date',
        'tanggal_berlaku_pp_baru' => 'date',
        'batas_revisi'            => 'date',
        'verifikasi_admin_at'     => 'datetime',
        'verifikasi_kasi_at'      => 'datetime',
        'created_at'              => 'datetime',
        'updated_at'              => 'datetime',
        'deleted_at'              => 'datetime',
    ];

    // ================= Relasi =================

    public function jenisAjuan()
    {
        return $this->belongsTo(\App\Models\HI\PP\EtamHiPpJenisajuan::class, 'jenis_ajuan', 'id');
    }

    public function syaratDokumen()
    {
        return $this->hasMany(\App\Models\HI\PP\EtamHiPpDokunggahpenyedia::class, 'ajuan_id', 'id');
    }

    public function verifikatorAdmin()
    {
        return $this->belongsTo(\App\Models\User::class, 'verifikasi_admin_by', 'id');
    }

    public function verifikatorKasi()
    {
        return $this->belongsTo(\App\Models\User::class, 'verifikasi_kasi_by', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }
}
