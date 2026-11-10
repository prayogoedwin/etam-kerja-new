<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtamTteDsUji extends Model
{
    protected $table = 'etam_tte_ds_uji';

    protected $guarded = [];

    protected $casts = [
        'kirim_ok' => 'boolean',
        'sign_ok' => 'boolean',
        'unduh_ok' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
