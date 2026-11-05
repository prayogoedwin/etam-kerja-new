<?php

namespace App\Services\Tte;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DsClient
{
    public const OK = 'ok';

    public const PASSPHRASE_SALAH = 'passphrase_salah';

    public const KONEKSI_PUTUS = 'koneksi_putus';

    public const NIK_KOSONG = 'nik_kosong';

    public const GAGAL = 'gagal';

    private const DEMO_PASSPHRASE = 'rahasia-tte';

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null}
     */
    public function tandatangani(?string $pathFile, string $passphrase, ?string $nik = null, ?string $keterangan = null): array
    {
        $pdf = $this->bacaPdf($pathFile);
        if ($pdf === null) {
            if (config('tte.ds.demo', true)) {
                $pdf = "%PDF-1.4\n% demo-tte\n%%EOF\n";
            } else {
                return $this->gagal(self::GAGAL, 'Berkas PDF sumber tidak ditemukan');
            }
        }

        if (config('tte.ds.demo', true)) {
            return $this->demo($pdf, $passphrase);
        }

        return $this->kirimKeDs($pdf, $passphrase, $nik, $keterangan);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null}
     */
    private function demo(string $pdf, string $passphrase): array
    {
        if (! hash_equals(self::DEMO_PASSPHRASE, $passphrase)) {
            return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah');
        }

        if (config('tte.ds.simulate_disconnect')) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus');
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
    private function kirimKeDs(string $pdf, string $passphrase, ?string $nik, ?string $keterangan): array
    {
        $base = rtrim((string) config('tte.ds.url'), '/');
        $token = (string) config('tte.ds.token');
        $template = (string) config('tte.ds.template');

        if ($base === '' || $token === '') {
            return $this->gagal(self::KONEKSI_PUTUS, 'Alamat atau token server DS belum diatur');
        }

        if ($template === '') {
            return $this->gagal(self::GAGAL, 'Kode template DS belum diatur');
        }

        $kirim = $this->postMultipart($base.'/ttdds', [
            'nik' => (string) $nik,
            'template' => $template,
            'keterangan' => (string) $keterangan,
        ], ['pdf', $pdf, 'dokumen.pdf']);

        if (! $kirim['ok']) {
            return $kirim;
        }

        $kodeDokumen = $this->kodeDariJson($kirim['json']);
        if ($kodeDokumen === null) {
            return $this->gagal(self::GAGAL, 'DS tidak mengembalikan kode dokumen');
        }

        $tte = $this->postForm($base.'/sign', [
            'kode' => $kodeDokumen,
            'passphrase' => $passphrase,
        ]);
        if (! $tte['ok']) {
            return $tte;
        }

        $unduh = $this->postForm($base.'/dokumen', [
            'kode' => $kodeDokumen,
        ]);
        if (! $unduh['ok']) {
            return $unduh;
        }

        $signed = $unduh['pdf'] ?? $this->pdfDariBody((string) $unduh['body']);
        if ($signed === null) {
            return $this->gagal(self::GAGAL, 'Respons DS bukan berkas PDF');
        }

        return [
            'ok' => true,
            'kode' => self::OK,
            'pesan' => 'TTE berhasil',
            'pdf' => $signed,
        ];
    }

    /**
     * @param  array<string, string>  $fields
     * @param  array{0: string, 1: string, 2: string}|null  $file
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, json?: mixed, body?: string}
     */
    private function postMultipart(string $url, array $fields, ?array $file = null): array
    {
        try {
            $request = $this->http();
            if ($file !== null) {
                $request = $request->attach($file[0], $file[1], $file[2]);
            }
            $response = $request->post($url, $fields);
        } catch (ConnectionException) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus');
        } catch (\Throwable) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus');
        }

        return $this->hasilHttp($response->status(), $response->body(), $response->json());
    }

    /**
     * @param  array<string, string>  $fields
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, json?: mixed, body?: string}
     */
    private function postForm(string $url, array $fields): array
    {
        try {
            $response = $this->http()->asForm()->post($url, $fields);
        } catch (ConnectionException) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus');
        } catch (\Throwable) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus');
        }

        return $this->hasilHttp($response->status(), $response->body(), $response->json());
    }

    private function http(): PendingRequest
    {
        $token = (string) config('tte.ds.token');

        return Http::timeout((int) config('tte.ds.timeout', 30))
            ->withQueryParameters(['token' => $token]);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, json?: mixed, body?: string}
     */
    private function hasilHttp(int $status, string $body, mixed $json): array
    {
        if ($status === 401 || $status === 403) {
            return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah');
        }

        $pdf = $this->pdfDariBody($body);
        if ($status >= 200 && $status < 300 && $pdf !== null) {
            return [
                'ok' => true,
                'kode' => self::OK,
                'pesan' => 'TTE berhasil',
                'pdf' => $pdf,
                'json' => $json,
                'body' => $body,
            ];
        }

        if ($status < 200 || $status >= 300) {
            $pesan = is_array($json) ? trim((string) ($json['message'] ?? '')) : '';
            if (str_contains(strtolower($pesan), 'passphrase') || str_contains(strtolower($pesan), 'sandi')) {
                return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah');
            }

            return $this->gagal(self::GAGAL, $pesan !== '' ? $pesan : 'Proses TTE di DS gagal');
        }

        if (is_array($json) && ($json['success'] ?? true) === false) {
            $pesan = trim((string) ($json['message'] ?? 'Proses TTE di DS gagal'));

            return $this->gagal(self::GAGAL, $pesan);
        }

        return [
            'ok' => true,
            'kode' => self::OK,
            'pesan' => is_array($json) ? (string) ($json['message'] ?? 'Berhasil') : 'Berhasil',
            'pdf' => null,
            'json' => $json,
            'body' => $body,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private function kodeDariJson(mixed $json): ?string
    {
        if (! is_array($json)) {
            return null;
        }

        $kode = $json['data']['kode'] ?? $json['kode'] ?? null;

        return is_string($kode) && $kode !== '' ? $kode : null;
    }

    private function pdfDariBody(string $body): ?string
    {
        return str_starts_with($body, '%PDF') ? $body : null;
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
