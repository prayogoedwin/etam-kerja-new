<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamTte extends Model
{
    use SoftDeletes;

    protected $table = 'etam_tte';

    protected $guarded = [];

    protected $casts = [
        'id_refrensi' => 'integer',
        'user_id_tte' => 'integer',
        'user_id_tujuan' => 'integer',
        'status' => 'integer',
    ];

    public const STATUS_MENUNGGU = 0;

    public const STATUS_BERHASIL = 1;

    public const STATUS_GAGAL = 2;

    public static function statusLabels(): array
    {
        return [
            self::STATUS_MENUNGGU => 'Menunggu',
            self::STATUS_BERHASIL => 'Berhasil',
            self::STATUS_GAGAL => 'Gagal',
        ];
    }

    public static function statusBadgeClass(int $status): string
    {
        return match ($status) {
            self::STATUS_MENUNGGU => 'warning',
            self::STATUS_BERHASIL => 'success',
            self::STATUS_GAGAL => 'danger',
            default => 'secondary',
        };
    }

    public static function statusBadge(int $status): string
    {
        $label = self::statusLabels()[$status] ?? '-';
        $color = self::statusBadgeClass($status);

        return '<span class="badge bg-'.$color.'">'.$label.'</span>';
    }

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_tte');
    }
}
