<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtamTteLog extends Model
{
    protected $table = 'etam_tte_log';

    protected $guarded = [];

    protected $casts = [
        'etam_tte_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
    ];

    public const STATUS_GAGAL = 0;

    public const STATUS_BERHASIL = 1;

    public static function statusBadge(int $status): string
    {
        if ($status === self::STATUS_BERHASIL) {
            return '<span class="badge bg-success">Berhasil</span>';
        }

        return '<span class="badge bg-danger">Gagal</span>';
    }

    public function tte(): BelongsTo
    {
        return $this->belongsTo(EtamTte::class, 'etam_tte_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
