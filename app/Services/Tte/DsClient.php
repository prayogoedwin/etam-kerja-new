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
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    public function tandatangani(?string $pathFile, string $passphrase, ?string $nik = null, ?string $keterangan = null, ?string $kodeDokumen = null): array
    {
        if ($kodeDokumen) {
            return $this->lanjutkanDenganKode($kodeDokumen, $passphrase);
        }

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
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    public function kirimDokumen(string $pdf, string $nik, ?string $keterangan = null): array
    {
        if ($pdf === '') {
            return $this->gagal(self::GAGAL, 'Berkas PDF kosong');
        }

        $siap = $this->siapKirim();
        if ($siap !== null) {
            return $siap;
        }

        $hasil = $this->postMultipart(rtrim((string) config('tte.ds.url'), '/').'/ttdds', [
            'nik' => $nik,
            'template' => (string) config('tte.ds.template'),
            'keterangan' => (string) $keterangan,
        ], ['pdf', $pdf, 'dokumen.pdf']);

        $kodeDokumen = $this->kodeDariJson($hasil['json'] ?? null);
        $hasil['kode_dokumen'] = $kodeDokumen;
        if ($hasil['ok'] && $kodeDokumen === null) {
            return $this->gagal(self::GAGAL, 'DS tidak mengembalikan kode dokumen', [
                'http' => $hasil['http'],
                'body' => $hasil['body'],
                'json' => $hasil['json'] ?? null,
            ]);
        }
        if ($kodeDokumen && $hasil['ok']) {
            $hasil['pesan'] = $hasil['pesan'] !== '' ? $hasil['pesan'] : 'Dokumen terkirim';
        }

        return $hasil;
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    public function tandatanganiKode(string $kodeDokumen, string $passphrase): array
    {
        $siap = $this->siapKirim();
        if ($siap !== null) {
            return $siap;
        }

        $hasil = $this->postForm(rtrim((string) config('tte.ds.url'), '/').'/sign', [
            'kode' => $kodeDokumen,
            'passphrase' => $passphrase,
        ], 'sign');
        $hasil['kode_dokumen'] = $kodeDokumen;

        return $hasil;
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    public function unduhDokumen(string $kodeDokumen): array
    {
        $siap = $this->siapKirim();
        if ($siap !== null) {
            return $siap;
        }

        $hasil = $this->postForm(rtrim((string) config('tte.ds.url'), '/').'/dokumen', [
            'kode' => $kodeDokumen,
        ]);
        $hasil['kode_dokumen'] = $kodeDokumen;
        $hasil['pdf'] = $hasil['pdf'] ?? $this->pdfDariBody((string) ($hasil['body'] ?? ''));
        if ($hasil['ok'] && $hasil['pdf'] === null) {
            return $this->gagal(self::GAGAL, 'Respons DS bukan berkas PDF', [
                'kode_dokumen' => $kodeDokumen,
                'http' => $hasil['http'],
                'body' => $hasil['body'],
                'json' => $hasil['json'] ?? null,
            ]);
        }

        return $hasil;
    }

    /**
     * @return array{kirim: array, sign: array|null, unduh: array|null}
     */
    public function ujiTigaLangkah(string $pdf, string $nik, string $passphrase, ?string $keterangan = null): array
    {
        $kirim = $this->kirimDokumen($pdf, $nik, $keterangan);
        $sign = null;
        $unduh = null;

        if ($kirim['ok'] && $kirim['kode_dokumen']) {
            $sign = $this->tandatanganiKode($kirim['kode_dokumen'], $passphrase);
            if ($sign['ok']) {
                $unduh = $this->unduhDokumen($kirim['kode_dokumen']);
            }
        }

        return [
            'kirim' => $kirim,
            'sign' => $sign,
            'unduh' => $unduh,
        ];
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function lanjutkanDenganKode(string $kodeDokumen, string $passphrase): array
    {
        if (config('tte.ds.demo', true)) {
            return $this->demo("%PDF-1.4\n% demo-tte\n%%EOF\n", $passphrase, $kodeDokumen);
        }

        $tte = $this->tandatanganiKode($kodeDokumen, $passphrase);
        if (! $tte['ok']) {
            return $tte;
        }

        return $this->unduhDokumen($kodeDokumen);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function demo(string $pdf, string $passphrase, ?string $kodeDokumen = null): array
    {
        if (! hash_equals(self::DEMO_PASSPHRASE, $passphrase)) {
            return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah', [
                'kode_dokumen' => $kodeDokumen,
            ]);
        }

        if (config('tte.ds.simulate_disconnect')) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus', [
                'kode_dokumen' => $kodeDokumen,
            ]);
        }

        return $this->ok('TTE berhasil (mode demo)', [
            'pdf' => $pdf,
            'kode_dokumen' => $kodeDokumen,
        ]);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function kirimKeDs(string $pdf, string $passphrase, ?string $nik, ?string $keterangan): array
    {
        $kirim = $this->kirimDokumen($pdf, (string) $nik, $keterangan);
        if (! $kirim['ok']) {
            return $kirim;
        }

        $kodeDokumen = $kirim['kode_dokumen'];
        if ($kodeDokumen === null) {
            return $this->gagal(self::GAGAL, 'DS tidak mengembalikan kode dokumen', [
                'http' => $kirim['http'],
                'body' => $kirim['body'],
                'json' => $kirim['json'] ?? null,
            ]);
        }

        $tte = $this->tandatanganiKode($kodeDokumen, $passphrase);
        if (! $tte['ok']) {
            return $tte;
        }

        return $this->unduhDokumen($kodeDokumen);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}|null
     */
    private function siapKirim(): ?array
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

        return null;
    }

    /**
     * @param  array<string, string>  $fields
     * @param  array{0: string, 1: string, 2: string}|null  $file
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function postMultipart(string $url, array $fields, ?array $file = null): array
    {
        try {
            $request = $this->http();
            if ($file !== null) {
                $request = $request->attach($file[0], $file[1], $file[2]);
            }
            $response = $request->post($url, $fields);
        } catch (ConnectionException $e) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus: '.$e->getMessage());
        } catch (\Throwable $e) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus: '.$e->getMessage());
        }

        return $this->hasilHttp($response->status(), $response->body(), $response->json());
    }

    /**
     * @param  array<string, string>  $fields
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function postForm(string $url, array $fields, string $langkah = 'umum'): array
    {
        try {
            $response = $this->http()->asForm()->post($url, $fields);
        } catch (ConnectionException $e) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus: '.$e->getMessage());
        } catch (\Throwable $e) {
            return $this->gagal(self::KONEKSI_PUTUS, 'Koneksi ke server DS terputus: '.$e->getMessage());
        }

        return $this->hasilHttp($response->status(), $response->body(), $response->json(), $langkah);
    }

    private function http(): PendingRequest
    {
        $token = (string) config('tte.ds.token');

        return Http::timeout((int) config('tte.ds.timeout', 30))
            ->withQueryParameters(['token' => $token]);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function hasilHttp(int $status, string $body, mixed $json, string $langkah = 'umum'): array
    {
        $pesan = $this->pesanDariJson($json);
        $extra = [
            'http' => $status,
            'body' => $body,
            'json' => $json,
            'kode_dokumen' => $this->kodeDariJson($json),
        ];

        if ($status === 401 || $status === 403) {
            if ($langkah === 'sign') {
                return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah', $extra);
            }

            return $this->gagal(self::GAGAL, $pesan !== '' ? $pesan : 'Akses ke DS ditolak', $extra);
        }

        $pdf = $this->pdfDariBody($body);
        $suksesJson = is_array($json) ? ($json['success'] ?? null) : null;
        if ($suksesJson === false || $suksesJson === 0 || $suksesJson === 'false' || $suksesJson === 'gagal') {
            if ($this->pesanPassphrase($pesan)) {
                return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah', $extra);
            }

            return $this->gagal(self::GAGAL, $pesan !== '' ? $pesan : 'Proses TTE di DS gagal', $extra);
        }

        if ($status >= 200 && $status < 300 && $pdf !== null) {
            return $this->ok($pesan !== '' ? $pesan : 'TTE berhasil', $extra + ['pdf' => $pdf]);
        }

        if ($status < 200 || $status >= 300) {
            if ($this->pesanPassphrase($pesan)) {
                return $this->gagal(self::PASSPHRASE_SALAH, 'Passphrase salah', $extra);
            }

            return $this->gagal(self::GAGAL, $pesan !== '' ? $pesan : 'Proses TTE di DS gagal', $extra);
        }

        return $this->ok($pesan !== '' ? $pesan : 'Berhasil', $extra + ['pdf' => $pdf]);
    }

    private function pesanDariJson(mixed $json): string
    {
        if (! is_array($json)) {
            return '';
        }

        $catatan = $json['data']['catatan'] ?? $json['catatan'] ?? null;
        if (is_array($catatan)) {
            $catatan = implode(' ', array_map('strval', $catatan));
        }
        if (is_string($catatan) && trim($catatan) !== '') {
            return trim($catatan);
        }

        foreach (['message', 'pesan', 'keterangan'] as $key) {
            $nilai = $json['data'][$key] ?? $json[$key] ?? null;
            if (is_string($nilai) && trim($nilai) !== '') {
                return trim($nilai);
            }
        }

        return '';
    }

    private function pesanPassphrase(string $pesan): bool
    {
        $lower = strtolower($pesan);

        return str_contains($lower, 'passphrase') || str_contains($lower, 'sandi');
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
     * @param  array<string, mixed>  $extra
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function ok(string $pesan, array $extra = []): array
    {
        return array_merge([
            'ok' => true,
            'kode' => self::OK,
            'pesan' => $pesan,
            'pdf' => null,
            'kode_dokumen' => null,
            'http' => null,
            'body' => null,
            'json' => null,
        ], $extra);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function gagal(string $kode, string $pesan, array $extra = []): array
    {
        return array_merge([
            'ok' => false,
            'kode' => $kode,
            'pesan' => $pesan,
            'pdf' => null,
            'kode_dokumen' => null,
            'http' => null,
            'body' => null,
            'json' => null,
        ], $extra);
    }
}
