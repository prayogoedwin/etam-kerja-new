<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAdmin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_admins';

    protected $fillable = [
        'user_id',
        'province_id',
        'kabkota_id',
        'kecamatan_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted',
    ];

    protected $dates = ['deleted_at']; 

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahkan relasi atau fungsi lain jika dibutuhkan
}
