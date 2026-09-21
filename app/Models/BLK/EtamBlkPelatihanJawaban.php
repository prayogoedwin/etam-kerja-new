<?php

namespace App\Models\BLK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkPelatihanJawaban extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_pelatihan_jawaban';

    protected $guarded = [];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihan::class, 'blk_pelatihan_id', 'id');
    }

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihanPertanyaan::class, 'blk_pertanyaan_id', 'id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihanPeserta::class, 'blk_peserta_id', 'id');
    }

    public function pesertaPerusahaan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkPelatihanPesertaPerusahaan::class, 'perusahaan_peserta_id', 'id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(EtamBlkForm::class, 'form_id', 'id');
    }

    public function formPertanyaan(): BelongsTo
    {
        return $this->belongsTo(EtamBlkFormPertanyaan::class, 'form_pertanyaan_id', 'id');
    }
}
