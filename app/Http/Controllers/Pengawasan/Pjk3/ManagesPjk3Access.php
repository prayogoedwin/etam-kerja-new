<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ManagesPjk3Access
{
    /**
     * @return array<int, string>
     */
    protected function pjk3StrukturKode(): array
    {
        return ['2', '21'];
    }

    protected function currentRoleName(): ?string
    {
        return Auth::user()?->roles[0]['name'] ?? null;
    }

    protected function canAccessPjk3Admin(): bool
    {
        if (in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi', 'eksekutif-provinsi'], true)) {
            return true;
        }

        return $this->isPjk3StrukturStaff();
    }

    protected function canVerifyPjk3(): bool
    {
        if (in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi'], true)) {
            return true;
        }

        return $this->isPjk3StrukturStaff();
    }

    protected function isPjk3StrukturStaff(): bool
    {
        $kode = (string) (Auth::user()?->kode_struktur ?? '');

        return in_array($kode, $this->pjk3StrukturKode(), true);
    }

    protected function denyIfCannotAccessAdmin(): void
    {
        if (! $this->canAccessPjk3Admin()) {
            abort(403);
        }
    }

    protected function denyIfCannotVerify(): void
    {
        if (! $this->canVerifyPjk3()) {
            abort(403);
        }
    }

    protected function canAccessSuketAdmin(): bool
    {
        if (in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi', 'eksekutif-provinsi'], true)) {
            return true;
        }

        return $this->kodeStrukturDiBidangDua((string) Auth::user()?->kode_struktur)
            && in_array($this->currentRoleName(), ['kepala-bidang', 'kepala-seksi', 'admin-bidang', 'petugas-bidang'], true);
    }

    protected function canKelolaMasterK3(): bool
    {
        if (in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi'], true)) {
            return true;
        }

        return $this->currentRoleName() === 'admin-bidang'
            && $this->kodeStrukturDiBidangDua((string) Auth::user()?->kode_struktur);
    }

    protected function isPerusahaanPjk3(?int $userId = null): bool
    {
        return EtamPjk3Ajuan::isPjk3Aktif($userId ?? (int) Auth::id());
    }

    /**
     * @return array<int, string>
     */
    protected function kodeStrukturDisposisiSuket(): array
    {
        return ['2', '21', '22'];
    }

    protected function canDisposisiSuket(): bool
    {
        if (in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi'], true)) {
            return true;
        }

        $kode = (string) (Auth::user()?->kode_struktur ?? '');
        if ($this->currentRoleName() === 'admin-bidang') {
            return in_array($kode, $this->kodeStrukturDisposisiSuket(), true);
        }

        return $this->currentRoleName() === 'kepala-seksi'
            && in_array($kode, ['21', '22'], true);
    }

    protected function isKasiK3(): bool
    {
        return $this->canDisposisiSuket();
    }

    protected function isPengawasK3(): bool
    {
        if ($this->currentRoleName() === 'petugas-bidang') {
            return $this->kodeStrukturDiBidangDua((string) Auth::user()?->kode_struktur);
        }

        return in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi'], true);
    }

    protected function isKadis(): bool
    {
        return in_array($this->currentRoleName(), ['eksekutif-provinsi', 'super-admin', 'admin-provinsi'], true);
    }

    protected function kodeStrukturDiBidangDua(string $kode): bool
    {
        return $kode === '2' || str_starts_with($kode, '2');
    }

    /**
     * @return Collection<int, User>
     */
    protected function daftarPengawasBidangDua(): Collection
    {
        return User::query()
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('kode_struktur', '2')
                    ->orWhere('kode_struktur', 'like', '2%');
            })
            ->whereHas('roles', function ($q) {
                $q->where('name', 'petugas-bidang');
            })
            ->orderBy('name')
            ->get();
    }

    protected function nextNomorSuket(int $kategoriId, string $kode): string
    {
        $tahun = now()->year;
        $count = EtamPjk3Suket::query()
            ->where('kategori_id', $kategoriId)
            ->whereNotNull('nomor_suket')
            ->whereYear('created_at', $tahun)
            ->count() + 1;

        return sprintf('%s.%03d/SUKET-K3/%d', $kode ?: 'K3', $count, $tahun);
    }

    protected function streamSuketDokumen(EtamPjk3Suket $suket, string $key): StreamedResponse
    {
        abort_unless(array_key_exists($key, EtamPjk3Suket::dokumenKeys()), 404);

        $item = $suket->dokumen[$key] ?? null;
        abort_unless(($item['tipe'] ?? null) === 'file' && ! empty($item['value']), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($item['value']), 404);

        $filename = basename($item['value']);

        return $disk->response($item['value'], $filename, [
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    protected function streamPjk3Dokumen(EtamPjk3Ajuan $ajuan, string $key): StreamedResponse
    {
        abort_unless(array_key_exists($key, EtamPjk3Ajuan::dokumenKeys()), 404);

        $item = $ajuan->dokumen[$key] ?? null;
        abort_unless(($item['tipe'] ?? null) === 'file' && ! empty($item['value']), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($item['value']), 404);

        $filename = basename($item['value']);

        return $disk->response($item['value'], $filename, [
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
