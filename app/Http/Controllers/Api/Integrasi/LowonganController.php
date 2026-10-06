<?php

namespace App\Http\Controllers\Api\Integrasi;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LowonganController extends Controller
{
    public function publik(Request $request)
    {
        try {
            $query = Lowongan::with(['pendidikan', 'kabkota', 'userPenyedia', 'jabatan'])
                ->where('status_id', 1)
                ->whereNull('deleted_at')
                ->orderByDesc('tanggal_start')
                ->limit(10);

            if ($request->filled('kabkota_id')) {
                $query->where('kabkota_id', $request->kabkota_id);
            }

            $items = $query->get();

            $data = $items->map(fn ($item) => $this->formatLowongan($item));

            return response()->json([
                'status' => true,
                'message' => 'Berhasil get data lowongan publik (maks 10)',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('API integrasi lowongan publik: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal get data lowongan',
                'data' => null,
            ], 500);
        }
    }

    public function internal(Request $request)
    {
        try {
            $idIntegration = $request->attributes->get('id_integration');
            $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

            $query = Lowongan::with(['pendidikan', 'kabkota', 'userPenyedia', 'jabatan'])
                ->where('status_id', 1)
                ->whereNull('deleted_at')
                ->orderByDesc('tanggal_start');

            if ($request->filled('kabkota_id')) {
                $query->where('kabkota_id', $request->kabkota_id);
            }

            if ($request->filled('judul')) {
                $query->where('judul_lowongan', 'like', '%'.$request->judul.'%');
            }

            $paginator = $query->paginate($perPage);

            $data = collect($paginator->items())->map(fn ($item) => $this->formatLowongan($item));

            return response()->json([
                'status' => true,
                'message' => 'Berhasil get data lowongan internal',
                'id_integration' => $idIntegration,
                'data' => $data,
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('API integrasi lowongan internal: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal get data lowongan',
                'data' => null,
            ], 500);
        }
    }

    private function formatLowongan(Lowongan $item): array
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $logo = optional($item->userPenyedia)->foto
            ? $baseUrl.'/storage/'.$item->userPenyedia->foto
            : $baseUrl.'/assets/etam_be/images/user/avatar-x.png';

        return [
            'id' => $item->id,
            'judul_lowongan' => $item->judul_lowongan,
            'jabatan' => optional($item->jabatan)->nama,
            'pendidikan' => optional($item->pendidikan)->name,
            'kabkota' => optional($item->kabkota)->name,
            'nama_perusahaan' => optional($item->userPenyedia)->name ?? $item->nama_perusahaan_bybkk,
            'logo_perusahaan' => $logo,
            'lokasi_penempatan' => $item->lokasi_penempatan_text,
            'tanggal_buka' => $item->tanggal_start,
            'tanggal_tutup' => $item->tanggal_end,
            'kisaran_gaji_mulai' => $item->kisaran_gaji,
            'kisaran_gaji_sampai' => $item->kisaran_gaji_akhir,
            'kebutuhan_pria' => $item->jumlah_pria,
            'kebutuhan_wanita' => $item->jumlah_wanita,
            'deskripsi' => $item->deskripsi,
            'is_lowongan_disabilitas' => $item->is_lowongan_disabilitas,
            'url_redirect' => $baseUrl.'/depan/lowongan-detail/'.encode_url($item->id),
        ];
    }
}
