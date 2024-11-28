<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lamaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_lamaran';

    protected $fillable = [
        'pencari_id',
        'lowongan_id',
        'kabkota_penempatan_id',
        'progres_id',
        'created_at',
        'updated_at',
        'keterangan'
    ];

    protected $dates = ['deleted_at'];
}
