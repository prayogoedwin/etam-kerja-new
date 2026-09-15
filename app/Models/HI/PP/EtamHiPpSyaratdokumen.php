<?php

namespace App\Models\HI\PP;

use Illuminate\Database\Eloquent\Model;

class EtamHiPpSyaratdokumen extends Model
{
    protected $table = 'etam_hi_pp_syaratdokumen';
    protected $primaryKey = 'id';

    public $timestamps = false; // tidak ada created_at/updated_at di migrasinya

    protected $guarded = [];

    // ================= Relasi =================

    public function dokumenUnggahan()
    {
        return $this->hasMany(EtamHiPpDokunggahpenyedia::class, 'syaratdokumen_id', 'id');
    }
}
