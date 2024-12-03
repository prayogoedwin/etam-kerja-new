<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 'etam_provinsi';
    protected $fillable = ['id', 'name'];



    public function kabkota()
    {
        return $this->hasMany(Kabkota::class, 'province_id', 'id');
    }
}
