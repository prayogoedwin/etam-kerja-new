<?php

namespace App\Models\Pengawasan\Pjk3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtamPjk3SuketUji extends Model
{
    protected $table = 'etam_pjk3_suket_uji';

    protected $guarded = [];

    public const STATUS_MEMENUHI = 'memenuhi';

    public const STATUS_TIDAK_MEMENUHI = 'tidak_memenuhi';

    protected $casts = [
        'urutan' => 'integer',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_MEMENUHI => 'Memenuhi',
            self::STATUS_TIDAK_MEMENUHI => 'Tidak memenuhi',
        ];
    }

    public function suket(): BelongsTo
    {
        return $this->belongsTo(EtamPjk3Suket::class, 'suket_id');
    }

    public function perangkat(): BelongsTo
    {
        return $this->belongsTo(EtamPjk3SuketPerangkat::class, 'perangkat_id');
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[(string) $this->status] ?? '-';
    }
}
