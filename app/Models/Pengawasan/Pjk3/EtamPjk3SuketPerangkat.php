<?php

namespace App\Models\Pengawasan\Pjk3;

use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EtamPjk3SuketPerangkat extends Model
{
    protected $table = 'etam_pjk3_suket_perangkat';

    protected $guarded = [];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function suket(): BelongsTo
    {
        return $this->belongsTo(EtamPjk3Suket::class, 'suket_id');
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(EtamPengawasanK3Jenis::class, 'jenis_id');
    }

    public function uji(): HasMany
    {
        return $this->hasMany(EtamPjk3SuketUji::class, 'perangkat_id')->orderBy('urutan')->orderBy('id');
    }
}
