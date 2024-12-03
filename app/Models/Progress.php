<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'etam_progres';
    protected $fillable = ['id','kode', 'name', 'modul'];

    protected $casts = [
        'kode' => 'integer',
    ];

}
