<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfilPencari extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_pencari';

    protected $fillable = [
        'user_id',
        'ktp',
        'toket',
        'name',
        'tempat_lahir',
        'tanggal_lahir',
        'gender',
        'id_provinsi',
        'id_kota',
        'id_kecamatan',
        'id_desa',
        'alamat',
        'kodepos',
        'id_pendidikan',
        'id_jurusan',
        'tahun_lulus',
        'id_status_perkawinan',
        'id_agama',
        'foto',
        'status_id',
        'is_alumni_bkk',
        'medsos',
        'posted_by'
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // user_id di tabel users_pencari, id di tabel users
    }
}
