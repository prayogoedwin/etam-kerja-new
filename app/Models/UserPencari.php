<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class UserPencari extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_pencari';

    protected $fillable = [
        'user_id',
        'ktp',
        'name',
        'tempat_lahir',
        'tanggal_lahir',
        'gender',
        'id_provinsi',
        'id_kota',
        'id_kecamatan',
        // 'id_desa',
        'alamat',
        'kodepos',
        'id_pendidikan',
        'id_jurusan',
        'tahun_lulus',
        'id_status_perkawinan',
        'id_agama',
        'id_jabatan_harapan',
        'foto',
        'status_id',
        'is_alumni_bkk',
        'bkk_id',
        'toket',
        'disabilitas',
        'jenis_disabilitas',
        'keterangan_disabilitas',
        'posted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_diterima',
        'medsos'
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id');
    }

    public function kabkota()
    {
        return $this->belongsTo(Kabkota::class, 'id_kota', 'id');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'id_pendidikan', 'id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id');
    }

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'id_agama', 'id');
    }
}
