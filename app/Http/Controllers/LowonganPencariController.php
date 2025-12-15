<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganPencari;
use App\Models\ProfilPencari;
use App\Models\UserPencari;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LowonganPencariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();
            $userPencari = UserPencari::where('user_id', $userId)->first();
            $kabkotaUser = $userPencari->id_kota;

            $lokers = LowonganPencari::select(
                'etam_lowongan.id',
                'etam_lowongan.judul_lowongan',
                'etam_lowongan.tanggal_start',
                'etam_lowongan.tanggal_end',
                'etam_lowongan.deskripsi',
                'users_penyedia.name as nama_perusahaan'
            )
            ->join('users_penyedia', 'users_penyedia.user_id', '=', 'etam_lowongan.posted_by') // Join tabel
            ->where('etam_lowongan.status_id', '1')
            // ->where('tipe_lowongan', 0) // lowongan umum, bukan bkk, bukan job fair
            ->whereIn('etam_lowongan.tipe_lowongan', [0, 3]) // lowongan umu, lowongan magang mandiri
            // ->where('deleted_at', null)
            ->whereNull('etam_lowongan.deleted_at') // Memastikan data tidak terhapus
            // 🔥 LOGIKA LINGKUP LOWONGAN
            ->where(function ($query) use ($kabkotaUser) {
                $query
                    // selain kabkota (provinsi / nasional) → tampilkan semua
                    ->where('etam_lowongan.lingkup_lowongan', '!=', 0)

                    // jika kabkota → harus sama dengan kabkota user
                    ->orWhere(function ($q) use ($kabkotaUser) {
                        $q->where('etam_lowongan.lingkup_lowongan', 0)
                        ->where('etam_lowongan.kabkota_id', $kabkotaUser);
                    });
            })
            ->orderBy('etam_lowongan.id', 'desc')
            ->get();

            return DataTables::of($lokers)
                ->addIndexColumn()
                ->addColumn('options', function ($loker) {

                    return '
                        <button class="btn btn-warning btn-sm" onclick="showEditModal(' . $loker->id . ')">Lihat</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        $data['jabatans'] = getJabatan();
        $data['sektors'] = getSektor();
        $data['kabkotas'] = getKabkota();
        $data['pendidikans'] = getPendidikan();
        $data['maritals'] = getMarital();
        $data['lingkup_low'] = [
            [
                "kode" => 0,
                "name" => "Kabupaten/Kota"
            ],
            [
                "kode" => 1,
                "name" => "Provinsi"
            ],
            [
                "kode" => 2,
                "name" => "Nasional"
            ]
        ];

        return view('backend.lowonganpencari.index', $data);
    }

    public function indexbkk(Request $request)
    {
        $userId = auth()->user()->id;
        $bkkId = getRowPencariById($userId)->bkk_id;

        if ($request->ajax()) {
            $lokers = LowonganPencari::select(
                'etam_lowongan.id',
                'etam_lowongan.judul_lowongan',
                'etam_lowongan.tanggal_start',
                'etam_lowongan.tanggal_end',
                'etam_lowongan.deskripsi'
            )
            ->join('users_penyedia', 'etam_lowongan.posted_by', '=', 'users_penyedia.user_id') // Join tabel
            ->where('etam_lowongan.status_id', '1')
            ->where('etam_lowongan.tipe_lowongan', 2) // lowongan bkk, bukan umum, bukan job fair
            // ->where('deleted_at', null)
            ->whereNull('etam_lowongan.deleted_at') // Memastikan data tidak terhapus
            ->get();

            return DataTables::of($lokers)
                ->addIndexColumn()
                ->addColumn('options', function ($loker) {

                    return '
                        <button class="btn btn-warning btn-sm" onclick="showEditModal(' . $loker->id . ')">Lihat</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        $data['jabatans'] = getJabatan();
        $data['sektors'] = getSektor();
        $data['kabkotas'] = getKabkota();
        $data['pendidikans'] = getPendidikan();
        $data['maritals'] = getMarital();

        return view('backend.lowonganbkk.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // $data = LowonganPencari::all()->findOrFail($id);
            $data = LowonganPencari::findOrFail($id);
            $data->statuslamaran = $data->statuslamaran(auth()->user()->id);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    public function lamar(Request $request, string $id){
        try {
            // Validasi input
            // $validatedData = $request->validate([
            //     'name' => 'required|string',
            //     'description' => 'required|string',
            // ]);

            $userId = auth()->user()->id;

            // Gunakan transaksi untuk memastikan atomicity
            DB::beginTransaction();

            // Cari admin berdasarkan ID
            $dataLow = LowonganPencari::findOrFail($id);

            //get profil pencari
            $profil = ProfilPencari::where('user_id', $userId)->first();

            //check if profil->foto is null
            if($profil->foto == null){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum mengunggah foto profil, silahkan lengkapi profil terlebih dahulu'
                ]);
            }

            // Cek apakah sudah ada pencari_id yang sama di tabel etam_lamaran
            // $existingLamaran = DB::table('etam_lamaran')
            //     ->where('pencari_id', $userId)
            //     ->where('lowongan_id', $dataLow['id'])
            //     ->first();

            // Kunci baris untuk mencegah race condition
            $existingLamaran = DB::table('etam_lamaran')
                ->where('pencari_id', $userId)
                ->where('lowongan_id', $dataLow->id)
                ->first();

            // Jika sudah ada, kembalikan error
            if ($existingLamaran) {
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Gagal menyimpan lamaran, anda sudah melamar untuk lowongan ini'
                ]);
            }

            // $dataInsert = array(
            //     'pencari_id' => $userId,
            //     'lowongan_id' => $dataLow['id'],
            //     'kabkota_penempatan_id' => $dataLow['kabkota_id'],
            //     'progres_id' => 4,
            //     'keterangan' => null
            // );

            // Panggil model LowonganPencari dan gunakan fungsi insertLamaran
            // $lowonganPencari = new LowonganPencari();
            // $result = $lowonganPencari->insertLamaran($dataInsert);

            $dataInsert = array(
                'pencari_id' => $userId,
                'lowongan_id' => $dataLow['id'],
                'kabkota_penempatan_id' => $dataLow['kabkota_id'],
                'progres_id' => 4,
                'keterangan' => null,
                'created_at' => now(),
            );

            // Lakukan insert ke tabel etam_lamaran
            DB::table('etam_lamaran')->insert($dataInsert);

            // Commit transaksi jika sukses
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Berhasil melamar pekerjaan']);
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
