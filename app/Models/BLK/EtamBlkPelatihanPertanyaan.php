<?php

namespace App\Models\BLK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkPelatihanPertanyaan extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_pelatihan_pertanyaan';

    protected $guarded = [];

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihan::class, 'blk_pelatihan_id', 'id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(EtamBlkPelatihanJawaban::class, 'blk_pertanyaan_id', 'id');
    }
}
