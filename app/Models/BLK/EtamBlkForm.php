<?php

namespace App\Models\BLK;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamBlkForm extends Model
{
    use SoftDeletes;

    protected $table = 'etam_blk_form';

    protected $guarded = [];

    public const JENIS_WAWANCARA = 'wawancara';

    public const JENIS_PRETEST = 'pretest';

    public static function jenisLabels(): array
    {
        return [
            self::JENIS_WAWANCARA => 'Wawancara',
            self::JENIS_PRETEST => 'Pretest',
        ];
    }

    public function blk(): BelongsTo
    {
        return $this->belongsTo(EtamBlk::class, 'blk_id', 'id');
    }

    public function pertanyaan(): HasMany
    {
        return $this->hasMany(EtamBlkFormPertanyaan::class, 'form_id', 'id')->orderBy('urutan');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function judulJenis(): string
    {
        return self::jenisLabels()[$this->jenis] ?? ucfirst((string) $this->jenis);
    }
}
