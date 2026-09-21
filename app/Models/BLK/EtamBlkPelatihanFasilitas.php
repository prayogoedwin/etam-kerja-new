<?php

namespace App\Models\BLK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkPelatihanFasilitas extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_pelatihan_fasilitas';

    protected $guarded = [];

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihan::class, 'blk_pelatihan_id', 'id');
    }
}
