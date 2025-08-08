<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EtamPencariPendidikan extends Model
{
    use HasFactory;

    protected $table = 'etam_pencari_pendidikan';

    protected $fillable = [
        'user_id',
        'pendidikan_id',
        'jurusan_id',
        'instansi',
        'tahun',
        'created_at',
        'updated_at'
    ];

     // Definisikan relasi ke model User
     public function user()
     {
         return $this->belongsTo(User::class, 'user_id', 'id');
     }

     // Definisikan relasi ke model Pendidikan
     public function pendidikan()
     {
         return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
     }

     // Definisikan relasi ke model Jurusan
     public function jurusan()
     {
         return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id');
     }
}
