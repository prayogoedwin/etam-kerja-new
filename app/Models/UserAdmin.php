<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

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
    ];

    protected $dates = ['deleted_at']; 

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kabkota()
    {
        return $this->belongsTo(Kabkota::class, 'kabkota_id', 'id');
    }
    


    // Tambahkan relasi atau fungsi lain jika dibutuhkan
}
