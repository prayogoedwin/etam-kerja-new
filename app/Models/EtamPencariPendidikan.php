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
}
