<?php

namespace App\Services\Tte;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        $hasil = $this->postMultipart($this->dsUrl('ttdds'), [
            'nik' => $nik,
            'template' => (string) config('tte.ds.template'),
            'keterangan' => (string) $keterangan,
        ], ['pdf', $pdf, 'dokumen.pdf']);

        $kodeDokumen = $this->kodeDariJson($hasil['json'] ?? null);
        $hasil['kode_dokumen'] = $kodeDokumen;
        if ($kodeDokumen === null) {
            Log::warning('DS ttdds tanpa kode dokumen', [
                'http' => $hasil['http'],
                'pesan' => $hasil['pesan'],
                'body' => mb_substr((string) ($hasil['body'] ?? ''), 0, 400),
            ]);

            $pesan = (! $hasil['ok'] && trim((string) $hasil['pesan']) !== '')
                ? $hasil['pesan']
                : $this->pesanResponDs($hasil, 'DS tidak mengembalikan kode dokumen');

            return $this->gagal(
                $hasil['kode'] === self::KONEKSI_PUTUS ? self::KONEKSI_PUTUS : self::GAGAL,
                $pesan,
                [
                    'http' => $hasil['http'],
                    'body' => $hasil['body'],
                    'json' => $hasil['json'] ?? null,
                ]
            );
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

        $hasil = $this->postForm($this->dsUrl('sign'), [
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

        $hasil = $this->postForm($this->dsUrl('dokumen'), [
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

        return $this->ambilPdfSetelahTtd($kodeDokumen);
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

        return $this->ambilPdfSetelahTtd($kodeDokumen);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function ambilPdfSetelahTtd(string $kodeDokumen): array
    {
        $unduh = $this->unduhDokumen($kodeDokumen);
        if ($unduh['ok']) {
            return $unduh;
        }

        return $this->gagal(
            self::GAGAL,
            'TTD di DS berhasil, tetapi PDF belum bisa diunduh. Callback DS mengarah ke server, bukan ke lokal. '.$unduh['pesan'],
            [
                'kode_dokumen' => $kodeDokumen,
                'http' => $unduh['http'],
                'body' => $unduh['body'],
                'json' => $unduh['json'] ?? null,
            ]
        );
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}|null
     */
    private function siapKirim(): ?array
    {
        $base = $this->dsBaseUrl();
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

        return $this->hasilHttp($response->status(), $response->body(), $response->json(), 'ttdds');
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
            ->withoutRedirecting()
            ->withQueryParameters(['token' => $token]);
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string, pdf: string|null, kode_dokumen: string|null, http: int|null, body: string|null, json: mixed}
     */
    private function hasilHttp(int $status, string $body, mixed $json, string $langkah = 'umum'): array
    {
        $json = $this->jsonDariBody($body, $json);
        $pesan = $this->pesanDariJson($json);
        $extra = [
            'http' => $status,
            'body' => $body,
            'json' => $json,
            'kode_dokumen' => $this->kodeDariJson($json),
        ];

        if ($langkah === 'sign' && $this->ttdBerhasilCallbackGagal($pesan)) {
            return $this->ok(
                'TTD berhasil. Pengiriman balik ke callback aplikasi gagal (wajar di lokal).',
                $extra
            );
        }

        if ($extra['kode_dokumen']) {
            return $this->ok($pesan !== '' ? $pesan : 'Dokumen terkirim', $extra);
        }

        if ($this->halamanLoginDs($body) || in_array($status, [301, 302, 303, 307, 308], true)) {
            return $this->gagal(
                self::GAGAL,
                'Token DS tidak diterima (HTTP '.$status.'). Server mengembalikan halaman login. Isi TTE_DS_TOKEN dengan token yang sama seperti di Postman, bukan token Firebase.',
                $extra
            );
        }

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

    private function ttdBerhasilCallbackGagal(string $pesan): bool
    {
        $lower = mb_strtolower($pesan);

        return str_contains($lower, 'berhasil menanda')
            || (str_contains($lower, 'berhasil') && str_contains($lower, 'gagal mengirim'));
    }

    private function dsUrl(string $path): string
    {
        return $this->dsBaseUrl().'/'.ltrim($path, '/');
    }

    private function dsBaseUrl(): string
    {
        $base = rtrim((string) config('tte.ds.url'), '/');
        if ($base !== '' && ! str_ends_with($base, '/v2') && preg_match('#/api$#', $base) === 1) {
            $base .= '/v2';
        }

        return $base;
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private function kodeDariJson(mixed $json): ?string
    {
        if (is_string($json) && $json !== '') {
            $decoded = json_decode($json, true);
            $json = is_array($decoded) ? $decoded : null;
        }
        if (! is_array($json)) {
            return null;
        }

        foreach ([
            $json['data']['kode'] ?? null,
            $json['data']['kode_dokumen'] ?? null,
            $json['kode'] ?? null,
            $json['kode_dokumen'] ?? null,
        ] as $kode) {
            if (is_string($kode) && $kode !== '') {
                return $kode;
            }
            if (is_int($kode) || is_float($kode)) {
                return (string) $kode;
            }
        }

        return null;
    }

    private function jsonDariBody(string $body, mixed $json): mixed
    {
        if (is_array($json)) {
            return $json;
        }

        if (is_string($json) && $json !== '') {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $trim = ltrim($body);
        if ($trim !== '' && ($trim[0] === '{' || $trim[0] === '[')) {
            $decoded = json_decode($body, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $json;
    }

    private function halamanLoginDs(string $body): bool
    {
        $lower = mb_strtolower($body);

        return str_contains($lower, 'masuk - digital signature')
            || (str_contains($lower, '<title>masuk') && str_contains($lower, 'digital signature'));
    }

    /**
     * @param  array<string, mixed>  $hasil
     */
    private function pesanResponDs(array $hasil, string $awal): string
    {
        $body = (string) ($hasil['body'] ?? '');
        if ($this->halamanLoginDs($body)) {
            return 'Token DS tidak diterima (HTTP '.($hasil['http'] ?? '?').'). Server mengembalikan halaman login. Isi TTE_DS_TOKEN dengan token yang sama seperti di Postman, bukan token Firebase.';
        }

        $http = $hasil['http'] ?? null;
        $pesan = trim((string) ($hasil['pesan'] ?? ''));
        if ($pesan === '' && is_array($hasil['json'] ?? null)) {
            $pesan = (string) json_encode($hasil['json'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if ($pesan === '' && $body !== '') {
            $pesan = mb_substr(trim((string) preg_replace('/\s+/', ' ', strip_tags($body))), 0, 280);
        }

        $out = $awal;
        if ($http) {
            $out .= ' (HTTP '.$http.')';
        }
        if ($pesan !== '') {
            $out .= ': '.$pesan;
        }

        return $out;
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
