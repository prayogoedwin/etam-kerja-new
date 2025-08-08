<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sektor extends Model
{
    protected $table = 'etam_sektor';
    protected $fillable = ['id', 'kode', 'name'];


}
