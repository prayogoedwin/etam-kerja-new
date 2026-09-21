<?php

namespace App\Models\BLK;

use App\Models\UserPenyedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkPelatihanPesertaPerusahaan extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_pelatihan_peserta_perusahaan';

    protected $guarded = [];

    public static function statusLabels(): array
    {
        return EtamBlkPelatihanPeserta::statusLabels();
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihan::class, 'blk_pelatihan_id', 'id');
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(UserPenyedia::class, 'perusahaan_id', 'id');
    }
}
