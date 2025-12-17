<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPencari;
use App\Models\UserPenyedia;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardEksekutifKabkotaController extends Controller
{
    /**
     * Get all dashboard data for Kabkota level
     * 
     * @param int|null $kabkotaId - ID kabkota dari URL (untuk admin provinsi)
     */
    public function index($kabkotaId = null)
    {
        $user = Auth::user();
        
        // Cek role dan tentukan kabkota_id
        if ($user->hasAnyRole(['super-admin', 'admin-provinsi', 'eksekutif-provinsi'])) {
            // Admin provinsi: bisa akses kabkota manapun via parameter
            if (!$kabkotaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter kabkota_id diperlukan'
                ], 400);
            }
        } else {
            // Admin kabkota: hanya bisa akses kabkotanya sendiri
            $kabkotaId = $user->admin->kabkota_id;
        }
        
        // Validasi kabkota exists
        $kabkota = DB::table('etam_kabkota')->where('id', $kabkotaId)->first();
        if (!$kabkota) {
            return response()->json([
                'success' => false,
                'message' => 'Kabupaten/Kota tidak ditemukan'
            ], 404);
        }
        
        $namaKabkota = $kabkota->name;

        return response()->json([
            'success' => true,
            'data' => [
                'kabkota_id' => $kabkotaId,
                'nama_kabkota' => $namaKabkota,
                'pencari' => $this->getPencariStats($kabkotaId),
                'pencari_diterima' => $this->getPencariDiterimaStats($kabkotaId),
                'penyedia' => $this->getPenyediaStats($kabkotaId),
                'lowongan' => $this->getLowonganStats($kabkotaId),
                'pendidikan' => $this->getTopPendidikan($kabkotaId),
                'jurusan' => $this->getTopJurusan($kabkotaId),
                'sektor' => $this->getTopSektor($kabkotaId),
                'sektor_perusahaan' => $this->getTopSektorPerusahaan($kabkotaId),
                'generated_at' => now()->format('d M Y H:i:s'),
            ]
        ]);
    }

    private function getPencariStats($kabkotaId)
    {
        $baseQuery = UserPencari::whereNull('deleted_at')
            ->where('is_diterima', 0)
            ->where('id_kota', $kabkotaId);

        $total = (clone $baseQuery)->count();

        $genderStats = (clone $baseQuery)
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender')
            ->toArray();

        $lakiLaki = $genderStats['L'] ?? $genderStats['Laki-laki'] ?? $genderStats['1'] ?? 0;
        $perempuan = $genderStats['P'] ?? $genderStats['Perempuan'] ?? $genderStats['2'] ?? 0;

        $topKecamatan = (clone $baseQuery)
            ->whereNotNull('users_pencari.id_kecamatan')
            ->join('etam_kecamatan', 'users_pencari.id_kecamatan', '=', 'etam_kecamatan.id')
            ->select('etam_kecamatan.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_kecamatan.id', 'etam_kecamatan.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'total' => $total,
            'laki_laki' => $lakiLaki,
            'perempuan' => $perempuan,
            'top_kecamatan' => $topKecamatan,
        ];
    }

    private function getPencariDiterimaStats($kabkotaId)
    {
        $totalDiterima = UserPencari::whereNull('deleted_at')
            ->where('is_diterima', 1)
            ->where('id_kota', $kabkotaId)
            ->count();

        $totalDitempatkan = DB::table('etam_lamaran')
            ->whereNull('etam_lamaran.deleted_at')
            ->where('etam_lamaran.progres_id', 3)
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id')
            ->where('users_pencari.id_kota', $kabkotaId)
            ->whereNull('users_pencari.deleted_at')
            ->count();

        return [
            'total_diterima' => $totalDiterima,
            'total_ditempatkan' => $totalDitempatkan,
        ];
    }

    private function getPenyediaStats($kabkotaId)
    {
        $baseQuery = UserPenyedia::whereNull('deleted_at')
            ->where('id_kota', $kabkotaId);

        $total = (clone $baseQuery)->count();

        $topKecamatan = (clone $baseQuery)
            ->whereNotNull('users_penyedia.id_kecamatan')
            ->join('etam_kecamatan', 'users_penyedia.id_kecamatan', '=', 'etam_kecamatan.id')
            ->select('etam_kecamatan.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_kecamatan.id', 'etam_kecamatan.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'total' => $total,
            'top_kecamatan' => $topKecamatan,
        ];
    }

    private function getLowonganStats($kabkotaId)
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $baseQuery = Lowongan::whereNull('deleted_at')
            ->where('status_id', 1)
            ->where('tanggal_end', '>=', $today)
            ->where('kabkota_id', $kabkotaId);

        $total = (clone $baseQuery)->count();

        $kebutuhan = (clone $baseQuery)
            ->select(
                DB::raw('COALESCE(SUM(jumlah_pria), 0) as total_pria'),
                DB::raw('COALESCE(SUM(jumlah_wanita), 0) as total_wanita')
            )
            ->first();

        $lamaranProses = DB::table('etam_lamaran')
            ->whereNull('etam_lamaran.deleted_at')
            ->whereIn('etam_lamaran.progres_id', [1, 2, 4])
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->whereNull('etam_lowongan.deleted_at')
            ->where('etam_lowongan.status_id', 1)
            ->where('etam_lowongan.tanggal_end', '>=', $today)
            ->where('etam_lowongan.kabkota_id', $kabkotaId)
            ->count();

        $lamaranDetail = DB::table('etam_lamaran')
            ->whereNull('etam_lamaran.deleted_at')
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->whereNull('etam_lowongan.deleted_at')
            ->where('etam_lowongan.status_id', 1)
            ->where('etam_lowongan.tanggal_end', '>=', $today)
            ->where('etam_lowongan.kabkota_id', $kabkotaId)
            ->join('etam_progres', 'etam_lamaran.progres_id', '=', 'etam_progres.id')
            ->select('etam_progres.name as status', 'etam_progres.kode', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_progres.id', 'etam_progres.name', 'etam_progres.kode')
            ->orderBy('etam_progres.kode')
            ->get();

        return [
            'total' => $total,
            'kebutuhan_pria' => $kebutuhan->total_pria ?? 0,
            'kebutuhan_wanita' => $kebutuhan->total_wanita ?? 0,
            'lamaran_proses' => $lamaranProses,
            'lamaran_detail' => $lamaranDetail,
        ];
    }

    private function getTopPendidikan($kabkotaId)
    {
        return UserPencari::whereNull('users_pencari.deleted_at')
            ->where('users_pencari.is_diterima', 0)
            ->where('users_pencari.id_kota', $kabkotaId)
            ->whereNotNull('users_pencari.id_pendidikan')
            ->join('etam_pendidikan', 'users_pencari.id_pendidikan', '=', 'etam_pendidikan.id')
            ->select('etam_pendidikan.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_pendidikan.id', 'etam_pendidikan.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    private function getTopJurusan($kabkotaId)
    {
        return UserPencari::whereNull('users_pencari.deleted_at')
            ->where('users_pencari.is_diterima', 0)
            ->where('users_pencari.id_kota', $kabkotaId)
            ->whereNotNull('users_pencari.id_jurusan')
            ->join('etam_jurusan', 'users_pencari.id_jurusan', '=', 'etam_jurusan.id')
            ->select('etam_jurusan.nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_jurusan.id', 'etam_jurusan.nama')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    private function getTopSektor($kabkotaId)
    {
        $today = Carbon::now()->format('Y-m-d');

        return Lowongan::whereNull('etam_lowongan.deleted_at')
            ->where('etam_lowongan.status_id', 1)
            ->where('etam_lowongan.tanggal_end', '>=', $today)
            ->where('etam_lowongan.kabkota_id', $kabkotaId)
            ->whereNotNull('etam_lowongan.sektor_id')
            ->join('etam_sektor', 'etam_lowongan.sektor_id', '=', 'etam_sektor.id')
            ->select('etam_sektor.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_sektor.id', 'etam_sektor.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    private function getTopSektorPerusahaan($kabkotaId)
    {
        return UserPenyedia::whereNull('users_penyedia.deleted_at')
            ->where('users_penyedia.id_kota', $kabkotaId)
            ->whereNotNull('users_penyedia.id_sektor')
            ->join('etam_sektor', 'users_penyedia.id_sektor', '=', 'etam_sektor.id')
            ->select('etam_sektor.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_sektor.id', 'etam_sektor.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
}