<?php

namespace App\Models\Pengawasan\Pjk3;

use App\Models\User;
use App\Models\UserPenyedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class EtamPjk3SuketKode extends Model
{
    protected $table = 'etam_pjk3_suket_kode';

    protected $guarded = [];

    protected $casts = [
        'berlaku_hingga' => 'datetime',
    ];

    public const MASA_HARI = 30;

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perusahaan_id');
    }

    public function profilPerusahaan(): BelongsTo
    {
        return $this->belongsTo(UserPenyedia::class, 'perusahaan_id', 'user_id');
    }

    public function masihBerlaku(): bool
    {
        return $this->berlaku_hingga !== null && $this->berlaku_hingga->gte(now());
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('berlaku_hingga', '>=', now());
    }

    public static function normalisasi(?string $kode): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $kode) ?? '');
    }

    public static function cariAktif(?string $kode): ?self
    {
        $nilai = self::normalisasi($kode);
        if ($nilai === '') {
            return null;
        }

        return self::query()->aktif()->where('kode', $nilai)->first();
    }

    public static function buatUntuk(int $perusahaanId): self
    {
        for ($i = 0; $i < 8; $i++) {
            $kode = 'SK3-'.strtoupper(Str::random(6));
            if (! self::query()->where('kode', $kode)->exists()) {
                return self::create([
                    'perusahaan_id' => $perusahaanId,
                    'kode' => $kode,
                    'berlaku_hingga' => now()->addDays(self::MASA_HARI),
                    'created_by' => Auth::id(),
                ]);
            }
        }

        throw new RuntimeException('Gagal membuat kode Suket K3 yang unik.');
    }
}
