<?php

namespace App\Models\Pengawasan\Pjk3;

use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\User;
use App\Models\UserPenyedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPjk3Suket extends Model
{
    use SoftDeletes;

    protected $table = 'etam_pjk3_suket';

    protected $guarded = [];

    protected $casts = [
        'dokumen' => 'array',
        'tanggal_pemeriksaan' => 'date',
        'ttd_pengawas_at' => 'datetime',
        'ttd_kadis_at' => 'datetime',
        'is_layak' => 'integer',
        'status' => 'integer',
    ];

    public const STATUS_DIAJUKAN = 0;

    public const STATUS_DISPO = 1;

    public const STATUS_DRAFT = 2;

    public const STATUS_TTD_PENGAWAS = 3;

    public const STATUS_TTD_KADIS = 4;

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DIAJUKAN => 'Menunggu disposisi Kasi',
            self::STATUS_DISPO => 'Disposisi ke Pengawas',
            self::STATUS_DRAFT => 'Draft LHPP / Suket',
            self::STATUS_TTD_PENGAWAS => 'TTD Pengawas, diajukan Kepala Dinas',
            self::STATUS_TTD_KADIS => 'Sudah TTD Kepala Dinas',
        ];
    }

    public static function dokumenKeys(): array
    {
        return [
            'surat_permohonan' => 'Surat permohonan',
            'lhpp' => 'Laporan hasil pemeriksaan dan pengujian (LHPP)',
            'skp_pjk3' => 'SKP PJK3 yang diterbitkan Kemnaker / instansi berwenang',
            'ahli_k3' => 'SKP Ahli K3 yang diterbitkan Kemnaker / instansi berwenang',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[(int) $this->status] ?? '-';
    }

    public function isDraft(): bool
    {
        return (int) $this->status < self::STATUS_TTD_KADIS;
    }

    public function pjk3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pjk3_penyedia_id');
    }

    public function profilPjk3(): BelongsTo
    {
        return $this->belongsTo(UserPenyedia::class, 'pjk3_penyedia_id', 'user_id');
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perusahaan_id');
    }

    public function profilPerusahaan(): BelongsTo
    {
        return $this->belongsTo(UserPenyedia::class, 'perusahaan_id', 'user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(EtamPengawasanK3Kategori::class, 'kategori_id');
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(EtamPengawasanK3Jenis::class, 'jenis_id');
    }

    public function pengawas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function kasi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasi_id');
    }
}
