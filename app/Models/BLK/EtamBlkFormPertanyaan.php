<?php

namespace App\Models\BLK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkFormPertanyaan extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_form_pertanyaan';

    protected $guarded = [];

    public const JENIS_TEKS = 1;

    public const JENIS_PARAGRAF = 2;

    public const JENIS_PILIHAN = 3;

    public const JENIS_CHECKBOX = 4;

    public static function jenisPertanyaanLabels(): array
    {
        return [
            self::JENIS_TEKS => 'Jawaban singkat',
            self::JENIS_PARAGRAF => 'Paragraf',
            self::JENIS_PILIHAN => 'Pilihan ganda',
            self::JENIS_CHECKBOX => 'Kotak centang',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(EtamBlkForm::class, 'form_id', 'id');
    }

    /**
     * @return array<int, string>
     */
    public function daftarPilihan(): array
    {
        $raw = trim((string) $this->pilihan);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map('strval', $decoded), fn ($item) => trim($item) !== ''));
        }

        return array_values(array_filter(array_map('trim', explode("\n", $raw))));
    }
}
