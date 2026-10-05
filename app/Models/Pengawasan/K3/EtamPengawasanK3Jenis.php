<?php

namespace App\Models\Pengawasan\K3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPengawasanK3Jenis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_pengawasan_k3_jenis';

    protected $fillable = [
        'kategori_id',
        'nama',
        'keterangan',
    ];

    /**
     * Relasi ke tabel kategori (Belongs To)
     */
    public function kategori()
    {
        return $this->belongsTo(EtamPengawasanK3Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke tabel ajuan (One to Many)
     */
    public function ajuan()
    {
        return $this->hasMany(EtamPengawasanK3Ajuan::class, 'jenis_id');
    }
}
