<?php

namespace App\Http\Controllers\Tte;

use App\Http\Controllers\Controller;
use App\Models\EtamTteDsUji;
use App\Services\Tte\DsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TteUjiDsController extends Controller
{
    public function index()
    {
        $ujiId = session('uji_id');

        return view('backend.tte.uji-ds', [
            'nik' => Auth::user()?->nikSiapTte(),
            'template' => (string) config('tte.ds.template'),
            'uji' => $ujiId ? EtamTteDsUji::query()->find($ujiId) : null,
            'daftar' => EtamTteDsUji::query()->latest('id')->limit(20)->get(),
        ]);
    }

    public function kirim(Request $request, DsClient $ds)
    {
        $data = $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'nik' => ['required', 'digits:16'],
            'passphrase' => ['required', 'string'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('pdf');
        $path = $file->store('tte/uji', 'local');
        $pdf = (string) Storage::disk('local')->get($path);

        $hasil = $ds->ujiTigaLangkah(
            $pdf,
            $data['nik'],
            $data['passphrase'],
            $data['keterangan'] ?? 'Uji DS ETAM'
        );

        $kirim = $hasil['kirim'];
        $sign = $hasil['sign'];
        $unduh = $hasil['unduh'];

        $pathAfter = null;
        if (($unduh['ok'] ?? false) && ! empty($unduh['pdf'])) {
            $pathAfter = 'tte/uji/after-'.uniqid('', true).'.pdf';
            Storage::disk('local')->put($pathAfter, $unduh['pdf']);
        }

        $uji = EtamTteDsUji::create([
            'user_id' => Auth::id(),
            'nik' => $data['nik'],
            'nama_file' => $file->getClientOriginalName(),
            'path_file' => $path,
            'template' => (string) config('tte.ds.template'),
            'keterangan' => $data['keterangan'] ?? null,
            'kode_ds' => $kirim['kode_dokumen'] ?? $sign['kode_dokumen'] ?? null,
            'kirim_ok' => (bool) ($kirim['ok'] ?? false),
            'kirim_http' => $kirim['http'] ?? null,
            'kirim_pesan' => $kirim['pesan'] ?? null,
            'kirim_body' => $this->cuplikanRespon($kirim),
            'sign_ok' => $sign['ok'] ?? null,
            'sign_http' => $sign['http'] ?? null,
            'sign_pesan' => $sign['pesan'] ?? null,
            'sign_body' => $this->cuplikanRespon($sign),
            'unduh_ok' => $unduh['ok'] ?? null,
            'unduh_http' => $unduh['http'] ?? null,
            'unduh_pesan' => $unduh['pesan'] ?? null,
            'unduh_body' => $this->cuplikanRespon($unduh),
            'path_file_after' => $pathAfter,
        ]);

        return redirect()
            ->route('tte.uji-ds')
            ->with('uji_id', $uji->id);
    }

    public function berkas(EtamTteDsUji $uji)
    {
        $path = $uji->path_file_after ?: $uji->path_file;
        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->streamDownload(function () use ($path) {
            echo Storage::disk('local')->get($path);
        }, $uji->nama_file ?: 'uji-ds.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $hasil
     */
    private function cuplikanRespon(?array $hasil): ?string
    {
        if ($hasil === null) {
            return null;
        }

        $json = $hasil['json'] ?? null;
        if (is_array($json)) {
            return (string) json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        $body = (string) ($hasil['body'] ?? '');
        if ($body === '') {
            return $hasil['pesan'] ?? null;
        }
        if (str_starts_with($body, '%PDF')) {
            return '[berkas PDF '.strlen($body).' byte]';
        }

        return mb_strlen($body) > 8000 ? mb_substr($body, 0, 8000)."\n… (terpotong)" : $body;
    }
}
