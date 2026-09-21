<?php

namespace App\Models\BLK;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBlk extends Model
{
    use SoftDeletes;

    protected $table = 'users_blk';

    protected $guarded = [];

    public const TIPE_ALL = 0;

    public const TIPE_PROVINSI = 1;

    public const TIPE_KABKOTA = 2;

    public const TIPE_ADMIN_BLK = 3;

    public const TIPE_OFFICER = 4;

    public static function tipeAkunLabels(): array
    {
        return [
            self::TIPE_ALL => 'Admin Semua BLK',
            self::TIPE_PROVINSI => 'Admin BLK Provinsi',
            self::TIPE_KABKOTA => 'Admin BLK Kab/Kota',
            self::TIPE_ADMIN_BLK => 'Admin BLK',
            self::TIPE_OFFICER => 'Officer BLK',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blk(): BelongsTo
    {
        return $this->belongsTo(EtamBlk::class, 'blk_id', 'id');
    }
}
