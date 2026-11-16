<?php

namespace App\Services\Tte;

use App\Models\EtamTte;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class TteVerifikasi
{
    /**
     * @return array{url: string|null, qr: string|null}
     */
    public function untukDokumen(string $modul, int $idRefrensi, ?string $kodeDokumen = null): array
    {
        $tte = $this->temukanUntukDokumen($modul, $idRefrensi, $kodeDokumen);
        $url = $tte ? $this->urlUntuk($tte) : null;

        return [
            'url' => $url,
            'qr' => $url ? $this->qrDataUri($url) : null,
        ];
    }

    public function temukanUntukDokumen(string $modul, int $idRefrensi, ?string $kodeDokumen = null): ?EtamTte
    {
        return EtamTte::query()
            ->where('nama_modul', $modul)
            ->where('id_refrensi', $idRefrensi)
            ->when(
                filled($kodeDokumen),
                fn ($q) => $q->where('kode_dokumen', $kodeDokumen)
            )
            ->whereNotNull('token_verifikasi')
            ->where('token_verifikasi', '!=', '')
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [EtamTte::STATUS_MENUNGGU])
            ->orderByDesc('id')
            ->first();
    }

    public function cari(string $modul, string $kode): ?EtamTte
    {
        $modul = trim($modul);
        $kode = trim($kode);
        if ($modul === '' || $kode === '') {
            return null;
        }

        return EtamTte::query()
            ->where('nama_modul', $modul)
            ->where('token_verifikasi', $kode)
            ->first();
    }

    public function urlUntuk(EtamTte $tte): ?string
    {
        $modul = trim((string) $tte->nama_modul);
        $kode = trim((string) $tte->token_verifikasi);
        if ($modul === '' || $kode === '') {
            return null;
        }

        return url('verifikasi/tte/'.$modul.'/'.$kode);
    }

    public function qrDataUri(string $url, int $size = 220): string
    {
        $logoPath = $this->pathLogoKaltim();

        if (function_exists('imagecreatetruecolor')) {
            try {
                $png = $this->buatQrPngDenganLogo($url, $size, $logoPath);
                if ($png !== '') {
                    return 'data:image/png;base64,'.base64_encode($png);
                }
            } catch (Throwable) {
                // SVG fallback di bawah.
            }
        }

        $svg = (string) QrCode::size($size)->margin(1)->errorCorrection('H')->generate($url);
        if ($logoPath) {
            $svg = $this->sisipkanLogoKeSvg($svg, $size, $logoPath);
        }

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function pathLogoKaltim(): ?string
    {
        foreach ([
            public_path('assets/etam_fe/images/logo/logo_kaltim.png'),
            public_path('assets/etam_be/images/logo/logo_kaltim.png'),
        ] as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function buatQrPngDenganLogo(string $url, int $size, ?string $logoPath): string
    {
        $qr = Encoder::encode($url, ErrorCorrectionLevel::H(), Encoder::DEFAULT_BYTE_MODE_ECODING);
        $matrix = $qr->getMatrix();
        $modules = $matrix->getWidth();
        $margin = 1;
        $total = $modules + ($margin * 2);
        $moduleSize = max(1, intdiv($size, $total));
        $canvas = $moduleSize * $total;

        $image = imagecreatetruecolor($canvas, $canvas);
        if ($image === false) {
            return '';
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix->get($x, $y) !== 1) {
                    continue;
                }
                $px = ($x + $margin) * $moduleSize;
                $py = ($y + $margin) * $moduleSize;
                imagefilledrectangle($image, $px, $py, $px + $moduleSize - 1, $py + $moduleSize - 1, $black);
            }
        }

        if ($logoPath) {
            $this->tempelLogoKaltim($image, $canvas, $logoPath);
        }

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    private function tempelLogoKaltim($image, int $canvas, string $logoPath): void
    {
        $logo = @imagecreatefrompng($logoPath);
        if ($logo === false) {
            return;
        }
        imagesavealpha($logo, true);

        $logoW = imagesx($logo);
        $logoH = imagesy($logo);
        if ($logoW < 1 || $logoH < 1) {
            imagedestroy($logo);

            return;
        }

        $max = (int) round($canvas * 0.24);
        if ($logoH >= $logoW) {
            $destH = $max;
            $destW = max(1, (int) round($max * ($logoW / $logoH)));
        } else {
            $destW = $max;
            $destH = max(1, (int) round($max * ($logoH / $logoW)));
        }

        $pad = (int) round(max($destW, $destH) * 0.22);
        $bg = max($destW, $destH) + ($pad * 2);
        $cx = (int) round($canvas / 2);
        $cy = (int) round($canvas / 2);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledellipse($image, $cx, $cy, $bg, $bg, $white);

        imagecopyresampled(
            $image,
            $logo,
            (int) round($cx - ($destW / 2)),
            (int) round($cy - ($destH / 2)),
            0,
            0,
            $destW,
            $destH,
            $logoW,
            $logoH
        );
        imagedestroy($logo);
    }

    private function sisipkanLogoKeSvg(string $svg, int $size, string $logoPath): string
    {
        $logo = base64_encode((string) file_get_contents($logoPath));
        $logoSize = (int) round($size * 0.24);
        $pad = (int) round($logoSize * 0.22);
        $bg = $logoSize + ($pad * 2);
        $x = ($size - $logoSize) / 2;
        $y = ($size - $logoSize) / 2;
        $overlay = sprintf(
            '<circle cx="%1$s" cy="%1$s" r="%2$s" fill="#fff"/>'.
            '<image x="%3$s" y="%4$s" width="%5$s" height="%5$s" href="data:image/png;base64,%6$s"/>',
            $size / 2,
            $bg / 2,
            $x,
            $y,
            $logoSize,
            $logo
        );

        return preg_replace('/<\/svg>\s*$/i', $overlay.'</svg>', $svg, 1) ?? $svg;
    }

    public function pathBerkasAsli(EtamTte $tte): ?string
    {
        if ((int) $tte->status !== EtamTte::STATUS_BERHASIL) {
            return null;
        }

        $path = $tte->path_file_after;
        if (! is_string($path) || $path === '' || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        return $path;
    }

    public function labelModul(string $modul): string
    {
        return match ($modul) {
            'blk' => 'BLK',
            'suket_k3' => 'Suket K3',
            'hi_pp' => 'Hubungan Industrial',
            default => strtoupper(str_replace('_', ' ', $modul)),
        };
    }

    /**
     * @return array{label: string, kelas: string}
     */
    public function statusPublik(?EtamTte $tte): array
    {
        if (! $tte) {
            return ['label' => 'Tidak ditemukan', 'kelas' => 'fail'];
        }

        return match ((int) $tte->status) {
            EtamTte::STATUS_BERHASIL => [
                'label' => 'Telah ditandatangani secara elektronik',
                'kelas' => 'ok',
            ],
            EtamTte::STATUS_GAGAL => [
                'label' => 'Tanda tangan elektronik gagal',
                'kelas' => 'fail',
            ],
            default => [
                'label' => 'Menunggu tanda tangan elektronik',
                'kelas' => 'wait',
            ],
        };
    }

    public function pesanBerkasKosong(?EtamTte $tte): string
    {
        if (! $tte) {
            return 'Kode verifikasi tidak ditemukan. Pastikan tautan atau QR code yang dipindai masih berlaku.';
        }

        return match ((int) $tte->status) {
            EtamTte::STATUS_BERHASIL => 'Berkas asli belum dapat ditampilkan. Silakan coba lagi nanti.',
            EtamTte::STATUS_GAGAL => 'Tanda tangan elektronik gagal. Berkas asli belum diterbitkan.',
            default => 'Berkas asli belum tersedia. Dokumen masih dalam proses tanda tangan elektronik.',
        };
    }
}
