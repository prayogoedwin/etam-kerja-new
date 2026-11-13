<?php

namespace App\Models\Pengawasan\Pjk3;

use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\User;
use App\Models\UserPenyedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamPjk3Suket extends Model
{
    use SoftDeletes;

    protected $table = 'etam_pjk3_suket';

    protected $guarded = [];

    protected $casts = [
        'dokumen' => 'array',
        'lhpp' => 'array',
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

    public const TTE_DOK_LHPP = 'lhpp';

    public const TTE_DOK_SUKET = 'suket';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DIAJUKAN => 'Diajukan',
            self::STATUS_DISPO => 'Diproses',
            self::STATUS_DRAFT => 'Diproses',
            self::STATUS_TTD_PENGAWAS => 'Diproses',
            self::STATUS_TTD_KADIS => 'Selesai',
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

    public function statusLabel(bool $admin = false): string
    {
        if ($admin) {
            $data = $this->dataTimeline(true);

            return $data['steps'][$data['current']]['label_full'] ?? '-';
        }

        return self::statusLabels()[(int) $this->status] ?? '-';
    }

    public function statusBadgeColor(): string
    {
        return match ((int) $this->status) {
            self::STATUS_DIAJUKAN => 'warning',
            self::STATUS_TTD_KADIS => 'success',
            default => 'info',
        };
    }

    /**
     * @return array{current: int, selesai: bool, steps: list<array{label: string, label_full: string, hint: string}>}
     */
    public function dataTimeline(bool $admin = false): array
    {
        if ($admin) {
            $status = (int) $this->status;

            return [
                'current' => min($status, self::STATUS_TTD_KADIS),
                'selesai' => $status >= self::STATUS_TTD_KADIS,
                'steps' => [
                    ['label' => 'Diajukan', 'label_full' => 'Diajukan', 'hint' => 'Menunggu disposisi'],
                    ['label' => 'Disposisi', 'label_full' => 'Disposisi Kasi', 'hint' => 'Diteruskan ke pengawas'],
                    ['label' => 'Periksa', 'label_full' => 'Pemeriksaan', 'hint' => 'Pengawas mengisi LHPP'],
                    ['label' => 'TTD Pws', 'label_full' => 'TTD Pengawas', 'hint' => 'LHPP dan Suket ditandatangani pengawas'],
                    ['label' => 'TTD Kadis', 'label_full' => 'TTD Kadis', 'hint' => 'Suket terbit'],
                ],
            ];
        }

        $status = (int) $this->status;
        $current = match (true) {
            $status >= self::STATUS_TTD_KADIS => 2,
            $status >= self::STATUS_DISPO => 1,
            default => 0,
        };

        return [
            'current' => $current,
            'selesai' => $status >= self::STATUS_TTD_KADIS,
            'steps' => [
                ['label' => 'Diajukan', 'label_full' => 'Diajukan', 'hint' => 'Pengajuan masuk'],
                ['label' => 'Diproses', 'label_full' => 'Diproses', 'hint' => 'Sedang diperiksa dan ditandatangani'],
                ['label' => 'Selesai', 'label_full' => 'Selesai', 'hint' => 'Suket sudah terbit'],
            ],
        ];
    }

    public function isDraft(): bool
    {
        return (int) $this->status < self::STATUS_TTD_KADIS;
    }

    public function nilaiLhpp(string $key, mixed $default = ''): mixed
    {
        return data_get($this->lhpp, $key, $default);
    }

    public function namaBidangAhliK3(): string
    {
        $nama = $this->ahliK3Kategori?->nama
            ?: $this->kategori?->nama
            ?: $this->nilaiLhpp('ahli_k3_bidang');

        return is_string($nama) && $nama !== '' ? $nama : '-';
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function sinkronkanPerangkatDanUji(array $rows): void
    {
        $this->uji()->delete();
        $this->perangkat()->delete();

        foreach (array_values($rows) as $i => $row) {
            $jenisId = (int) ($row['jenis_id'] ?? 0);
            if ($jenisId < 1) {
                continue;
            }

            $nama = trim((string) ($row['nama'] ?? ''));
            if ($nama === '') {
                $nama = (string) EtamPengawasanK3Jenis::query()->whereKey($jenisId)->value('nama');
            }
            if ($nama === '') {
                continue;
            }

            $perangkat = $this->perangkat()->create([
                'jenis_id' => $jenisId,
                'nama' => $nama,
                'merk' => trim((string) ($row['merk'] ?? '')) ?: null,
                'jumlah' => trim((string) ($row['jumlah'] ?? '')) ?: null,
                'lokasi' => trim((string) ($row['lokasi'] ?? '')) ?: null,
                'urutan' => $i + 1,
            ]);

            foreach (array_values((array) ($row['uji'] ?? [])) as $j => $uji) {
                if (! is_array($uji)) {
                    continue;
                }
                $item = trim((string) ($uji['item'] ?? ''));
                $status = trim((string) ($uji['status'] ?? ''));
                $hasil = trim((string) ($uji['hasil'] ?? ''));
                if ($item === '' && $status === '' && $hasil === '') {
                    continue;
                }

                $perangkat->uji()->create([
                    'suket_id' => $this->id,
                    'item' => $item !== '' ? $item : '-',
                    'status' => in_array($status, [
                        EtamPjk3SuketUji::STATUS_MEMENUHI,
                        EtamPjk3SuketUji::STATUS_TIDAK_MEMENUHI,
                    ], true) ? $status : null,
                    'hasil' => $hasil !== '' ? $hasil : null,
                    'urutan' => $j + 1,
                ]);
            }
        }
    }

    public function pjk3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pjk3_penyedia_id');
    }

    public function profilPjk3(): BelongsTo
    {
        return $this->belongsTo(UserPenyedia::class, 'pjk3_penyedia_id', 'user_id');
    }

    public function kodeAkses(): BelongsTo
    {
        return $this->belongsTo(EtamPjk3SuketKode::class, 'kode_id');
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

    public function ahliK3Kategori(): BelongsTo
    {
        return $this->belongsTo(EtamPengawasanK3Kategori::class, 'ahli_k3_kategori_id');
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(EtamPengawasanK3Jenis::class, 'jenis_id');
    }

    public function perangkat(): HasMany
    {
        return $this->hasMany(EtamPjk3SuketPerangkat::class, 'suket_id')->orderBy('urutan')->orderBy('id');
    }

    public function uji(): HasMany
    {
        return $this->hasMany(EtamPjk3SuketUji::class, 'suket_id')->orderBy('urutan')->orderBy('id');
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
