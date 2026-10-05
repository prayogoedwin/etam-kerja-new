<?php

namespace App\Models\Pengawasan\K3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPengawasanK3Spt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_pengawasan_k3_spt';

    protected $fillable = [
        'ajuan_id',
        'pengawas_id',
        'nomor_spt',
        'uraian_tugas',
        'tanggal_tugas',
        'lokasi',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'tanggal_tugas' => 'date',
    ];

    /**
     * Relasi ke tabel ajuan
     */
    public function ajuan()
    {
        return $this->belongsTo(EtamPengawasanK3Ajuan::class, 'ajuan_id');
    }

    /**
     * Relasi ke tabel users (Pengawas Ketenagakerjaan)
     */
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }
}
