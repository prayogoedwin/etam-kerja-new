<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class UserPenyedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_penyedia';

    protected $fillable = [
        'user_id',
        'name',
        'luar_negri',
        'deskripsi',
        'jenis_perusahaan',
        'nomor_sip3mi',
        'nib',
        'id_sektor',
        'id_provinsi',
        'id_kota',
        'id_kecamatan',
        'id_desa',
        'alamat',
        'kodepos',
        'telpon',
        'jabatan',
        'website',
        'status_id',
        'foto',
        'shared_by_id',
        'posted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
