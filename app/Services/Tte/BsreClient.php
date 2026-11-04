<?php

namespace App\Services\Tte;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BsreClient
{
    public const OK = 'ok';

    public const PASSPHRASE_SALAH = 'passphrase_salah';

    public const KONEKSI_PUTUS = 'koneksi_putus';

    public const NIK_KOSONG = 'nik_kosong';

    public const GAGAL = 'gagal';

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null}
     */
    public function tandatangani(?string $pathFile, string $passphrase, ?string $nik = null): array
    {
        $pdf = $this->bacaPdf($pathFile);
        if ($pdf === null) {
            if (config('tte.bsre.demo', true)) {
                $pdf = "%PDF-1.4\n% demo-tte\n%%EOF\n";
            } else {
                return $this->gagal(self::GAGAL, 'Berkas PDF sumber tidak ditemukan');
            }
        }

        if (config('tte.bsre.demo', true)) {
            return $this->demo($pdf, $passphrase);
        }

        return $this->kirimKeBsre($pdf, $passphrase, $nik);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null}
     */
    private function demo(string $pdf, string $passphrase): array
    {
        $expected = (string) config('tte.bsre.demo_passphrase', '');
        if ($expected !== '' && ! hash_equals($expected, $passphrase)) {
            return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah');
        }

        if (config('tte.bsre.simulate_disconnect')) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server BSrE terputus');
        }

        return [
            'ok' => true,
            'kode' => self::OK,
            'pesan' => 'TTE berhasil (mode demo)',
            'pdf' => $pdf,
        ];
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null}
     */
    private function kirimKeBsre(string $pdf, string $passphrase, ?string $nik): array
    {
        $url = rtrim((string) config('tte.bsre.url'), '/');
        if ($url === '') {
            return $this->gagal(self::KONEKSI_PUTUS, 'Alamat server BSrE belum diatur');
        }

        try {
            $response = Http::timeout((int) config('tte.bsre.timeout', 30))
                ->attach('file', $pdf, 'dokumen.pdf')
                ->post($url, [
                    'nik' => $nik,
                    'passphrase' => $passphrase,
                ]);
        } catch (ConnectionException $e) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server BSrE terputus');
        } catch (\Throwable $e) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server BSrE terputus');
        }

        if ($response->status() === 401 || $response->status() === 403) {
            return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah');
        }

        if (! $response->successful()) {
            $pesan = trim((string) $response->json('message'));
            if (str_contains(strtolower($pesan), 'passphrase') || str_contains(strtolower($pesan), 'sandi')) {
                return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah');
            }

            return $this->gagal(self::GAGAL, $pesan !== '' ? $pesan : 'Proses TTE di BSrE gagal');
        }

        $pdf = $this->pdfDariRespons($response->body(), $response->json());
        if ($pdf === null) {
            return $this->gagal(self::GAGAL, 'Respons BSrE bukan berkas PDF');
        }

        return [
            'ok' => true,
            'kode' => self::OK,
            'pesan' => 'TTE berhasil',
            'pdf' => $pdf,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private function pdfDariRespons(string $body, mixed $json): ?string
    {
        if (str_starts_with($body, '%PDF')) {
            return $body;
        }

        if (! is_array($json)) {
            return null;
        }

        $kandidat = [
            $json['base64'] ?? null,
            $json['file'] ?? null,
            $json['pdf'] ?? null,
            $json['document'] ?? null,
            $json['data']['base64'] ?? null,
            $json['data']['file'] ?? null,
            $json['data']['pdf'] ?? null,
            $json['url'] ?? null,
            $json['download_url'] ?? null,
            $json['file_url'] ?? null,
            $json['link'] ?? null,
            $json['data']['url'] ?? null,
            $json['data']['download_url'] ?? null,
        ];

        foreach ($kandidat as $nilai) {
            if (! is_string($nilai) || $nilai === '') {
                continue;
            }
            if (str_starts_with($nilai, '%PDF')) {
                return $nilai;
            }
            if (str_starts_with($nilai, 'http://') || str_starts_with($nilai, 'https://')) {
                $unduh = $this->unduhPdf($nilai);
                if ($unduh !== null) {
                    return $unduh;
                }

                continue;
            }
            $decoded = base64_decode($nilai, true);
            if (is_string($decoded) && str_starts_with($decoded, '%PDF')) {
                return $decoded;
            }
        }

        return null;
    }

    private function unduhPdf(string $url): ?string
    {
        try {
            $response = Http::timeout((int) config('tte.bsre.timeout', 30))->get($url);
        } catch (\Throwable $e) {
            return null;
        }

        $body = $response->body();

        return $response->successful() && str_starts_with($body, '%PDF') ? $body : null;
    }

    private function bacaPdf(?string $pathFile): ?string
    {
        if (! $pathFile || ! Storage::disk('local')->exists($pathFile)) {
            return null;
        }

        $isi = Storage::disk('local')->get($pathFile);

        return is_string($isi) && $isi !== '' ? $isi : null;
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null}
     */
    private function gagal(string $kode, string $pesan): array
    {
        return [
            'ok' => false,
            'kode' => $kode,
            'pesan' => $pesan,
            'pdf' => null,
        ];
    }
}
