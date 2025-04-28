<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lowongan;
use App\Models\Pendidikan;
use App\Models\Kabkota;
use Illuminate\Support\Facades\Log;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Lowongan::with('pendidikan', 'kabkota'); // include relasi

            // Filter dinamis berdasarkan input
            if ($request->filled('judul_lowongan')) {
                $query->where('judul_lowongan', 'like', '%' . $request->judul_lowongan . '%');
            }

            if ($request->filled('pendidikan')) {
                $query->where('pendidikan_id', $request->pendidikan);
            }

            if ($request->filled('lokasi_perusahaan')) {
                $query->where('lokasi_penempatan_text', 'like', '%' . $request->lokasi_perusahaan . '%');
            }

            $query->where('status_id', 1);
            $query->where('is_lowongan_disabilitas', 0);

            // $lowongans = $query->get();
            $query->whereNull('deleted_at');

            $query->orderBy('tanggal_start', 'desc');

            $lowongans = $query->limit(50)->get();


            if ($lowongans->isEmpty()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Data tidak ditemukan',
                    'data' => []
                ]);
            }

            // Format data
            $result = $lowongans->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul_lowongan' => $item->judul_lowongan,
                    'pendidikan' => [
                        'id' => $item->pendidikan_id,
                        'pendidikan' => optional($item->pendidikan)->name ?? '-',
                    ],
                    'lokasi_perusahaan' => [
                        'id' => $item->kabkota_id,
                        'kabkota' => optional($item->kabkota)->name ?? '-',
                    ],
                    'tanggal_buka' => $item->tanggal_start,
                    'tanggal_tutup' => $item->tanggal_end,
                    'kisaran_gaji_mulai' => $item->kisaran_gaji,
                    'kisaran_gaji_sampai' => $item->kisaran_gaji_akhir,
                    'kebutuhan_pria' => $item->jumlah_pria,
                    'kebutuhan_wanita' => $item->jumlah_wanita,
                    'deskripsi' => $item->deskripsi,
                    'link_lowongan_kerja' => 'https://etamkerja.kaltimprov.go.id/depan/lowongan-detail/' . encode_url($item->id),
                ];
            });

            return response()->json([
                'status' => 1,
                'message' => 'Berhasil get data',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            // log error ke laravel.log
            Log::error('Error get lowongan: ' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => 'Gagal get data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
