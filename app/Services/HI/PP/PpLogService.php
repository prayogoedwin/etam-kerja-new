<?php

namespace App\Services\HI\PP;

use App\Models\HI\PP\EtamHiPpLog;
use Illuminate\Support\Facades\Auth;

class PpLogService
{
    /**
     * Catat log aktivitas modul HI PP.
     *
     * @param  int         $ajuanId
     * @param  string      $action     Salah satu konstanta EtamHiPpLog::ACTION_*
     * @param  string|null $description Kalimat manusiawi (opsional)
     * @param  array       $meta       Data tambahan
     */
    public static function log(
        int $ajuanId,
        string $action,
        ?string $description = null,
        array $meta = []
    ): EtamHiPpLog {
        $user = Auth::user();

        // Ambil role dari Spatie / manual
        $role = null;
        if ($user) {
            if (method_exists($user, 'getRoleNames')) {
                $role = $user->getRoleNames()->first();
            } elseif (isset($user->roles[0]['name'])) {
                $role = $user->roles[0]['name'];
            }
        }

        return EtamHiPpLog::create([
            'ajuan_id'    => $ajuanId,
            'user_id'     => $user->id ?? null,
            'role'        => $role,
            'action'      => $action,
            'description' => $description,
            'meta'        => $meta ?: null,
            'ip_address'  => request()->ip(),
            'user_agent'  => substr((string) request()->userAgent(), 0, 255),
            'created_at'  => now(),
        ]);
    }
}
