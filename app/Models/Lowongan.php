<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lowongan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_lowongan';

    protected $fillable = [
        'nama_perusahaan_bybkk',
        'jabatan_id',
        'sektor_id',
        'tanggal_start',
        'tanggal_end',
        'judul_lowongan',
        'kabkota_id',
        'lokasi_penempatan_text',
        'kisaran_gaji',
        'kisaran_gaji_akhir',
        'jumlah_pria',
        'jumlah_wanita',
        'deskripsi',
        'status_id',
        'pendidikan_id',
        'jurusan_id',
        'marital_id',
        'acc_by',
        'acc_by_role',
        'acc_at',
        'is_lowongan_bkk',
        'is_lowongan_ln',
        'is_lowongan_disabilitas',
        'tipe_lowongan',
        'nama_petugas',
        'nip_petugas',
        'kompetensi',
        'posted_by',
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke model User
    public function userPenyedia()
    {
        return $this->belongsTo(UserPenyedia::class, 'posted_by', 'user_id');
    }

    // Lowongan.php
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id');
    }

    public function kabkota()
    {
        return $this->belongsTo(Kabkota::class, 'kabkota_id', 'id');
    }
}
