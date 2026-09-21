<?php

namespace App\Http\Controllers\BLK;

use App\Models\BLK\EtamBlk;
use App\Models\BLK\UserBlk;
use Illuminate\Support\Facades\Auth;

trait ManagesBlkAccess
{
    protected function currentRoleName(): ?string
    {
        return Auth::user()?->roles[0]['name'] ?? null;
    }

    protected function isBlkMasterAdmin(): bool
    {
        return in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi'], true);
    }

    /**
     * @return array<int, string>
     */
    protected function balaiStaffRoles(): array
    {
        return ['kepala-balai', 'admin-balai', 'petugas-balai'];
    }

    protected function isBlkStaffRole(): bool
    {
        return in_array($this->currentRoleName(), $this->balaiStaffRoles(), true);
    }

    protected function currentBlkProfile(): ?UserBlk
    {
        return UserBlk::where('user_id', Auth::id())->first();
    }

    /**
     * null = semua BLK, array = id yang boleh diakses.
     *
     * @return array<int, int>|null
     */
    protected function accessibleBlkIds(): ?array
    {
        if ($this->isBlkMasterAdmin()) {
            return null;
        }

        if ($this->isBlkStaffRole()) {
            $kodeStruktur = Auth::user()?->kode_struktur;
            if (! $kodeStruktur) {
                return [];
            }

            return array_map('intval', EtamBlk::query()
                ->where('kode_struktur', $kodeStruktur)
                ->pluck('id')
                ->all());
        }

        $profile = $this->currentBlkProfile();
        if (! $profile) {
            return [];
        }

        return match ((int) $profile->tipe_akun) {
            UserBlk::TIPE_ALL => null,
            UserBlk::TIPE_PROVINSI => array_map('intval', EtamBlk::query()->where('tipe_lembaga', EtamBlk::TIPE_PROVINSI)->pluck('id')->all()),
            UserBlk::TIPE_KABKOTA => array_map('intval', EtamBlk::query()->where('tipe_lembaga', EtamBlk::TIPE_KABKOTA)->pluck('id')->all()),
            default => $profile->blk_id ? [(int) $profile->blk_id] : [],
        };
    }

    protected function canAccessBlk(?int $blkId): bool
    {
        $ids = $this->accessibleBlkIds();

        if ($ids === null) {
            return true;
        }

        if (! $blkId) {
            return false;
        }

        return in_array($blkId, $ids, true);
    }

    protected function isBlkBalaiAdmin(): bool
    {
        return $this->currentRoleName() === 'admin-balai';
    }

    /**
     * Role yang boleh ditambahkan admin balai.
     *
     * @return array<string, string>
     */
    protected function balaiStaffCreateRoles(): array
    {
        return [
            'admin-balai' => 'Admin',
            'petugas-balai' => 'Petugas',
        ];
    }

    protected function canManageBlkUsers(): bool
    {
        if ($this->isBlkMasterAdmin()) {
            return true;
        }

        if ($this->isBlkBalaiAdmin()) {
            return true;
        }

        $profile = $this->currentBlkProfile();
        if (! $profile) {
            return false;
        }

        return in_array((int) $profile->tipe_akun, [
            UserBlk::TIPE_ALL,
            UserBlk::TIPE_PROVINSI,
            UserBlk::TIPE_KABKOTA,
            UserBlk::TIPE_ADMIN_BLK,
        ], true);
    }

    protected function canMutateBlkUsers(): bool
    {
        return $this->canManageBlkUsers() && ! $this->isBlkBalaiAdmin();
    }

    protected function canCreatePelatihan(): bool
    {
        if ($this->isBlkMasterAdmin()) {
            return true;
        }

        if ($this->currentRoleName() === 'admin-blk') {
            return true;
        }

        return $this->isBlkStaffRole();
    }
}
