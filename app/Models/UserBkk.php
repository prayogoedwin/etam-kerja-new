<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class UserBkk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_bkk';

    protected $fillable = [
        'user_id',
        'no_sekolah',
        'id_sekolah',
        'name',
        'website',
        'alamat',
        'id_provinsi',
        'id_kota',
        'id_kecamatan',
        'kodepos',
        'nama_bkk',
        'no_bkk',
        'tanggal_aktif_bkk',
        'tanggal_non_aktif_bkk',
        'telpon',
        'hp',
        'contact_person',
        'jabatan',
        'foto',
        'tanggal_register',
        'status_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
