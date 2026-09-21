<?php

namespace App\Models\BLK;

use App\Models\Kabkota;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlk extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk';

    protected $guarded = [];

    public const TIPE_PROVINSI = 1;

    public const TIPE_KABKOTA = 2;

    public static function tipeLembagaLabels(): array
    {
        return [
            self::TIPE_PROVINSI => 'Provinsi',
            self::TIPE_KABKOTA => 'Kabupaten/Kota',
        ];
    }

    public function kabkota(): BelongsTo
    {
        return $this->belongsTo(Kabkota::class, 'kabkota_id', 'id');
    }

    public function usersBlk(): HasMany
    {
        return $this->hasMany(UserBlk::class, 'blk_id', 'id');
    }

    public function pelatihans(): HasMany
    {
        return $this->hasMany(EtamBlkPelatihan::class, 'blk_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
