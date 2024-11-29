<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk query builder

class LowonganPencari extends Model
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

    // Fungsi untuk insert ke tabel etam_lamaran
    public function insertLamaran($data)
    {
        return DB::table('etam_lamaran')->insert([
            'pencari_id' => $data['pencari_id'],
            'lowongan_id' => $data['lowongan_id'],
            'kabkota_penempatan_id' => $data['kabkota_penempatan_id'],
            'progres_id' => $data['progres_id'],
            'keterangan' => $data['keterangan'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function statuslamaran($userid){
        // return $this->hasMany(StatusLamaran::class, 'lowongan_id', 'id');
        return DB::table('etam_lamaran')
        ->where('lowongan_id', $this->id)
        ->where('pencari_id', $userid)
        ->first();
    }
}
