<?php

namespace App\Models\Pengawasan\K3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPengawasanK3Kategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_pengawasan_k3_kategori';

    protected $fillable = [
        'nama',
        'keterangan',
    ];

    /**
     * Relasi ke tabel jenis (One to Many)
     */
    public function jenis()
    {
        return $this->hasMany(EtamPengawasanK3Jenis::class, 'kategori_id');
    }

    /**
     * Relasi ke tabel ajuan (One to Many)
     */
    public function ajuan()
    {
        return $this->hasMany(EtamPengawasanK3Ajuan::class, 'kategori_id');
    }
}
