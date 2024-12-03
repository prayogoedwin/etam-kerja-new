<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lamaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_lamaran';

    protected $fillable = [
        'pencari_id',
        'lowongan_id',
        'kabkota_penempatan_id',
        'progres_id',
        'created_at',
        'updated_at',
        'keterangan'
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'pencari_id', 'id');
    }

    // Relasi ke model ProfilPencari
    public function profilPencari()
    {
        return $this->hasOne(ProfilPencari::class, 'user_id', 'pencari_id');
    }

    // Relasi ke model Lowongan
    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id', 'id');
    }

    // relasi ke model ProfilPenyedia
    public function penyedia()
    {
        return $this->hasOneThrough(
            ProfilPenyedia::class, // Model tujuan
            Lowongan::class,       // Model perantara
            'id',                  // Foreign key di tabel lowongan (referensi ke tabel lamaran)
            'user_id',             // Foreign key di tabel profil_penyedia
            'lowongan_id',         // Local key di tabel lamaran
            'posted_by'            // Local key di tabel lowongan
        );
    }
}
