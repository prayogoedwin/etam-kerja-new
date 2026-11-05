<?php

namespace App\Models\HI\PP;

use Illuminate\Database\Eloquent\Model;

class EtamHiPpLog extends Model
{
    protected $table = 'etam_hi_pp_logs';
    protected $primaryKey = 'id';

    // Log immutable — hanya created_at, tanpa updated_at
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'meta'       => 'array',
        'created_at' => 'datetime',
    ];

    // ================= Konstanta Aksi =================
    public const ACTION_SUBMIT_AJUAN        = 'submit_ajuan';
    public const ACTION_UPDATE_AJUAN        = 'update_ajuan';
    public const ACTION_SUBMIT_REVISI       = 'submit_revisi';
    public const ACTION_ADMIN_ACC           = 'admin_acc';
    public const ACTION_ADMIN_REVISI        = 'admin_revisi';
    public const ACTION_KASI_ACC            = 'kasi_acc';
    public const ACTION_KASI_REVISI         = 'kasi_revisi';
    public const ACTION_INPUT_SK            = 'input_sk';
    public const ACTION_CETAK_SK            = 'cetak_sk';
    public const ACTION_UPLOAD_SK_FINAL     = 'upload_sk_final';
    public const ACTION_UPLOAD_FINAL_PP     = 'upload_final_pp';

    // ================= Relasi =================
    public function ajuan()
    {
        return $this->belongsTo(EtamHiPpAjuan::class, 'ajuan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
