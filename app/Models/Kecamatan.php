<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'etam_kecamatan';
    protected $fillable = ['id', 'regency_id', 'name'];

    public function kabkota()
    {
        return $this->belongsTo(Kabkota::class, 'regency_id', 'id');
    }
    
}

