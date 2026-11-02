<?php

namespace App\Models\BLK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkPelatihanKompetensi extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_pelatihan_kompetensi';

    protected $guarded = [];

    public const TIPE_INTI = 'inti';

    public const TIPE_PENUNJANG = 'penunjang';

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihan::class, 'blk_pelatihan_id', 'id');
    }

    public function kodeLabel(): string
    {
        $kode = trim((string) $this->kode);

        return $kode === '' ? '-' : $kode;
    }
}
