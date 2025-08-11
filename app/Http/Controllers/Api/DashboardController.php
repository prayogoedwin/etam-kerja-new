<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kabkota;
use App\Models\UserPencari;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function pencari(Request $request)
    {
        try {
            // Ambil kabupaten/kota dengan province_id = 64 saja
            $semuaKabkota = Kabkota::where('province_id', 64)
                ->orderBy('name', 'asc')
                ->get();
            
            // Query untuk mendapatkan data pencari kerja berdasarkan gender dan kota
            // Hanya dari kabkota dengan province_id = 64
            $dataPencariKerja = UserPencari::select('id_kota', 'gender', DB::raw('count(*) as jumlah'))
                ->whereIn('id_kota', $semuaKabkota->pluck('id'))
                ->groupBy('id_kota', 'gender')
                ->get();

            // Buat array untuk menyimpan hasil
            $result = [];
            
            // Loop semua kabkota untuk memastikan semuanya muncul
            foreach ($semuaKabkota as $kabkota) {
                $result[] = [
                    'id_kota' => $kabkota->id,
                    'nama_kota' => $kabkota->name,
                    'pria' => 0,
                    'wanita' => 0,
                    'total' => 0
                ];
            }

            // Update data dengan jumlah pencari kerja yang ada
            foreach ($dataPencariKerja as $data) {
                // Cari index kabkota dalam result array
                $index = array_search($data->id_kota, array_column($result, 'id_kota'));
                
                if ($index !== false) {
                    if ($data->gender == 'L' || $data->gender == 'Laki-laki' || $data->gender == 'pria') {
                        $result[$index]['pria'] = $data->jumlah;
                    } elseif ($data->gender == 'P' || $data->gender == 'Perempuan' || $data->gender == 'wanita') {
                        $result[$index]['wanita'] = $data->jumlah;
                    }
                    
                    // Hitung ulang total
                    $result[$index]['total'] = $result[$index]['pria'] + $result[$index]['wanita'];
                }
            }

            // Hitung total pria dan wanita keseluruhan
            $totalPria = array_sum(array_column($result, 'pria'));
            $totalWanita = array_sum(array_column($result, 'wanita'));
            $totalKeseluruhan = $totalPria + $totalWanita;

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $result,
                'summary' => [
                    'total_kabkota' => count($result),
                    'total_pria' => $totalPria,
                    'total_wanita' => $totalWanita,
                    'total_keseluruhan' => $totalKeseluruhan,
                    'persentase_pria' => $totalKeseluruhan > 0 ? round(($totalPria / $totalKeseluruhan) * 100, 2) : 0,
                    'persentase_wanita' => $totalKeseluruhan > 0 ? round(($totalWanita / $totalKeseluruhan) * 100, 2) : 0
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error mengambil data pencari kerja: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}