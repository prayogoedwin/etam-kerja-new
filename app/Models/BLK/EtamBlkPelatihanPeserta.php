<?php

namespace App\Models\BLK;

use App\Models\UserPencari;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkPelatihanPeserta extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_pelatihan_peserta';

    protected $guarded = [];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public const STATUS_MENUNGGU = 0;

    public const STATUS_SELEKSI = 1;

    public const STATUS_DITERIMA = 2;

    public const STATUS_DITOLAK = 3;

    public const STATUS_CADANGAN = 4;

    public const STATUS_LULUS = 5;

    public static function statusLabels(): array
    {
        return [
            self::STATUS_MENUNGGU => 'Menunggu',
            self::STATUS_SELEKSI => 'Proses Seleksi',
            self::STATUS_DITERIMA => 'Diterima',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_CADANGAN => 'Cadangan',
            self::STATUS_LULUS => 'Lulus',
        ];
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihan::class, 'blk_pelatihan_id', 'id');
    }

    public function pencari(): BelongsTo
    {
        return $this->belongsTo(UserPencari::class, 'pencari_id', 'id');
    }
}
