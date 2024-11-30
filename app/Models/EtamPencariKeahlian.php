<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPencariKeahlian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_pencari_keahlian';

    protected $fillable = [
        'user_id',
        'keahlian',
        'created_at',
        'updated_at'
    ];

    protected $dates = ['deleted_at'];
}
