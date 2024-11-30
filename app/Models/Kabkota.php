<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabkota extends Model
{
    protected $table = 'etam_kabkota';
    protected $fillable = ['id', 'province_id', 'name', 'kantor','alamat','telp','email','web','icon', 'created_at', 'updated_at'];

    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class, 'regency_id', 'id');
    }
}
