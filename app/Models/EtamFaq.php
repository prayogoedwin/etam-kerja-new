<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamFaq extends Model
{
    use SoftDeletes;

    protected $table = 'etam_faqs';

    protected $fillable = ['name', 'description', 'created_by', 'updated_by', 'deleted_by', 'id_deleted'];

    // Jika Anda ingin mengubah nama kolom default `deleted_at`
    // protected $dates = ['deleted_at'];
}
