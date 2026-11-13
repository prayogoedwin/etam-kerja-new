<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Tte\TteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DsCallbackController extends Controller
{
    public function __construct(private TteService $tte) {}

    public function terima(Request $request): JsonResponse
    {
        $kode = $this->kodeDariRequest($request);
        $pdf = $this->pdfDariRequest($request);

        if ($kode === null || $pdf === null) {
            Log::warning('DS callback terima tidak lengkap', [
                'kode' => $kode,
                'ada_pdf' => $pdf !== null,
                'keys' => array_keys($request->all()),
            ]);

            return response()->json(['status' => 'Gagal', 'message' => 'kode atau berkas PDF tidak ada'], 422);
        }

        $hasil = $this->tte->terimaBerkasDs($kode, $pdf);

        return response()->json([
            'status' => $hasil['ok'] ? 'Berhasil' : 'Gagal',
            'message' => $hasil['pesan'],
        ], $hasil['ok'] ? 200 : 422);
    }

    public function tolak(Request $request): JsonResponse
    {
        $kode = $this->kodeDariRequest($request);
        if ($kode === null) {
            return response()->json(['status' => 'Gagal', 'message' => 'kode tidak ada'], 422);
        }

        $alasan = trim((string) (
            $request->input('keterangan')
            ?? $request->input('alasan')
            ?? $request->input('catatan')
            ?? $request->input('message')
            ?? ''
        ));

        $hasil = $this->tte->tolakBerkasDs($kode, $alasan);

        return response()->json([
            'status' => $hasil['ok'] ? 'Berhasil' : 'Gagal',
            'message' => $hasil['pesan'],
        ], $hasil['ok'] ? 200 : 422);
    }

    private function kodeDariRequest(Request $request): ?string
    {
        foreach (['kode', 'kode_dokumen', 'kodedokumen'] as $key) {
            $nilai = $request->input($key);
            if (is_string($nilai) && trim($nilai) !== '') {
                return trim($nilai);
            }
        }

        return null;
    }

    private function pdfDariRequest(Request $request): ?string
    {
        foreach (['signpdf', 'pdf', 'dokumen', 'file'] as $key) {
            $file = $request->file($key);
            if ($file && $file->isValid()) {
                $isi = file_get_contents($file->getRealPath());
                if (is_string($isi) && str_starts_with($isi, '%PDF')) {
                    return $isi;
                }
            }
        }

        $mentah = $request->getContent();
        if (is_string($mentah) && str_starts_with($mentah, '%PDF')) {
            return $mentah;
        }

        return null;
    }
}
