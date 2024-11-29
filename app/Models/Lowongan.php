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
        'nama_petugas',
        'nip_petugas',
        'kompetensi',
        'posted_by',
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke model User
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // Lowongan.php
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }
}
