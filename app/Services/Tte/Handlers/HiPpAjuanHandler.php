<?php

namespace App\Services\Tte\Handlers;

use App\Models\EtamTte;
use App\Models\HI\PP\EtamHiPpAjuan;
use App\Services\Tte\TteModuleHandler;

class HiPpAjuanHandler implements TteModuleHandler
{
    public function modul(): string
    {
        return 'hi_pp';
    }

    public function modelClass(): string
    {
        return EtamHiPpAjuan::class;
    }

    public function judul(EtamTte $tte): string
    {
        return $tte->judul ?: 'Surat Pengesahan Peraturan Perusahaan — #'.$tte->id_refrensi;
    }

    public function nilaiStatusSukses(EtamTte $tte): mixed
    {
        return 1;
    }

    public function setelahBerhasil(EtamTte $tte): void
    {
        // TteService sudah mengubah status_ttd menjadi nilaiStatusSukses().
    }

    public function setelahGagal(EtamTte $tte): void
    {
        // Status pengajuan tetap menunggu sampai dokumen berhasil ditandatangani.
    }

    public function siapkanBerkasDs(EtamTte $tte): void
    {
        //
    }
}
