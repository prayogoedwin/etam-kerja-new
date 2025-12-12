<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamMagangDn extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'etam_magang_dn';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jenis_penyelenggara',
        'id_penyelenggara',
        'nama_magang',
        'slug',
        'penyelenggara',
        'deskripsi',
        'poster',
        'tipe_magang',
        'kota',
        'lokasi_penyelenggaraan',
        'tanggal_open_pendaftaran_tenant',
        'tipe_partnership',
        'tanggal_close_pendaftaran_tenant',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_verifikasi',
        'id_verifikator',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_open_pendaftaran_tenant' => 'date',
        'tanggal_close_pendaftaran_tenant' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jenis_penyelenggara' => 'integer',
        'tipe_magang' => 'integer',
        'tipe_partnership' => 'integer',
        'status_verifikasi' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Constants untuk jenis penyelenggara
     */
    const JENIS_PEMERINTAH = 0;
    const JENIS_SWASTA = 1;

    /**
     * Constants untuk tipe job fair
     */
    const TIPE_ONLINE = 0;
    const TIPE_OFFLINE = 1;

    /**
     * Constants untuk tipe partnership
     */
    const PARTNERSHIP_TERTUTUP = 0;
    const PARTNERSHIP_OPEN = 1;

    /**
     * Relationship ke User (penyelenggara)
     */
    public function penyelenggaraUser()
    {
        return $this->belongsTo(User::class, 'id_penyelenggara');
    }

    /**
     * Relationship ke User (verifikator)
     */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'id_verifikator');
    }

    /**
     * Relationship ke User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship ke User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship ke User (deleter)
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope untuk filter berdasarkan jenis penyelenggara
     */
    public function scopeJenisPenyelenggara($query, $jenis)
    {
        return $query->where('jenis_penyelenggara', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan tipe job fair
     */
    public function scopeTipeMagang($query, $tipe)
    {
        return $query->where('tipe_magang', $tipe);
    }

    /**
     * Scope untuk job fair yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope untuk job fair yang sudah diverifikasi
     */
    public function scopeTerverifikasi($query)
    {
        return $query->where('status_verifikasi', 1);
    }

    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'magangpemerintah_id', 'id')
                    ->where('tipe_lowongan', 4);  // Filter hanya lowongan magang pemerintah
    }

    public function lowonganAktif()
    {
        return $this->hasMany(Lowongan::class, 'magangpemerintah_id', 'id')
                    ->where('tipe_lowongan', 4)
                    ->where('progres', 1);  // Hanya lowongan aktif
    }

    // public function perusahaan()
    // {
    //     return $this->hasMany(EtamJobFairPerush::class, 'jobfair_id', 'id');
    // }

    public function getTotalLowonganAttribute()
    {
        return $this->lowongan()->count();
    }

    public function getTotalLowonganAktifAttribute()
    {
        return $this->lowonganAktif()->count();
    }

     public function getTotalPerusahaanAttribute()
    {
        return $this->perusahaan()->count();
    }
}
