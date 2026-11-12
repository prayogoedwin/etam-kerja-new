<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtamStruktur extends Model
{
    public const KODE_DINAS = '99';

    protected $table = 'etam_struktur';

    protected $fillable = [
        'tipe',
        'kode_lokasi',
        'kode_bidang',
        'nama',
        'alamat',
        'slug',
    ];

    public function getTipeLabelAttribute(): string
    {
        return (int) $this->tipe === 2 ? 'Kabupaten/Kota' : 'Provinsi';
    }
}
