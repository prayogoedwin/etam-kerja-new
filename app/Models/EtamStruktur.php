<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtamStruktur extends Model
{
    protected $table = 'etam_struktur';

    protected $fillable = [
        'tipe',
        'kode_lokasi',
        'kode_bidang',
        'nama',
        'slug',
    ];

    public function getTipeLabelAttribute(): string
    {
        return (int) $this->tipe === 2 ? 'Kabupaten/Kota' : 'Provinsi';
    }
}
