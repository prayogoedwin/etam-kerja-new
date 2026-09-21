<?php

namespace App\Models\BLK;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EtamBlkPelatihan extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_pelatihan';

    protected $guarded = [];

    protected $casts = [
        'tanggal_pendaftaran' => 'date',
        'tanggal_pendaftaran_selesai' => 'date',
        'tanggal_pelaksanaan' => 'date',
        'tanggal_pelaksanaan_selesai' => 'date',
    ];

    public const UNTUK_PENCARI = 0;

    public const UNTUK_PENYEDIA = 1;

    public const STATUS_DRAFT = 0;

    public const STATUS_AKTIF = 1;

    public const STATUS_TUTUP = 2;

    public const STATUS_SELESAI = 3;

    public static function untukLabels(): array
    {
        return [
            self::UNTUK_PENCARI => 'Pencari Kerja',
            self::UNTUK_PENYEDIA => 'Pemberi Kerja',
        ];
    }

    public static function pembiayaanLabels(): array
    {
        return [
            0 => 'APBD',
            1 => 'DBHCHT',
            2 => 'APBN',
        ];
    }

    public static function tipeLabels(): array
    {
        return [
            0 => 'Offline',
            1 => 'Online',
            2 => 'Hybrid',
            3 => 'MTU',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_TUTUP => 'Tutup',
            self::STATUS_SELESAI => 'Selesai',
        ];
    }

    public function blk(): BelongsTo
    {
        return $this->belongsTo(EtamBlk::class, 'blk_id', 'id');
    }

    public function fasilitas(): HasMany
    {
        return $this->hasMany(EtamBlkPelatihanFasilitas::class, 'blk_pelatihan_id', 'id');
    }

    public function syarat(): HasMany
    {
        return $this->hasMany(EtamBlkPelatihanSyarat::class, 'blk_pelatihan_id', 'id');
    }

    public function pertanyaan(): HasMany
    {
        return $this->hasMany(EtamBlkPelatihanPertanyaan::class, 'blk_pelatihan_id', 'id');
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(EtamBlkPelatihanPeserta::class, 'blk_pelatihan_id', 'id');
    }

    public function pesertaPerusahaan(): HasMany
    {
        return $this->hasMany(EtamBlkPelatihanPesertaPerusahaan::class, 'blk_pelatihan_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function generateSlug(): string
    {
        return Str::slug((string) $this->nama_pelatihan).'-'.time();
    }

    public function isOpenForRegistration(): bool
    {
        if ((int) $this->status !== self::STATUS_AKTIF) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->tanggal_pendaftaran && $today < $this->tanggal_pendaftaran->toDateString()) {
            return false;
        }

        if ($this->tanggal_pendaftaran_selesai && $today > $this->tanggal_pendaftaran_selesai->toDateString()) {
            return false;
        }

        return true;
    }
}
