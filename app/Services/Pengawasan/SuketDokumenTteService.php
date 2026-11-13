<?php

namespace App\Services\Pengawasan;

use App\Models\EtamTte;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SuketDokumenTteService
{
    public const BSRE_TEKS = "Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik\nyang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).";

    public function pathDokumenTte(int $suketId, string $kodeDokumen, bool $sudahTte = false, ?Carbon $waktu = null): string
    {
        $waktu ??= now();
        $nama = $kodeDokumen === EtamPjk3Suket::TTE_DOK_SUKET
            ? 'suket_tte_'.$suketId.'.pdf'
            : 'lhpp_tte_'.$suketId.'.pdf';

        return sprintf(
            'tte/%s/suket_k3/%s/%s/%s',
            $sudahTte ? 'after' : 'before',
            $waktu->format('Y'),
            $waktu->format('m'),
            $nama
        );
    }

    public function simpanDokumenTte(EtamPjk3Suket $suket, string $kodeDokumen, bool $sudahTte = false): string
    {
        $path = $this->pathDokumenTte($suket->id, $kodeDokumen, $sudahTte);
        Storage::disk('local')->put($path, $this->buatPdfDokumen($suket, $kodeDokumen));

        return $path;
    }

    public function buatPdfDokumen(EtamPjk3Suket $suket, string $kodeDokumen): string
    {
        $suket->loadMissing([
            'profilPjk3', 'profilPerusahaan', 'kategori', 'ahliK3Kategori', 'jenis',
            'pengawas', 'perangkat.jenis', 'perangkat.uji',
        ]);
        $view = $kodeDokumen === EtamPjk3Suket::TTE_DOK_SUKET
            ? 'backend.pengawasan.pjk3.suket.cetak-suket'
            : 'backend.pengawasan.pjk3.suket.cetak-lhpp';

        $html = view($view, [
            'suket' => $suket,
            'untukDs' => true,
            'tampilDraft' => false,
            'logoBsre' => $this->dataUriLogoBsre(),
            'bsreTeks' => self::BSRE_TEKS,
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOption('defaultMediaType', 'print')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->output();
    }

    public function pathBerkasTte(EtamPjk3Suket $suket, string $kodeDokumen): ?string
    {
        $rows = EtamTte::query()
            ->where('nama_modul', 'suket_k3')
            ->where('id_refrensi', $suket->id)
            ->where('kode_dokumen', $kodeDokumen)
            ->where('status', EtamTte::STATUS_BERHASIL)
            ->whereNotNull('path_file_after')
            ->orderByDesc('id')
            ->get(['path_file_after']);

        foreach ($rows as $row) {
            $path = $row->path_file_after;
            if (is_string($path) && $path !== '' && Storage::disk('local')->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function dataUriLogoBsre(): string
    {
        $path = public_path('assets/etam_be/images/logo/logo_bsre.png');
        if (! is_file($path)) {
            return asset('assets/etam_be/images/logo/logo_bsre.png');
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }
}
