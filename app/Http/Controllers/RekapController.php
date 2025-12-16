<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPencari;
use App\Models\Lowongan;
use App\Models\Lamaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    // public function ak3titik1(){
    //     return view('backend.rekap.ak3.satu');
    // }

    /**
     * IPK III/1: IKHTISAR STATISTIK IPK
     */
    public function ak3titik1(Request $request)
    {
        // Get bulan dan tahun dari request, default bulan ini
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Buat tanggal awal dan akhir bulan yang dipilih
        $tanggalAwalBulanIni = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhirBulanIni = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Bulan lalu
        $tanggalAwalBulanLalu = Carbon::create($tahun, $bulan, 1)->subMonth()->startOfMonth();
        $tanggalAkhirBulanLalu = Carbon::create($tahun, $bulan, 1)->subMonth()->endOfMonth();

        // =====================================================
        // I. PENCARI KERJA
        // =====================================================

        // Definisi kelompok umur
        $kelompokUmur = [
            '15-19' => ['min' => 15, 'max' => 19],
            '20-29' => ['min' => 20, 'max' => 29],
            '30-44' => ['min' => 30, 'max' => 44],
            '45-54' => ['min' => 45, 'max' => 54],
            '55+'   => ['min' => 55, 'max' => 150],
        ];

        // 1. Pencari kerja yang belum ditempatkan pada bulan yang lalu
        $pencari1 = $this->getPencariKerjaByStatus(
            'belum_ditempatkan',
            $tanggalAkhirBulanLalu,
            null,
            $kelompokUmur
        );

        // 2. Pencari kerja yang terdaftar pada bulan ini
        $pencari2 = $this->getPencariKerjaTerdaftar(
            $tanggalAwalBulanIni,
            $tanggalAkhirBulanIni,
            $kelompokUmur
        );

        // A. JUMLAH (1+2)
        $pencariA = $this->sumPencariData($pencari1, $pencari2);

        // 3. Pencari kerja yang ditempatkan pada bulan ini
        $pencari3 = $this->getPencariKerjaDitempatkan(
            $tanggalAwalBulanIni,
            $tanggalAkhirBulanIni,
            $kelompokUmur
        );

        // 4. Pencari kerja yang dihapuskan pada bulan ini
        $pencari4 = $this->getPencariKerjaDihapus(
            $tanggalAwalBulanIni,
            $tanggalAkhirBulanIni,
            $kelompokUmur
        );

        // B. JUMLAH (3+4)
        $pencariB = $this->sumPencariData($pencari3, $pencari4);

        // 5. Pencari kerja yang belum ditempatkan pada akhir bulan ini (A-B)
        $pencari5 = $this->subtractPencariData($pencariA, $pencariB);

        // =====================================================
        // II. LOWONGAN
        // =====================================================

        // 1. Lowongan yang belum dipenuhi pada akhir bulan lalu
        $lowongan1 = $this->getLowonganBelumDipenuhi($tanggalAkhirBulanLalu);

        // 2. Lowongan yang terdaftar bulan ini
        $lowongan2 = $this->getLowonganTerdaftar($tanggalAwalBulanIni, $tanggalAkhirBulanIni);

        // C. JUMLAH (1+2)
        $lowonganC = [
            'L' => $lowongan1['L'] + $lowongan2['L'],
            'W' => $lowongan1['W'] + $lowongan2['W'],
            'total' => $lowongan1['total'] + $lowongan2['total'],
        ];

        // 3. Lowongan yang dipenuhi bulan ini
        $lowongan3 = $this->getLowonganDipenuhi($tanggalAwalBulanIni, $tanggalAkhirBulanIni);

        // 4. Lowongan yang dihapuskan bulan ini
        $lowongan4 = $this->getLowonganDihapus($tanggalAwalBulanIni, $tanggalAkhirBulanIni);

        // D. JUMLAH (3+4)
        $lowonganD = [
            'L' => $lowongan3['L'] + $lowongan4['L'],
            'W' => $lowongan3['W'] + $lowongan4['W'],
            'total' => $lowongan3['total'] + $lowongan4['total'],
        ];

        // 5. Lowongan yang belum dipenuhi akhir bulan ini (C-D)
        $lowongan5 = [
            'L' => $lowonganC['L'] - $lowonganD['L'],
            'W' => $lowonganC['W'] - $lowonganD['W'],
            'total' => $lowonganC['total'] - $lowonganD['total'],
        ];

        // Data untuk view
        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $this->getNamaBulan($bulan),
            'kelompokUmur' => array_keys($kelompokUmur),
            'pencari' => [
                '1' => $pencari1,
                '2' => $pencari2,
                'A' => $pencariA,
                '3' => $pencari3,
                '4' => $pencari4,
                'B' => $pencariB,
                '5' => $pencari5,
            ],
            'lowongan' => [
                '1' => $lowongan1,
                '2' => $lowongan2,
                'C' => $lowonganC,
                '3' => $lowongan3,
                '4' => $lowongan4,
                'D' => $lowonganD,
                '5' => $lowongan5,
            ],
        ];

        return view('backend.rekap.ak3.satu', $data);
    }

    /**
     * Get pencari kerja yang belum ditempatkan sampai tanggal tertentu
     */
    private function getPencariKerjaByStatus($status, $sampaiTanggal, $dariTanggal = null, $kelompokUmur = [])
    {
        $result = $this->initPencariResult($kelompokUmur);

        // Query pencari yang terdaftar dan belum ditempatkan
        $query = UserPencari::query()
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $sampaiTanggal);

        if ($dariTanggal) {
            $query->where('created_at', '>=', $dariTanggal);
        }

        // Filter yang belum ditempatkan (is_diterima != 1 atau null)
        if ($status === 'belum_ditempatkan') {
            $query->where(function ($q) {
                $q->whereNull('is_diterima')
                  ->orWhere('is_diterima', '!=', 1);
            });
        }

        $pencaris = $query->get();

        foreach ($pencaris as $pencari) {
            $umur = $this->hitungUmur($pencari->tanggal_lahir, $sampaiTanggal);
            $gender = strtoupper($pencari->gender) === 'L' ? 'L' : 'P';
            $kelompok = $this->getKelompokUmur($umur, $kelompokUmur);

            if ($kelompok) {
                $result[$kelompok][$gender]++;
                $result['jumlah'][$gender]++;
                $result['jumlah']['total']++;
            }
        }

        return $result;
    }

    /**
     * Get pencari kerja yang terdaftar pada periode tertentu
     */
    private function getPencariKerjaTerdaftar($dariTanggal, $sampaiTanggal, $kelompokUmur)
    {
        $result = $this->initPencariResult($kelompokUmur);

        $pencaris = UserPencari::query()
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
            ->get();

        foreach ($pencaris as $pencari) {
            $umur = $this->hitungUmur($pencari->tanggal_lahir, $sampaiTanggal);
            $gender = strtoupper($pencari->gender) === 'L' ? 'L' : 'P';
            $kelompok = $this->getKelompokUmur($umur, $kelompokUmur);

            if ($kelompok) {
                $result[$kelompok][$gender]++;
                $result['jumlah'][$gender]++;
                $result['jumlah']['total']++;
            }
        }

        return $result;
    }

    /**
     * Get pencari kerja yang ditempatkan pada periode tertentu
     */
    private function getPencariKerjaDitempatkan($dariTanggal, $sampaiTanggal, $kelompokUmur)
    {
        $result = $this->initPencariResult($kelompokUmur);

        // Ambil dari tabel lamaran dengan progres "diterima" atau dari is_diterima di users_pencari
        $lamarans = Lamaran::query()
            ->whereNull('deleted_at')
            ->whereBetween('updated_at', [$dariTanggal, $sampaiTanggal])
            ->where('progres_id', 3) // Asumsi progres_id 3 = diterima, sesuaikan dengan data Anda
            ->with('user.pencari')
            ->get();

        foreach ($lamarans as $lamaran) {
            if ($lamaran->user && $lamaran->user->pencari) {
                $pencari = $lamaran->user->pencari;
                $umur = $this->hitungUmur($pencari->tanggal_lahir, $sampaiTanggal);
                $gender = strtoupper($pencari->gender) === 'L' ? 'L' : 'P';
                $kelompok = $this->getKelompokUmur($umur, $kelompokUmur);

                if ($kelompok) {
                    $result[$kelompok][$gender]++;
                    $result['jumlah'][$gender]++;
                    $result['jumlah']['total']++;
                }
            }
        }

        return $result;
    }

    /**
     * Get pencari kerja yang dihapus pada periode tertentu
     */
    private function getPencariKerjaDihapus($dariTanggal, $sampaiTanggal, $kelompokUmur)
    {
        $result = $this->initPencariResult($kelompokUmur);

        $pencaris = UserPencari::onlyTrashed()
            ->whereBetween('deleted_at', [$dariTanggal, $sampaiTanggal])
            ->get();

        foreach ($pencaris as $pencari) {
            $umur = $this->hitungUmur($pencari->tanggal_lahir, $sampaiTanggal);
            $gender = strtoupper($pencari->gender) === 'L' ? 'L' : 'P';
            $kelompok = $this->getKelompokUmur($umur, $kelompokUmur);

            if ($kelompok) {
                $result[$kelompok][$gender]++;
                $result['jumlah'][$gender]++;
                $result['jumlah']['total']++;
            }
        }

        return $result;
    }

    /**
     * Get lowongan yang belum dipenuhi sampai tanggal tertentu
     */
    private function getLowonganBelumDipenuhi($sampaiTanggal)
    {
        $lowongans = Lowongan::query()
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $sampaiTanggal)
            ->where(function ($q) use ($sampaiTanggal) {
                $q->where('tanggal_end', '>=', $sampaiTanggal)
                  ->orWhereNull('tanggal_end');
            })
            ->whereIn('status_id', [0, 1]) // Status aktif/pending, sesuaikan
            ->get();

        $L = $lowongans->sum('jumlah_pria') ?? 0;
        $W = $lowongans->sum('jumlah_wanita') ?? 0;

        return [
            'L' => $L,
            'W' => $W,
            'total' => $L + $W,
        ];
    }

    /**
     * Get lowongan yang terdaftar pada periode tertentu
     */
    private function getLowonganTerdaftar($dariTanggal, $sampaiTanggal)
    {
        $lowongans = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
            ->get();

        $L = $lowongans->sum('jumlah_pria') ?? 0;
        $W = $lowongans->sum('jumlah_wanita') ?? 0;

        return [
            'L' => $L,
            'W' => $W,
            'total' => $L + $W,
        ];
    }

    /**
     * Get lowongan yang dipenuhi pada periode tertentu
     */
    private function getLowonganDipenuhi($dariTanggal, $sampaiTanggal)
    {
        // Lowongan dianggap dipenuhi jika status berubah ke "selesai" pada periode tersebut
        $lowongans = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('updated_at', [$dariTanggal, $sampaiTanggal])
            ->where('status_id', 3) // Asumsi status_id 3 = selesai/dipenuhi, sesuaikan
            ->get();

        $L = $lowongans->sum('jumlah_pria') ?? 0;
        $W = $lowongans->sum('jumlah_wanita') ?? 0;

        return [
            'L' => $L,
            'W' => $W,
            'total' => $L + $W,
        ];
    }

    /**
     * Get lowongan yang dihapus pada periode tertentu
     */
    private function getLowonganDihapus($dariTanggal, $sampaiTanggal)
    {
        $lowongans = Lowongan::onlyTrashed()
            ->whereBetween('deleted_at', [$dariTanggal, $sampaiTanggal])
            ->get();

        $L = $lowongans->sum('jumlah_pria') ?? 0;
        $W = $lowongans->sum('jumlah_wanita') ?? 0;

        return [
            'L' => $L,
            'W' => $W,
            'total' => $L + $W,
        ];
    }

    /**
     * Initialize pencari result array
     */
    private function initPencariResult($kelompokUmur)
    {
        $result = [];
        foreach ($kelompokUmur as $key => $range) {
            $result[$key] = ['L' => 0, 'P' => 0];
        }
        $result['jumlah'] = ['L' => 0, 'P' => 0, 'total' => 0];
        return $result;
    }

    /**
     * Hitung umur dari tanggal lahir
     */
    private function hitungUmur($tanggalLahir, $tanggalAcuan)
    {
        if (!$tanggalLahir) {
            return 0;
        }
        return Carbon::parse($tanggalLahir)->diffInYears(Carbon::parse($tanggalAcuan));
    }

    /**
     * Get kelompok umur berdasarkan umur
     */
    private function getKelompokUmur($umur, $kelompokUmur)
    {
        foreach ($kelompokUmur as $key => $range) {
            if ($umur >= $range['min'] && $umur <= $range['max']) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Sum two pencari data arrays
     */
    private function sumPencariData($data1, $data2)
    {
        $result = [];
        foreach ($data1 as $key => $values) {
            if (is_array($values)) {
                $result[$key] = [
                    'L' => ($data1[$key]['L'] ?? 0) + ($data2[$key]['L'] ?? 0),
                    'P' => ($data1[$key]['P'] ?? 0) + ($data2[$key]['P'] ?? 0),
                ];
                if (isset($values['total'])) {
                    $result[$key]['total'] = ($data1[$key]['total'] ?? 0) + ($data2[$key]['total'] ?? 0);
                }
            }
        }
        return $result;
    }

    /**
     * Subtract pencari data (data1 - data2)
     */
    private function subtractPencariData($data1, $data2)
    {
        $result = [];
        foreach ($data1 as $key => $values) {
            if (is_array($values)) {
                $result[$key] = [
                    'L' => max(0, ($data1[$key]['L'] ?? 0) - ($data2[$key]['L'] ?? 0)),
                    'P' => max(0, ($data1[$key]['P'] ?? 0) - ($data2[$key]['P'] ?? 0)),
                ];
                if (isset($values['total'])) {
                    $result[$key]['total'] = max(0, ($data1[$key]['total'] ?? 0) - ($data2[$key]['total'] ?? 0));
                }
            }
        }
        return $result;
    }

    /**
     * Get nama bulan dalam Bahasa Indonesia
     */
    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $namaBulan[$bulan] ?? '';
    }


     /**
     * IPK III/2: PENCARI KERJA YANG TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN
     * Breakdown berdasarkan Jenis Pendidikan
     */
    public function ak3titik2(Request $request)
    {
        // Get bulan dan tahun dari request, default bulan ini
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Buat tanggal awal dan akhir bulan yang dipilih
        $tanggalAwalBulanIni = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhirBulanIni = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Tanggal akhir bulan lalu (untuk sisa akhir bulan lalu)
        $tanggalAkhirBulanLalu = Carbon::create($tahun, $bulan, 1)->subMonth()->endOfMonth();

        // Ambil daftar pendidikan
        $pendidikans = \App\Models\Pendidikan::orderBy('kode')->get();

        // Initialize result array
        $result = [];
        foreach ($pendidikans as $pendidikan) {
            $result[$pendidikan->id] = [
                'kode' => $pendidikan->kode,
                'nama' => $pendidikan->name,
                'sisa_bulan_lalu' => ['L' => 0, 'W' => 0],
                'pendaftaran' => ['L' => 0, 'W' => 0],
                'penempatan' => ['L' => 0, 'W' => 0],
                'penghapusan' => ['L' => 0, 'W' => 0],
                'sisa_bulan_ini' => ['L' => 0, 'W' => 0],
            ];
        }

        // =====================================================
        // 1. SISA AKHIR BULAN (LALU) - Terdaftar s.d. akhir bulan lalu dan belum ditempatkan
        // =====================================================
        $sisaBulanLalu = UserPencari::query()
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $tanggalAkhirBulanLalu)
            ->where(function ($q) {
                $q->whereNull('is_diterima')
                  ->orWhere('is_diterima', '!=', 1);
            })
            ->selectRaw('id_pendidikan, gender, COUNT(*) as total')
            ->groupBy('id_pendidikan', 'gender')
            ->get();

        foreach ($sisaBulanLalu as $row) {
            if (isset($result[$row->id_pendidikan])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->id_pendidikan]['sisa_bulan_lalu'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 2. PENDAFTARAN BULAN INI
        // =====================================================
        $pendaftaran = UserPencari::query()
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->selectRaw('id_pendidikan, gender, COUNT(*) as total')
            ->groupBy('id_pendidikan', 'gender')
            ->get();

        foreach ($pendaftaran as $row) {
            if (isset($result[$row->id_pendidikan])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->id_pendidikan]['pendaftaran'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 3. PENEMPATAN BULAN INI (dari tabel lamaran dengan progres diterima)
        // =====================================================
        $penempatan = Lamaran::query()
            ->whereNull('etam_lamaran.deleted_at')
            ->whereBetween('etam_lamaran.updated_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->where('etam_lamaran.progres_id', 3) // progres_id 3 = diterima, sesuaikan
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id')
            ->selectRaw('users_pencari.id_pendidikan, users_pencari.gender, COUNT(*) as total')
            ->groupBy('users_pencari.id_pendidikan', 'users_pencari.gender')
            ->get();

        foreach ($penempatan as $row) {
            if (isset($result[$row->id_pendidikan])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->id_pendidikan]['penempatan'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 4. PENGHAPUSAN BULAN INI (soft deleted)
        // =====================================================
        $penghapusan = UserPencari::onlyTrashed()
            ->whereBetween('deleted_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->selectRaw('id_pendidikan, gender, COUNT(*) as total')
            ->groupBy('id_pendidikan', 'gender')
            ->get();

        foreach ($penghapusan as $row) {
            if (isset($result[$row->id_pendidikan])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->id_pendidikan]['penghapusan'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 5. HITUNG SISA AKHIR BULAN INI
        // Sisa = Sisa Bulan Lalu + Pendaftaran - Penempatan - Penghapusan
        // =====================================================
        foreach ($result as $id => $data) {
            $result[$id]['sisa_bulan_ini']['L'] = max(0,
                $data['sisa_bulan_lalu']['L'] +
                $data['pendaftaran']['L'] -
                $data['penempatan']['L'] -
                $data['penghapusan']['L']
            );
            $result[$id]['sisa_bulan_ini']['W'] = max(0,
                $data['sisa_bulan_lalu']['W'] +
                $data['pendaftaran']['W'] -
                $data['penempatan']['W'] -
                $data['penghapusan']['W']
            );
        }

        // =====================================================
        // HITUNG JUMLAH TOTAL
        // =====================================================
        $jumlah = [
            'sisa_bulan_lalu' => ['L' => 0, 'W' => 0],
            'pendaftaran' => ['L' => 0, 'W' => 0],
            'penempatan' => ['L' => 0, 'W' => 0],
            'penghapusan' => ['L' => 0, 'W' => 0],
            'sisa_bulan_ini' => ['L' => 0, 'W' => 0],
        ];

        foreach ($result as $data) {
            $jumlah['sisa_bulan_lalu']['L'] += $data['sisa_bulan_lalu']['L'];
            $jumlah['sisa_bulan_lalu']['W'] += $data['sisa_bulan_lalu']['W'];
            $jumlah['pendaftaran']['L'] += $data['pendaftaran']['L'];
            $jumlah['pendaftaran']['W'] += $data['pendaftaran']['W'];
            $jumlah['penempatan']['L'] += $data['penempatan']['L'];
            $jumlah['penempatan']['W'] += $data['penempatan']['W'];
            $jumlah['penghapusan']['L'] += $data['penghapusan']['L'];
            $jumlah['penghapusan']['W'] += $data['penghapusan']['W'];
            $jumlah['sisa_bulan_ini']['L'] += $data['sisa_bulan_ini']['L'];
            $jumlah['sisa_bulan_ini']['W'] += $data['sisa_bulan_ini']['W'];
        }

        // Data untuk view
        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $this->getNamaBulan($bulan),
            'pendidikans' => $result,
            'jumlah' => $jumlah,
        ];

        return view('backend.rekap.ak3.dua', $data);
    }


    // public function ak3titik2(){
    //     return view('backend.rekap.ak3.dua');
    // }



    /**
     * IPK III/3: PENCARI KERJA TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN MENURUT GOLONGAN JABATAN
     * Breakdown berdasarkan Jabatan dari Lowongan yang dilamar
     * Berbasis TRIWULAN
     */
    public function ak3titik3(Request $request)
    {
        // Get triwulan dan tahun dari request
        $triwulan = $request->get('triwulan', ceil(Carbon::now()->month / 3));
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Hitung range tanggal triwulan
        $bulanAwal = (($triwulan - 1) * 3) + 1;
        $bulanAkhir = $triwulan * 3;

        $tanggalAwalTriwulanIni = Carbon::create($tahun, $bulanAwal, 1)->startOfMonth();
        $tanggalAkhirTriwulanIni = Carbon::create($tahun, $bulanAkhir, 1)->endOfMonth();

        // Triwulan lalu
        $triwulanLalu = $triwulan - 1;
        $tahunTriwulanLalu = $tahun;
        if ($triwulanLalu < 1) {
            $triwulanLalu = 4;
            $tahunTriwulanLalu = $tahun - 1;
        }
        $bulanAwalLalu = (($triwulanLalu - 1) * 3) + 1;
        $bulanAkhirLalu = $triwulanLalu * 3;
        $tanggalAkhirTriwulanLalu = Carbon::create($tahunTriwulanLalu, $bulanAkhirLalu, 1)->endOfMonth();

        // Ambil daftar jabatan
        $jabatans = \App\Models\Jabatan::orderBy('id')->get();

        // Initialize result array
        $result = [];
        foreach ($jabatans as $jabatan) {
            $result[$jabatan->id] = [
                'kode' => $jabatan->kode ?? $jabatan->id,
                'nama' => $jabatan->nama,
                'sisa_triwulan_lalu' => ['L' => 0, 'W' => 0],
                'pendaftaran' => ['L' => 0, 'W' => 0],
                'penempatan' => ['L' => 0, 'W' => 0],
                'penghapusan' => ['L' => 0, 'W' => 0],
                'sisa_triwulan_ini' => ['L' => 0, 'W' => 0],
            ];
        }

        // =====================================================
        // 1. SISA AKHIR TRIWULAN LALU
        // Lamaran yang dibuat s.d. akhir triwulan lalu, progres_id != 3, tidak terhapus
        // =====================================================
        $sisaTriwulanLalu = Lamaran::query()
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id')
            ->whereNull('etam_lamaran.deleted_at')
            ->whereNull('users_pencari.deleted_at')
            ->where('etam_lamaran.created_at', '<=', $tanggalAkhirTriwulanLalu)
            ->where('etam_lamaran.progres_id', '!=', 3)
            ->selectRaw('etam_lowongan.jabatan_id, users_pencari.gender, COUNT(*) as total')
            ->groupBy('etam_lowongan.jabatan_id', 'users_pencari.gender')
            ->get();

        foreach ($sisaTriwulanLalu as $row) {
            if (isset($result[$row->jabatan_id])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->jabatan_id]['sisa_triwulan_lalu'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 2. PENDAFTARAN TRIWULAN INI
        // Lamaran baru di triwulan ini, semua progres_id kecuali 3, tidak terhapus
        // =====================================================
        $pendaftaran = Lamaran::query()
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id')
            ->whereNull('etam_lamaran.deleted_at')
            ->whereNull('users_pencari.deleted_at')
            ->whereBetween('etam_lamaran.created_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->where('etam_lamaran.progres_id', '!=', 3)
            ->selectRaw('etam_lowongan.jabatan_id, users_pencari.gender, COUNT(*) as total')
            ->groupBy('etam_lowongan.jabatan_id', 'users_pencari.gender')
            ->get();

        foreach ($pendaftaran as $row) {
            if (isset($result[$row->jabatan_id])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->jabatan_id]['pendaftaran'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 3. PENEMPATAN TRIWULAN INI
        // Lamaran dengan progres_id = 3 (diterima) di triwulan ini
        // =====================================================
        $penempatan = Lamaran::query()
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id')
            ->whereNull('etam_lamaran.deleted_at')
            ->whereNull('users_pencari.deleted_at')
            ->whereBetween('etam_lamaran.updated_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->where('etam_lamaran.progres_id', 3)
            ->selectRaw('etam_lowongan.jabatan_id, users_pencari.gender, COUNT(*) as total')
            ->groupBy('etam_lowongan.jabatan_id', 'users_pencari.gender')
            ->get();

        foreach ($penempatan as $row) {
            if (isset($result[$row->jabatan_id])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->jabatan_id]['penempatan'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 4. PENGHAPUSAN TRIWULAN INI
        // Lamaran yang pencarinya (users_pencari) dihapus di triwulan ini
        // =====================================================
        $penghapusan = Lamaran::query()
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id')
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id')
            ->whereNull('etam_lamaran.deleted_at')
            ->whereNotNull('users_pencari.deleted_at')
            ->whereBetween('users_pencari.deleted_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->selectRaw('etam_lowongan.jabatan_id, users_pencari.gender, COUNT(*) as total')
            ->groupBy('etam_lowongan.jabatan_id', 'users_pencari.gender')
            ->get();

        foreach ($penghapusan as $row) {
            if (isset($result[$row->jabatan_id])) {
                $gender = strtoupper($row->gender) === 'L' ? 'L' : 'W';
                $result[$row->jabatan_id]['penghapusan'][$gender] = $row->total;
            }
        }

        // =====================================================
        // 5. HITUNG SISA AKHIR TRIWULAN INI
        // Sisa = Sisa Triwulan Lalu + Pendaftaran - Penempatan - Penghapusan
        // =====================================================
        foreach ($result as $id => $data) {
            $result[$id]['sisa_triwulan_ini']['L'] = max(0,
                $data['sisa_triwulan_lalu']['L'] +
                $data['pendaftaran']['L'] -
                $data['penempatan']['L'] -
                $data['penghapusan']['L']
            );
            $result[$id]['sisa_triwulan_ini']['W'] = max(0,
                $data['sisa_triwulan_lalu']['W'] +
                $data['pendaftaran']['W'] -
                $data['penempatan']['W'] -
                $data['penghapusan']['W']
            );
        }

        // =====================================================
        // HITUNG JUMLAH TOTAL
        // =====================================================
        $jumlah = [
            'sisa_triwulan_lalu' => ['L' => 0, 'W' => 0],
            'pendaftaran' => ['L' => 0, 'W' => 0],
            'penempatan' => ['L' => 0, 'W' => 0],
            'penghapusan' => ['L' => 0, 'W' => 0],
            'sisa_triwulan_ini' => ['L' => 0, 'W' => 0],
        ];

        foreach ($result as $data) {
            $jumlah['sisa_triwulan_lalu']['L'] += $data['sisa_triwulan_lalu']['L'];
            $jumlah['sisa_triwulan_lalu']['W'] += $data['sisa_triwulan_lalu']['W'];
            $jumlah['pendaftaran']['L'] += $data['pendaftaran']['L'];
            $jumlah['pendaftaran']['W'] += $data['pendaftaran']['W'];
            $jumlah['penempatan']['L'] += $data['penempatan']['L'];
            $jumlah['penempatan']['W'] += $data['penempatan']['W'];
            $jumlah['penghapusan']['L'] += $data['penghapusan']['L'];
            $jumlah['penghapusan']['W'] += $data['penghapusan']['W'];
            $jumlah['sisa_triwulan_ini']['L'] += $data['sisa_triwulan_ini']['L'];
            $jumlah['sisa_triwulan_ini']['W'] += $data['sisa_triwulan_ini']['W'];
        }

        // Data untuk view
        $data = [
            'triwulan' => $triwulan,
            'tahun' => $tahun,
            'namaTriwulan' => $this->getNamaTriwulan($triwulan),
            'jabatans' => $result,
            'jumlah' => $jumlah,
        ];

        return view('backend.rekap.ak3.tiga', $data);
    }

    /**
     * Get nama triwulan
     */
    private function getNamaTriwulan($triwulan)
    {
        $nama = [
            1 => 'Triwulan I (Januari - Maret)',
            2 => 'Triwulan II (April - Juni)',
            3 => 'Triwulan III (Juli - September)',
            4 => 'Triwulan IV (Oktober - Desember)',
        ];
        return $nama[$triwulan] ?? '';
    }


    // public function ak3titik3(){
    //     return view('backend.rekap.ak3.tiga');
    // }



     /**
     * IPK III/4: LOWONGAN KERJA YANG TERDAFTAR, DIPENUHI DAN DIHAPUSKAN
     * Breakdown berdasarkan Jenis Pendidikan dari Lowongan
     * Berbasis BULANAN
     */
    public function ak3titik4(Request $request)
    {
        // Get bulan dan tahun dari request, default bulan ini
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Buat tanggal awal dan akhir bulan yang dipilih
        $tanggalAwalBulanIni = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhirBulanIni = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Tanggal akhir bulan lalu
        $tanggalAkhirBulanLalu = Carbon::create($tahun, $bulan, 1)->subMonth()->endOfMonth();

        // Ambil daftar pendidikan
        $pendidikans = \App\Models\Pendidikan::orderBy('kode')->get();

        // Initialize result array
        $result = [];
        foreach ($pendidikans as $pendidikan) {
            $result[$pendidikan->id] = [
                'kode' => $pendidikan->kode,
                'nama' => $pendidikan->name,
                'sisa_bulan_lalu' => ['L' => 0, 'W' => 0],
                'pendaftaran' => ['L' => 0, 'W' => 0],
                'penempatan' => ['L' => 0, 'W' => 0],
                'penghapusan' => ['L' => 0, 'W' => 0],
                'sisa_bulan_ini' => ['L' => 0, 'W' => 0],
            ];
        }

        // =====================================================
        // 1. SISA AKHIR BULAN LALU
        // Lowongan created s.d. akhir bulan lalu, status_id != 2, tidak terhapus, belum dipenuhi
        // =====================================================
        $sisaBulanLalu = Lowongan::query()
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $tanggalAkhirBulanLalu)
            ->where('status_id', '!=', 2) // aktif
            ->whereNotIn('status_id', [3]) // belum dipenuhi (sesuaikan jika beda)
            ->selectRaw('pendidikan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('pendidikan_id')
            ->get();

        foreach ($sisaBulanLalu as $row) {
            if (isset($result[$row->pendidikan_id])) {
                $result[$row->pendidikan_id]['sisa_bulan_lalu']['L'] = $row->total_l ?? 0;
                $result[$row->pendidikan_id]['sisa_bulan_lalu']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 2. PENDAFTARAN BULAN INI
        // Lowongan baru created di bulan ini
        // =====================================================
        $pendaftaran = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->selectRaw('pendidikan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('pendidikan_id')
            ->get();

        foreach ($pendaftaran as $row) {
            if (isset($result[$row->pendidikan_id])) {
                $result[$row->pendidikan_id]['pendaftaran']['L'] = $row->total_l ?? 0;
                $result[$row->pendidikan_id]['pendaftaran']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 3. PENEMPATAN/DIPENUHI BULAN INI
        // Lowongan yang status berubah ke dipenuhi (status_id = 3) di bulan ini
        // =====================================================
        $penempatan = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('updated_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->where('status_id', 3) // dipenuhi, sesuaikan
            ->selectRaw('pendidikan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('pendidikan_id')
            ->get();

        foreach ($penempatan as $row) {
            if (isset($result[$row->pendidikan_id])) {
                $result[$row->pendidikan_id]['penempatan']['L'] = $row->total_l ?? 0;
                $result[$row->pendidikan_id]['penempatan']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 4. PENGHAPUSAN BULAN INI
        // Lowongan yang di soft delete di bulan ini
        // =====================================================
        $penghapusan = Lowongan::onlyTrashed()
            ->whereBetween('deleted_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->selectRaw('pendidikan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('pendidikan_id')
            ->get();

        foreach ($penghapusan as $row) {
            if (isset($result[$row->pendidikan_id])) {
                $result[$row->pendidikan_id]['penghapusan']['L'] = $row->total_l ?? 0;
                $result[$row->pendidikan_id]['penghapusan']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 5. HITUNG SISA AKHIR BULAN INI
        // Sisa = Sisa Bulan Lalu + Pendaftaran - Penempatan - Penghapusan
        // =====================================================
        foreach ($result as $id => $data) {
            $result[$id]['sisa_bulan_ini']['L'] = max(0,
                $data['sisa_bulan_lalu']['L'] +
                $data['pendaftaran']['L'] -
                $data['penempatan']['L'] -
                $data['penghapusan']['L']
            );
            $result[$id]['sisa_bulan_ini']['W'] = max(0,
                $data['sisa_bulan_lalu']['W'] +
                $data['pendaftaran']['W'] -
                $data['penempatan']['W'] -
                $data['penghapusan']['W']
            );
        }

        // =====================================================
        // HITUNG JUMLAH TOTAL
        // =====================================================
        $jumlah = [
            'sisa_bulan_lalu' => ['L' => 0, 'W' => 0],
            'pendaftaran' => ['L' => 0, 'W' => 0],
            'penempatan' => ['L' => 0, 'W' => 0],
            'penghapusan' => ['L' => 0, 'W' => 0],
            'sisa_bulan_ini' => ['L' => 0, 'W' => 0],
        ];

        foreach ($result as $data) {
            $jumlah['sisa_bulan_lalu']['L'] += $data['sisa_bulan_lalu']['L'];
            $jumlah['sisa_bulan_lalu']['W'] += $data['sisa_bulan_lalu']['W'];
            $jumlah['pendaftaran']['L'] += $data['pendaftaran']['L'];
            $jumlah['pendaftaran']['W'] += $data['pendaftaran']['W'];
            $jumlah['penempatan']['L'] += $data['penempatan']['L'];
            $jumlah['penempatan']['W'] += $data['penempatan']['W'];
            $jumlah['penghapusan']['L'] += $data['penghapusan']['L'];
            $jumlah['penghapusan']['W'] += $data['penghapusan']['W'];
            $jumlah['sisa_bulan_ini']['L'] += $data['sisa_bulan_ini']['L'];
            $jumlah['sisa_bulan_ini']['W'] += $data['sisa_bulan_ini']['W'];
        }

        // Data untuk view
        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $this->getNamaBulan($bulan),
            'pendidikans' => $result,
            'jumlah' => $jumlah,
        ];

        return view('backend.rekap.ak3.empat', $data);
    }

    // public function ak3titik4(){
    //     return view('backend.rekap.ak3.empat');
    // }


    /**
     * IPK III/5: LOWONGAN KERJA TERDAFTAR, DIPENUHI DAN DIHAPUSKAN MENURUT GOLONGAN JABATAN
     * Breakdown berdasarkan Jabatan dari Lowongan
     * Berbasis TRIWULAN
     */
    public function ak3titik5(Request $request)
    {
        // Get triwulan dan tahun dari request
        $triwulan = $request->get('triwulan', ceil(Carbon::now()->month / 3));
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Hitung range tanggal triwulan
        $bulanAwal = (($triwulan - 1) * 3) + 1;
        $bulanAkhir = $triwulan * 3;

        $tanggalAwalTriwulanIni = Carbon::create($tahun, $bulanAwal, 1)->startOfMonth();
        $tanggalAkhirTriwulanIni = Carbon::create($tahun, $bulanAkhir, 1)->endOfMonth();

        // Triwulan lalu
        $triwulanLalu = $triwulan - 1;
        $tahunTriwulanLalu = $tahun;
        if ($triwulanLalu < 1) {
            $triwulanLalu = 4;
            $tahunTriwulanLalu = $tahun - 1;
        }
        $bulanAkhirLalu = $triwulanLalu * 3;
        $tanggalAkhirTriwulanLalu = Carbon::create($tahunTriwulanLalu, $bulanAkhirLalu, 1)->endOfMonth();

        // Ambil daftar jabatan
        $jabatans = \App\Models\Jabatan::orderBy('id')->get();

        // Initialize result array
        $result = [];
        foreach ($jabatans as $jabatan) {
            $result[$jabatan->id] = [
                'kode' => $jabatan->kode ?? $jabatan->id,
                'nama' => $jabatan->nama,
                'sisa_triwulan_lalu' => ['L' => 0, 'W' => 0],
                'terdaftar' => ['L' => 0, 'W' => 0],
                'dipenuhi' => ['L' => 0, 'W' => 0],
                'dihapus' => ['L' => 0, 'W' => 0],
                'sisa_triwulan_ini' => ['L' => 0, 'W' => 0],
            ];
        }

        // =====================================================
        // 1. SISA AKHIR TRIWULAN LALU
        // Lowongan created s.d. akhir triwulan lalu, aktif, belum dipenuhi
        // =====================================================
        $sisaTriwulanLalu = Lowongan::query()
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $tanggalAkhirTriwulanLalu)
            ->where('status_id', '!=', 2) // aktif
            ->whereNotIn('status_id', [3]) // belum dipenuhi
            ->selectRaw('jabatan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('jabatan_id')
            ->get();

        foreach ($sisaTriwulanLalu as $row) {
            if (isset($result[$row->jabatan_id])) {
                $result[$row->jabatan_id]['sisa_triwulan_lalu']['L'] = $row->total_l ?? 0;
                $result[$row->jabatan_id]['sisa_triwulan_lalu']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 2. LOWONGAN TERDAFTAR TRIWULAN INI
        // =====================================================
        $terdaftar = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->selectRaw('jabatan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('jabatan_id')
            ->get();

        foreach ($terdaftar as $row) {
            if (isset($result[$row->jabatan_id])) {
                $result[$row->jabatan_id]['terdaftar']['L'] = $row->total_l ?? 0;
                $result[$row->jabatan_id]['terdaftar']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 3. LOWONGAN DIPENUHI TRIWULAN INI
        // =====================================================
        $dipenuhi = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('updated_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->where('status_id', 3) // dipenuhi
            ->selectRaw('jabatan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('jabatan_id')
            ->get();

        foreach ($dipenuhi as $row) {
            if (isset($result[$row->jabatan_id])) {
                $result[$row->jabatan_id]['dipenuhi']['L'] = $row->total_l ?? 0;
                $result[$row->jabatan_id]['dipenuhi']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 4. LOWONGAN DIHAPUS TRIWULAN INI
        // =====================================================
        $dihapus = Lowongan::onlyTrashed()
            ->whereBetween('deleted_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->selectRaw('jabatan_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('jabatan_id')
            ->get();

        foreach ($dihapus as $row) {
            if (isset($result[$row->jabatan_id])) {
                $result[$row->jabatan_id]['dihapus']['L'] = $row->total_l ?? 0;
                $result[$row->jabatan_id]['dihapus']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 5. HITUNG SISA AKHIR TRIWULAN INI
        // =====================================================
        foreach ($result as $id => $data) {
            $result[$id]['sisa_triwulan_ini']['L'] = max(0,
                $data['sisa_triwulan_lalu']['L'] +
                $data['terdaftar']['L'] -
                $data['dipenuhi']['L'] -
                $data['dihapus']['L']
            );
            $result[$id]['sisa_triwulan_ini']['W'] = max(0,
                $data['sisa_triwulan_lalu']['W'] +
                $data['terdaftar']['W'] -
                $data['dipenuhi']['W'] -
                $data['dihapus']['W']
            );
        }

        // =====================================================
        // HITUNG JUMLAH TOTAL
        // =====================================================
        $jumlah = [
            'sisa_triwulan_lalu' => ['L' => 0, 'W' => 0],
            'terdaftar' => ['L' => 0, 'W' => 0],
            'dipenuhi' => ['L' => 0, 'W' => 0],
            'dihapus' => ['L' => 0, 'W' => 0],
            'sisa_triwulan_ini' => ['L' => 0, 'W' => 0],
        ];

        foreach ($result as $data) {
            $jumlah['sisa_triwulan_lalu']['L'] += $data['sisa_triwulan_lalu']['L'];
            $jumlah['sisa_triwulan_lalu']['W'] += $data['sisa_triwulan_lalu']['W'];
            $jumlah['terdaftar']['L'] += $data['terdaftar']['L'];
            $jumlah['terdaftar']['W'] += $data['terdaftar']['W'];
            $jumlah['dipenuhi']['L'] += $data['dipenuhi']['L'];
            $jumlah['dipenuhi']['W'] += $data['dipenuhi']['W'];
            $jumlah['dihapus']['L'] += $data['dihapus']['L'];
            $jumlah['dihapus']['W'] += $data['dihapus']['W'];
            $jumlah['sisa_triwulan_ini']['L'] += $data['sisa_triwulan_ini']['L'];
            $jumlah['sisa_triwulan_ini']['W'] += $data['sisa_triwulan_ini']['W'];
        }

        // Data untuk view
        $data = [
            'triwulan' => $triwulan,
            'tahun' => $tahun,
            'namaTriwulan' => $this->getNamaTriwulan($triwulan),
            'jabatans' => $result,
            'jumlah' => $jumlah,
        ];

        return view('backend.rekap.ak3.lima', $data);
    }

    // public function ak3titik5(){
    //     return view('backend.rekap.ak3.lima');
    // }


    /**
     * IPK III/6: LOWONGAN KERJA TERDAFTAR, DIPENUHI DAN DIHAPUSKAN MENURUT SEKTOR LAPANGAN USAHA
     * Breakdown berdasarkan Sektor dari Lowongan
     * Berbasis TRIWULAN
     */
    public function ak3titik6(Request $request)
    {
        // Get triwulan dan tahun dari request
        $triwulan = $request->get('triwulan', ceil(Carbon::now()->month / 3));
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Hitung range tanggal triwulan
        $bulanAwal = (($triwulan - 1) * 3) + 1;
        $bulanAkhir = $triwulan * 3;

        $tanggalAwalTriwulanIni = Carbon::create($tahun, $bulanAwal, 1)->startOfMonth();
        $tanggalAkhirTriwulanIni = Carbon::create($tahun, $bulanAkhir, 1)->endOfMonth();

        // Triwulan lalu
        $triwulanLalu = $triwulan - 1;
        $tahunTriwulanLalu = $tahun;
        if ($triwulanLalu < 1) {
            $triwulanLalu = 4;
            $tahunTriwulanLalu = $tahun - 1;
        }
        $bulanAkhirLalu = $triwulanLalu * 3;
        $tanggalAkhirTriwulanLalu = Carbon::create($tahunTriwulanLalu, $bulanAkhirLalu, 1)->endOfMonth();

        // Ambil daftar sektor yang aktif
        $sektors = \App\Models\Sektor::whereNull('deleted_at')->orderBy('kode')->get();

        // Initialize result array
        $result = [];
        foreach ($sektors as $sektor) {
            $result[$sektor->id] = [
                'kode' => $sektor->kode,
                'nama' => $sektor->name,
                'sisa_triwulan_lalu' => ['L' => 0, 'W' => 0],
                'terdaftar' => ['L' => 0, 'W' => 0],
                'dipenuhi' => ['L' => 0, 'W' => 0],
                'dihapus' => ['L' => 0, 'W' => 0],
                'sisa_triwulan_ini' => ['L' => 0, 'W' => 0],
            ];
        }

        // =====================================================
        // 1. SISA AKHIR TRIWULAN LALU
        // =====================================================
        $sisaTriwulanLalu = Lowongan::query()
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $tanggalAkhirTriwulanLalu)
            ->where('status_id', '!=', 2)
            ->whereNotIn('status_id', [3])
            ->selectRaw('sektor_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('sektor_id')
            ->get();

        foreach ($sisaTriwulanLalu as $row) {
            if (isset($result[$row->sektor_id])) {
                $result[$row->sektor_id]['sisa_triwulan_lalu']['L'] = $row->total_l ?? 0;
                $result[$row->sektor_id]['sisa_triwulan_lalu']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 2. LOWONGAN TERDAFTAR TRIWULAN INI
        // =====================================================
        $terdaftar = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->selectRaw('sektor_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('sektor_id')
            ->get();

        foreach ($terdaftar as $row) {
            if (isset($result[$row->sektor_id])) {
                $result[$row->sektor_id]['terdaftar']['L'] = $row->total_l ?? 0;
                $result[$row->sektor_id]['terdaftar']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 3. LOWONGAN DIPENUHI TRIWULAN INI
        // =====================================================
        $dipenuhi = Lowongan::query()
            ->whereNull('deleted_at')
            ->whereBetween('updated_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->where('status_id', 3)
            ->selectRaw('sektor_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('sektor_id')
            ->get();

        foreach ($dipenuhi as $row) {
            if (isset($result[$row->sektor_id])) {
                $result[$row->sektor_id]['dipenuhi']['L'] = $row->total_l ?? 0;
                $result[$row->sektor_id]['dipenuhi']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 4. LOWONGAN DIHAPUS TRIWULAN INI
        // =====================================================
        $dihapus = Lowongan::onlyTrashed()
            ->whereBetween('deleted_at', [$tanggalAwalTriwulanIni, $tanggalAkhirTriwulanIni])
            ->selectRaw('sektor_id, SUM(jumlah_pria) as total_l, SUM(jumlah_wanita) as total_w')
            ->groupBy('sektor_id')
            ->get();

        foreach ($dihapus as $row) {
            if (isset($result[$row->sektor_id])) {
                $result[$row->sektor_id]['dihapus']['L'] = $row->total_l ?? 0;
                $result[$row->sektor_id]['dihapus']['W'] = $row->total_w ?? 0;
            }
        }

        // =====================================================
        // 5. HITUNG SISA AKHIR TRIWULAN INI
        // =====================================================
        foreach ($result as $id => $data) {
            $result[$id]['sisa_triwulan_ini']['L'] = max(0,
                $data['sisa_triwulan_lalu']['L'] +
                $data['terdaftar']['L'] -
                $data['dipenuhi']['L'] -
                $data['dihapus']['L']
            );
            $result[$id]['sisa_triwulan_ini']['W'] = max(0,
                $data['sisa_triwulan_lalu']['W'] +
                $data['terdaftar']['W'] -
                $data['dipenuhi']['W'] -
                $data['dihapus']['W']
            );
        }

        // =====================================================
        // HITUNG JUMLAH TOTAL
        // =====================================================
        $jumlah = [
            'sisa_triwulan_lalu' => ['L' => 0, 'W' => 0],
            'terdaftar' => ['L' => 0, 'W' => 0],
            'dipenuhi' => ['L' => 0, 'W' => 0],
            'dihapus' => ['L' => 0, 'W' => 0],
            'sisa_triwulan_ini' => ['L' => 0, 'W' => 0],
        ];

        foreach ($result as $data) {
            $jumlah['sisa_triwulan_lalu']['L'] += $data['sisa_triwulan_lalu']['L'];
            $jumlah['sisa_triwulan_lalu']['W'] += $data['sisa_triwulan_lalu']['W'];
            $jumlah['terdaftar']['L'] += $data['terdaftar']['L'];
            $jumlah['terdaftar']['W'] += $data['terdaftar']['W'];
            $jumlah['dipenuhi']['L'] += $data['dipenuhi']['L'];
            $jumlah['dipenuhi']['W'] += $data['dipenuhi']['W'];
            $jumlah['dihapus']['L'] += $data['dihapus']['L'];
            $jumlah['dihapus']['W'] += $data['dihapus']['W'];
            $jumlah['sisa_triwulan_ini']['L'] += $data['sisa_triwulan_ini']['L'];
            $jumlah['sisa_triwulan_ini']['W'] += $data['sisa_triwulan_ini']['W'];
        }

        // Data untuk view
        $data = [
            'triwulan' => $triwulan,
            'tahun' => $tahun,
            'namaTriwulan' => $this->getNamaTriwulan($triwulan),
            'sektors' => $result,
            'jumlah' => $jumlah,
        ];

        return view('backend.rekap.ak3.enam', $data);
    }


    // public function ak3titik6(){
    //     return view('backend.rekap.ak3.enam');
    // }
    

    // ===================

    /**
     * IPK III/7: PENCARI KERJA YANG TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN
     * Menurut Lama Lulusan dan Tingkat Pendidikan
     * Berbasis BULANAN
     */
    public function ak3titik7(Request $request)
    {
        // Get bulan dan tahun dari request, default bulan ini
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Buat tanggal awal dan akhir bulan yang dipilih
        $tanggalAwalBulanIni = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhirBulanIni = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Tanggal akhir bulan lalu
        $tanggalAkhirBulanLalu = Carbon::create($tahun, $bulan, 1)->subMonth()->endOfMonth();

        // Ambil daftar pendidikan
        $pendidikans = \App\Models\Pendidikan::orderBy('kode')->get();

        // Kategori lama lulusan
        $lamaLulusan = ['0-2', '3-5', '6+'];

        // Initialize result array
        $result = [];
        foreach ($pendidikans as $pendidikan) {
            $result[$pendidikan->id] = [
                'kode' => $pendidikan->kode,
                'nama' => $pendidikan->name,
                'sisa_bulan_lalu' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
                'terdaftar' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
                'penempatan' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
                'sisa_bulan_ini' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
            ];
        }

        // =====================================================
        // 1. SISA AKHIR BULAN LALU
        // =====================================================
        $sisaBulanLalu = UserPencari::query()
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $tanggalAkhirBulanLalu)
            ->where(function ($q) {
                $q->whereNull('is_diterima')
                  ->orWhere('is_diterima', '!=', 1);
            })
            ->selectRaw('id_pendidikan, tahun_lulus, COUNT(*) as total')
            ->groupBy('id_pendidikan', 'tahun_lulus')
            ->get();

        foreach ($sisaBulanLalu as $row) {
            if (isset($result[$row->id_pendidikan])) {
                $kategori = $this->getKategoriLamaLulusan($row->tahun_lulus, $tanggalAkhirBulanLalu->year);
                $result[$row->id_pendidikan]['sisa_bulan_lalu'][$kategori] += $row->total;
            }
        }

        // =====================================================
        // 2. TERDAFTAR BULAN INI
        // =====================================================
        $terdaftar = UserPencari::query()
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->selectRaw('id_pendidikan, tahun_lulus, COUNT(*) as total')
            ->groupBy('id_pendidikan', 'tahun_lulus')
            ->get();

        foreach ($terdaftar as $row) {
            if (isset($result[$row->id_pendidikan])) {
                $kategori = $this->getKategoriLamaLulusan($row->tahun_lulus, $tahun);
                $result[$row->id_pendidikan]['terdaftar'][$kategori] += $row->total;
            }
        }

        // =====================================================
        // 3. PENEMPATAN BULAN INI
        // =====================================================
        $penempatan = Lamaran::query()
            ->whereNull('etam_lamaran.deleted_at')
            ->whereBetween('etam_lamaran.updated_at', [$tanggalAwalBulanIni, $tanggalAkhirBulanIni])
            ->where('etam_lamaran.progres_id', 3)
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id')
            ->whereNull('users_pencari.deleted_at')
            ->selectRaw('users_pencari.id_pendidikan, users_pencari.tahun_lulus, COUNT(*) as total')
            ->groupBy('users_pencari.id_pendidikan', 'users_pencari.tahun_lulus')
            ->get();

        foreach ($penempatan as $row) {
            if (isset($result[$row->id_pendidikan])) {
                $kategori = $this->getKategoriLamaLulusan($row->tahun_lulus, $tahun);
                $result[$row->id_pendidikan]['penempatan'][$kategori] += $row->total;
            }
        }

        // =====================================================
        // 4. HITUNG SISA AKHIR BULAN INI
        // Sisa = Sisa Bulan Lalu + Terdaftar - Penempatan
        // =====================================================
        foreach ($result as $id => $data) {
            foreach ($lamaLulusan as $kategori) {
                $result[$id]['sisa_bulan_ini'][$kategori] = max(0,
                    $data['sisa_bulan_lalu'][$kategori] +
                    $data['terdaftar'][$kategori] -
                    $data['penempatan'][$kategori]
                );
            }
        }

        // =====================================================
        // HITUNG JUMLAH TOTAL
        // =====================================================
        $jumlah = [
            'sisa_bulan_lalu' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
            'terdaftar' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
            'penempatan' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
            'sisa_bulan_ini' => ['0-2' => 0, '3-5' => 0, '6+' => 0],
        ];

        foreach ($result as $data) {
            foreach ($lamaLulusan as $kategori) {
                $jumlah['sisa_bulan_lalu'][$kategori] += $data['sisa_bulan_lalu'][$kategori];
                $jumlah['terdaftar'][$kategori] += $data['terdaftar'][$kategori];
                $jumlah['penempatan'][$kategori] += $data['penempatan'][$kategori];
                $jumlah['sisa_bulan_ini'][$kategori] += $data['sisa_bulan_ini'][$kategori];
            }
        }

        // Data untuk view
        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $this->getNamaBulan($bulan),
            'lamaLulusan' => $lamaLulusan,
            'pendidikans' => $result,
            'jumlah' => $jumlah,
        ];

        return view('backend.rekap.ak3.tujuh', $data);
    }

    /**
     * Get kategori lama lulusan berdasarkan tahun lulus
     */
    private function getKategoriLamaLulusan($tahunLulus, $tahunAcuan)
    {
        if (!$tahunLulus) {
            return '6+'; // Default jika tidak ada tahun lulus
        }
        
        $lama = $tahunAcuan - $tahunLulus;
        
        if ($lama <= 2) {
            return '0-2';
        } elseif ($lama <= 5) {
            return '3-5';
        } else {
            return '6+';
        }
    }
    
    // public function ak3titik7(){
    //     return view('backend.rekap.ak3.tuju');
    // }

    public function ak3titik8(){
        return view('backend.rekap.ak3.delapan');
    }
}
