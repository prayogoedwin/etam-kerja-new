<?php

namespace App\Models\Pengawasan\K3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPengawasanK3Ajuan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_pengawasan_k3_ajuan';

    protected $fillable = [
        'penyedia_id',
        'kategori_id',
        'jenis_id',
        'nama_alat',
        'lokasi_alat',
        'kapasitas_alat',
        'jumlah_unit',
        'keterangan',
        'dok_unggah_penyedia',
        'is_kadis_dispo',
        'kadis_dispo_at',
        'is_kabid_dispo',
        'kabid_dispo_at',
        'is_kasi_dispo',
        'kasi_dispo_at',
        'deleted_by',
    ];

    protected $casts = [
        'kadis_dispo_at' => 'datetime',
        'kabid_dispo_at' => 'datetime',
        'kasi_dispo_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel users (Penyedia / Perusahaan)
     */
    public function penyedia()
    {
        return $this->belongsTo(User::class, 'penyedia_id');
    }

    /**
     * Relasi ke tabel kategori
     */
    public function kategori()
    {
        return $this->belongsTo(EtamPengawasanK3Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke tabel jenis
     */
    public function jenis()
    {
        return $this->belongsTo(EtamPengawasanK3Jenis::class, 'jenis_id');
    }

    /**
     * Relasi ke tabel SPT (One to Many / One to One)
     */
    public function spt()
    {
        return $this->hasMany(EtamPengawasanK3Spt::class, 'ajuan_id');
    }
}
