<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPencari;
use App\Models\UserPenyedia;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardEksekutifController extends Controller
{
    /**
     * Get all dashboard data
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pencari' => $this->getPencariStats(),
                'pencari_diterima' => $this->getPencariDiterimaStats(),
                'penyedia' => $this->getPenyediaStats(),
                'lowongan' => $this->getLowonganStats(),
                'pendidikan' => $this->getTopPendidikan(),
                'jurusan' => $this->getTopJurusan(),
                'sektor' => $this->getTopSektor(),
                'sektor_perusahaan' => $this->getTopSektorPerusahaan(),
                'generated_at' => now()->format('d M Y H:i:s'),
            ]
        ]);
    }

    /**
     * Get pencari kerja statistics
     */
    private function getPencariStats()
    {
        $baseQuery = UserPencari::whereNull('deleted_at')
            ->where('is_diterima', 0);

        $total = (clone $baseQuery)->count();

        $genderStats = (clone $baseQuery)
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender')
            ->toArray();

        $lakiLaki = $genderStats['L'] ?? $genderStats['Laki-laki'] ?? $genderStats['1'] ?? 0;
        $perempuan = $genderStats['P'] ?? $genderStats['Perempuan'] ?? $genderStats['2'] ?? 0;

        $topKota = (clone $baseQuery)
            ->join('etam_kabkota', 'users_pencari.id_kota', '=', 'etam_kabkota.id')
            ->select('etam_kabkota.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_kabkota.id', 'etam_kabkota.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'total' => $total,
            'laki_laki' => $lakiLaki,
            'perempuan' => $perempuan,
            'top_kota' => $topKota,
        ];
    }

    /**
     * Get pencari yang sudah diterima kerja
     */
    private function getPencariDiterimaStats()
    {
        $totalDiterima = UserPencari::whereNull('deleted_at')
            ->where('is_diterima', 1)
            ->count();

        $totalDitempatkan = DB::table('etam_lamaran')
            ->whereNull('etam_lamaran.deleted_at')
            ->where('etam_lamaran.progres_id', 3)
            ->count();

        return [
            'total_diterima' => $totalDiterima,
            'total_ditempatkan' => $totalDitempatkan,
        ];
    }

    /**
     * Get penyedia/perusahaan statistics
     */
    private function getPenyediaStats()
    {
        $baseQuery = UserPenyedia::whereNull('deleted_at');

        $total = (clone $baseQuery)->count();

        $topKota = (clone $baseQuery)
            ->join('etam_kabkota', 'users_penyedia.id_kota', '=', 'etam_kabkota.id')
            ->select('etam_kabkota.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_kabkota.id', 'etam_kabkota.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'total' => $total,
            'top_kota' => $topKota,
        ];
    }

    /**
     * Get lowongan statistics
     */
    private function getLowonganStats()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $baseQuery = Lowongan::whereNull('deleted_at')
            ->where('status_id', 1)
            ->where('tanggal_end', '>=', $today);

        $total = (clone $baseQuery)->count();

        $kebutuhan = (clone $baseQuery)
            ->select(
                DB::raw('COALESCE(SUM(jumlah_pria), 0) as total_pria'),
                DB::raw('COALESCE(SUM(jumlah_wanita), 0) as total_wanita')
            )
            ->first();

        $topKota = (clone $baseQuery)
            ->join('etam_kabkota', 'etam_lowongan.kabkota_id', '=', 'etam_kabkota.id')
            ->select('etam_kabkota.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_kabkota.id', 'etam_kabkota.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $lamaranProses = DB::table('etam_lamaran')
            ->whereNull('etam_lamaran.deleted_at')
            ->whereIn('etam_lamaran.progres_id', [1, 2, 4])
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->whereNull('etam_lowongan.deleted_at')
            ->where('etam_lowongan.status_id', 1)
            ->where('etam_lowongan.tanggal_end', '>=', $today)
            ->count();

        $lamaranDetail = DB::table('etam_lamaran')
            ->whereNull('etam_lamaran.deleted_at')
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->whereNull('etam_lowongan.deleted_at')
            ->where('etam_lowongan.status_id', 1)
            ->where('etam_lowongan.tanggal_end', '>=', $today)
            ->join('etam_progres', 'etam_lamaran.progres_id', '=', 'etam_progres.id')
            ->select('etam_progres.name as status', 'etam_progres.kode', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_progres.id', 'etam_progres.name', 'etam_progres.kode')
            ->orderBy('etam_progres.kode')
            ->get();

        return [
            'total' => $total,
            'kebutuhan_pria' => $kebutuhan->total_pria ?? 0,
            'kebutuhan_wanita' => $kebutuhan->total_wanita ?? 0,
            'top_kota' => $topKota,
            'lamaran_proses' => $lamaranProses,
            'lamaran_detail' => $lamaranDetail,
        ];
    }

    /**
     * Get top 5 pendidikan
     */
    private function getTopPendidikan()
    {
        return UserPencari::whereNull('users_pencari.deleted_at')
            ->where('users_pencari.is_diterima', 0)
            ->whereNotNull('users_pencari.id_pendidikan')
            ->join('etam_pendidikan', 'users_pencari.id_pendidikan', '=', 'etam_pendidikan.id')
            ->select('etam_pendidikan.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_pendidikan.id', 'etam_pendidikan.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    /**
     * Get top 5 jurusan
     */
    private function getTopJurusan()
    {
        return UserPencari::whereNull('users_pencari.deleted_at')
            ->where('users_pencari.is_diterima', 0)
            ->whereNotNull('users_pencari.id_jurusan')
            ->join('etam_jurusan', 'users_pencari.id_jurusan', '=', 'etam_jurusan.id')
            ->select('etam_jurusan.nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_jurusan.id', 'etam_jurusan.nama')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    /**
     * Get top 5 sektor lowongan
     */
    private function getTopSektor()
    {
        $today = Carbon::now()->format('Y-m-d');

        return Lowongan::whereNull('etam_lowongan.deleted_at')
            ->where('etam_lowongan.status_id', 1)
            ->where('etam_lowongan.tanggal_end', '>=', $today)
            ->whereNotNull('etam_lowongan.sektor_id')
            ->join('etam_sektor', 'etam_lowongan.sektor_id', '=', 'etam_sektor.id')
            ->select('etam_sektor.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_sektor.id', 'etam_sektor.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    /**
     * Get top 5 sektor perusahaan
     */
    private function getTopSektorPerusahaan()
    {
        return UserPenyedia::whereNull('users_penyedia.deleted_at')
            ->whereNotNull('users_penyedia.id_sektor')
            ->join('etam_sektor', 'users_penyedia.id_sektor', '=', 'etam_sektor.id')
            ->select('etam_sektor.name as nama', DB::raw('COUNT(*) as total'))
            ->groupBy('etam_sektor.id', 'etam_sektor.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
}