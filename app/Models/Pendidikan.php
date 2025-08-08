<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    protected $table = 'etam_pendidikan';
    protected $fillable = ['id', 'kode', 'name'];

}
