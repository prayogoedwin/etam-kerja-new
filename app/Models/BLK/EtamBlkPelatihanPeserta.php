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
        'status_ttd' => 'integer',
        'ttd_uptd_at' => 'datetime',
        'ttd_dinas_at' => 'datetime',
    ];

    public const STATUS_MENUNGGU = 0;

    public const STATUS_SELEKSI = 1;

    public const STATUS_DITERIMA = 2;

    public const STATUS_DITOLAK = 3;

    public const STATUS_CADANGAN = 4;

    public const STATUS_LULUS = 5;

    public const TTD_MENUNGGU = 0;

    public const TTD_DIAJUKAN_UPTD = 1;

    public const TTD_TTD_UPTD = 2;

    public const TTD_TTD_DINAS = 3;

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

    public static function statusTtdLabels(): array
    {
        return [
            self::TTD_MENUNGGU => 'Menunggu',
            self::TTD_DIAJUKAN_UPTD => 'Diajukan ke Kepala UPTD',
            self::TTD_TTD_UPTD => 'Sudah TTD Kepala UPTD, diajukan ke Kepala Dinas',
            self::TTD_TTD_DINAS => 'Sudah ditandatangani Kepala Dinas',
        ];
    }

    public static function statusTtdBadgeClass(int $status): string
    {
        return match ($status) {
            self::TTD_MENUNGGU => 'warning',
            self::TTD_DIAJUKAN_UPTD => 'info',
            self::TTD_TTD_UPTD => 'primary',
            self::TTD_TTD_DINAS => 'success',
            default => 'secondary',
        };
    }

    public static function statusTtdBadge(int $status): string
    {
        $label = self::statusTtdLabels()[$status] ?? '-';
        $color = self::statusTtdBadgeClass($status);

        return '<span class="badge bg-'.$color.'">'.$label.'</span>';
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
