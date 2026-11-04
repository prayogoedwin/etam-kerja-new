<?php

namespace App\Services\Tte\Handlers;

use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\EtamTte;
use App\Services\Blk\BlkSertifikatKelulusanService;
use App\Services\Tte\TteModuleHandler;
use App\Services\Tte\TteQueue;
use Illuminate\Support\Str;

class BlkPesertaHandler implements TteModuleHandler
{
    public function __construct(
        private TteQueue $queue,
        private BlkSertifikatKelulusanService $sertifikat,
    ) {}

    public function modul(): string
    {
        return 'blk';
    }

    public function modelClass(): string
    {
        return EtamBlkPelatihanPeserta::class;
    }

    public function judul(EtamTte $tte): string
    {
        if ($tte->judul) {
            return $tte->judul;
        }

        $peserta = EtamBlkPelatihanPeserta::find($tte->id_refrensi);
        $nama = $peserta->name ?? '#'.$tte->id_refrensi;

        return $this->kodeDokumen($tte) === EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT
            ? 'Sertifikat Kelulusan — '.$nama
            : 'Daftar Unit Kompetensi — '.$nama;
    }

    public function nilaiStatusSukses(EtamTte $tte): mixed
    {
        return $this->kodeDokumen($tte) === EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT
            ? EtamBlkPelatihanPeserta::TTD_TTD_DINAS
            : EtamBlkPelatihanPeserta::TTD_TTD_UPTD;
    }

    /**
     * Implementasi BLK dari hook umum TteModuleHandler::setelahBerhasil().
     * Engine memanggil ini untuk setiap modul; di sini BLK simpan berkas TTE
     * lalu mengantri sertifikat Kepala Dinas setelah kompetensi ditandatangani.
     */
    public function setelahBerhasil(EtamTte $tte): void
    {
        $peserta = EtamBlkPelatihanPeserta::query()->with('pelatihan.blk')->find($tte->id_refrensi);
        if (! $peserta) {
            return;
        }

        $kode = $this->kodeDokumen($tte);

        if ($kode === EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT) {
            $peserta->ttd_dinas_at = now();
            $peserta->updated_by = $tte->user_id_tte;
            $peserta->save();

            return;
        }

        $peserta->ttd_uptd_at = now();
        $peserta->updated_by = $tte->user_id_tte;
        $peserta->save();

        $tokenSertifikat = Str::lower(Str::random(40));
        $pathSertifikat = $this->sertifikat->simpanDokumenTte(
            $peserta,
            EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT,
            false,
            route('tte.berkas', $tokenSertifikat)
        );

        $this->queue->ajukan([
            'nama_modul' => $this->modul(),
            'nama_table' => $peserta->getTable(),
            'id_refrensi' => $peserta->id,
            'kolom_status_refrensi' => 'status_ttd',
            'id_struktur' => null,
            'kode_dokumen' => EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT,
            'path_file_before' => $pathSertifikat,
            'token_verifikasi' => $tokenSertifikat,
            'judul' => 'Sertifikat Kelulusan — '.$peserta->name,
        ]);
    }

    public function setelahGagal(EtamTte $tte): void
    {
        //
    }

    private function kodeDokumen(EtamTte $tte): string
    {
        if ($tte->kode_dokumen) {
            return $tte->kode_dokumen;
        }

        return $tte->id_struktur
            ? EtamBlkPelatihanPeserta::TTE_DOK_KOMPETENSI
            : EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT;
    }
}
