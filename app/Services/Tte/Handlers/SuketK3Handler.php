<?php

namespace App\Services\Tte\Handlers;

use App\Models\EtamStruktur;
use App\Models\EtamTte;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use App\Models\User;
use App\Services\Pengawasan\SuketDokumenTteService;
use App\Services\Tte\TteModuleHandler;
use App\Services\Tte\TteQueue;
use Illuminate\Support\Str;

class SuketK3Handler implements TteModuleHandler
{
    public function __construct(
        private TteQueue $queue,
        private SuketDokumenTteService $dokumen,
    ) {}

    public function modul(): string
    {
        return 'suket_k3';
    }

    public function modelClass(): string
    {
        return EtamPjk3Suket::class;
    }

    public function judul(EtamTte $tte): string
    {
        if ($tte->judul) {
            return $tte->judul;
        }

        $suket = EtamPjk3Suket::query()->with('profilPerusahaan')->find($tte->id_refrensi);
        $nama = $suket?->profilPerusahaan?->name ?? '#'.$tte->id_refrensi;

        return $this->kodeDokumen($tte) === EtamPjk3Suket::TTE_DOK_SUKET
            ? 'Suket Layak K3 — '.$nama
            : 'LHPP K3 — '.$nama;
    }

    public function nilaiStatusSukses(EtamTte $tte): mixed
    {
        $suket = EtamPjk3Suket::query()->find($tte->id_refrensi);
        $current = (int) ($suket?->status ?? EtamPjk3Suket::STATUS_DRAFT);

        if ($this->adalahTteKadis($tte)) {
            return EtamPjk3Suket::STATUS_TTD_KADIS;
        }

        if ($this->kodeDokumen($tte) === EtamPjk3Suket::TTE_DOK_SUKET) {
            return max($current, EtamPjk3Suket::STATUS_TTD_PENGAWAS);
        }

        return max($current, EtamPjk3Suket::STATUS_DRAFT);
    }

    public function setelahBerhasil(EtamTte $tte): void
    {
        $suket = EtamPjk3Suket::query()->with(['pengawas', 'profilPerusahaan'])->find($tte->id_refrensi);
        if (! $suket) {
            return;
        }

        if ($this->adalahTteKadis($tte)) {
            $suket->ttd_kadis_at = now();
            $suket->updated_by = $tte->user_id_tte;
            $suket->save();

            return;
        }

        if ($this->kodeDokumen($tte) === EtamPjk3Suket::TTE_DOK_SUKET) {
            $suket->ttd_pengawas_at = now();
            $suket->updated_by = $tte->user_id_tte;
            $suket->save();
        }

        if (! $this->pengawasSelesai($suket->id)) {
            return;
        }

        $pathSuket = $tte->kode_dokumen === EtamPjk3Suket::TTE_DOK_SUKET
            ? $tte->path_file_after
            : EtamTte::query()
                ->where('nama_modul', $this->modul())
                ->where('id_refrensi', $suket->id)
                ->where('kode_dokumen', EtamPjk3Suket::TTE_DOK_SUKET)
                ->where('status', EtamTte::STATUS_BERHASIL)
                ->where(function ($q) {
                    $q->whereNull('id_struktur')
                        ->orWhere('id_struktur', '!=', EtamStruktur::KODE_DINAS);
                })
                ->orderByDesc('id')
                ->value('path_file_after');

        if (! is_string($pathSuket) || $pathSuket === '') {
            $pathSuket = $this->dokumen->simpanDokumenTte($suket, EtamPjk3Suket::TTE_DOK_SUKET, false);
        }

        $this->queue->ajukan([
            'nama_modul' => $this->modul(),
            'nama_table' => $suket->getTable(),
            'id_refrensi' => $suket->id,
            'kolom_status_refrensi' => 'status',
            'id_struktur' => EtamStruktur::KODE_DINAS,
            'user_id_tujuan' => User::idKepalaDinas(),
            'kode_dokumen' => EtamPjk3Suket::TTE_DOK_SUKET,
            'path_file_before' => $pathSuket,
            'token_verifikasi' => Str::lower(Str::random(40)),
            'judul' => 'Suket Layak K3 — '.($suket->profilPerusahaan->name ?? $suket->nama_alat),
        ]);
    }

    public function setelahGagal(EtamTte $tte): void
    {
        //
    }

    public function siapkanBerkasDs(EtamTte $tte): void
    {
        if ($this->adalahTteKadis($tte)) {
            return;
        }

        $suket = EtamPjk3Suket::query()->find($tte->id_refrensi);
        if (! $suket) {
            return;
        }

        $path = $this->dokumen->simpanDokumenTte($suket, $this->kodeDokumen($tte), false);
        if ($tte->path_file_before !== $path) {
            $tte->path_file_before = $path;
            $tte->save();
        }
    }

    public function ajukanPengawas(EtamPjk3Suket $suket): void
    {
        $suket->loadMissing(['pengawas', 'profilPerusahaan']);
        $pengawas = $suket->pengawas;
        $idPengawas = $pengawas?->id ?: $suket->pengawas_id;
        $kodeStruktur = $pengawas?->kode_struktur ? (string) $pengawas->kode_struktur : null;
        $nama = $suket->profilPerusahaan->name ?? $suket->nama_alat;

        foreach ([EtamPjk3Suket::TTE_DOK_LHPP, EtamPjk3Suket::TTE_DOK_SUKET] as $kode) {
            $path = $this->dokumen->simpanDokumenTte($suket, $kode, false);
            $judul = $kode === EtamPjk3Suket::TTE_DOK_SUKET
                ? 'Suket Layak K3 — '.$nama
                : 'LHPP K3 — '.$nama;

            $this->queue->ajukan([
                'nama_modul' => $this->modul(),
                'nama_table' => $suket->getTable(),
                'id_refrensi' => $suket->id,
                'kolom_status_refrensi' => 'status',
                'id_struktur' => $kodeStruktur,
                'user_id_tujuan' => $idPengawas,
                'kode_dokumen' => $kode,
                'path_file_before' => $path,
                'token_verifikasi' => Str::lower(Str::random(40)),
                'judul' => $judul,
            ]);
        }
    }

    private function pengawasSelesai(int $suketId): bool
    {
        $berhasil = EtamTte::query()
            ->where('nama_modul', $this->modul())
            ->where('id_refrensi', $suketId)
            ->where('status', EtamTte::STATUS_BERHASIL)
            ->where(function ($q) {
                $q->whereNull('id_struktur')
                    ->orWhere('id_struktur', '!=', EtamStruktur::KODE_DINAS);
            })
            ->pluck('kode_dokumen');

        return $berhasil->contains(EtamPjk3Suket::TTE_DOK_LHPP)
            && $berhasil->contains(EtamPjk3Suket::TTE_DOK_SUKET);
    }

    private function adalahTteKadis(EtamTte $tte): bool
    {
        return (string) $tte->id_struktur === EtamStruktur::KODE_DINAS;
    }

    private function kodeDokumen(EtamTte $tte): string
    {
        if ($tte->kode_dokumen) {
            return $tte->kode_dokumen;
        }

        return $this->adalahTteKadis($tte)
            ? EtamPjk3Suket::TTE_DOK_SUKET
            : EtamPjk3Suket::TTE_DOK_LHPP;
    }
}
