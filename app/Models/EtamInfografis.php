<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamInfografis extends Model
{
    use SoftDeletes;

    protected $table = 'etam_infografis';

    protected $fillable = [
        'name',
        'path_file',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
