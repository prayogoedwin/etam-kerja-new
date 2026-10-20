<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan;
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
        if (in_array($this->currentRoleName(), ['super-admin', 'admin-provinsi'], true)) {
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
