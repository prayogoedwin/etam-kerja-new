<?php

namespace App\Models\Pengawasan\Pjk3;

use App\Models\User;
use App\Models\UserPenyedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPjk3Ajuan extends Model
{
    use SoftDeletes;

    protected $table = 'etam_pjk3_ajuan';

    protected $guarded = [];

    protected $casts = [
        'tanggal_ajuan' => 'date',
        'masa_berlaku' => 'date',
        'log_verifikasi' => 'array',
        'dokumen' => 'array',
    ];

    public const STATUS_MENUNGGU = 0;

    public const STATUS_ACC = 1;

    public const STATUS_REVISI = 2;

    public const STATUS_TOLAK = 3;

    public static function statusLabels(): array
    {
        return [
            self::STATUS_MENUNGGU => 'Menunggu',
            self::STATUS_ACC => 'Acc',
            self::STATUS_REVISI => 'Revisi',
            self::STATUS_TOLAK => 'Tolak',
        ];
    }

    public static function dokumenKeys(): array
    {
        return [
            'sk_pjk3' => 'Surat Keputusan Penunjukan sebagai Perusahaan Jasa K3 sesuai objek K3 dari Kemnaker RI yang masih aktif',
            'sk_ahli' => 'Surat Keputusan Penunjukan ahli sesuai objek K3 yang berada di PJK3 yang masih aktif',
            'wlkp' => 'Bukti Wajib Lapor Ketenagakerjaan di Perusahaan (WLKP)',
            'bpjs' => 'Bukti Setor BPJS bulan terakhir',
        ];
    }

    public function penyedia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyedia_id');
    }

    public function profilPenyedia(): BelongsTo
    {
        return $this->belongsTo(UserPenyedia::class, 'penyedia_id', 'user_id');
    }

    public function isExpired(): bool
    {
        if ((int) $this->status_verifikasi !== self::STATUS_ACC || ! $this->masa_berlaku) {
            return false;
        }

        return $this->masa_berlaku->lt(now()->startOfDay());
    }

    public function statusLabel(): string
    {
        if ($this->isExpired()) {
            return 'Masa berlaku habis';
        }

        return self::statusLabels()[(int) $this->status_verifikasi] ?? '-';
    }

    public static function logStatusLabel(?string $status): string
    {
        return match ($status) {
            'acc' => 'ACC',
            'revisi' => 'REVISI',
            'tolak' => 'TOLAK',
            'ajuan_ulang' => 'AJUAN ULANG',
            default => strtoupper((string) $status) ?: '-',
        };
    }
}
