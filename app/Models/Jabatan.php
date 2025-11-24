<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_jabatan';

    protected $fillable = [
        'nama',
    ];

    protected $dates = ['deleted_at'];

    // Relasi ke Lowongan (one to many)
    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'jabatan_id', 'id');
    }

    // Scope untuk jabatan aktif (tidak dihapus)
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Scope untuk ordering by nama
    public function scopeOrdered($query)
    {
        return $query->orderBy('nama', 'asc');
    }
}