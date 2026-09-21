<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $table = 'etam_agama';

    protected $fillable = ['id', 'name', 'keterangan', 'created_at', 'updated_at'];
}
