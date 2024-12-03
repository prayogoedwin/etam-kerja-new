<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'is_diterima',
        'medsos',
        'posted_by'
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // user_id di tabel users_pencari, id di tabel users
    }


    // public function provinsi()
    // {
    //     return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id');
    // }
    public function provinsi()
    {
        return DB::table('etam_provinsi')->where('id', $this->id_provinsi)->first();
    }
    public function kabupaten(){
        return DB::table('etam_kabkota')->where('id', $this->id_kota)->first();
    }
    public function kecamatan(){
        return DB::table('etam_kecamatan')->where('id', $this->id_kecamatan)->first();
    }
    public function desa(){
        return DB::table('etam_desa')->where('id', $this->id_desa)->first();
    }
    public function pendidikan(){
        return DB::table('etam_pendidikan')->where('id', $this->id_pendidikan)->first();
    }
    public function jurusan(){
        return DB::table('etam_jurusan')->where('id', $this->id_jurusan)->first();
    }
}
