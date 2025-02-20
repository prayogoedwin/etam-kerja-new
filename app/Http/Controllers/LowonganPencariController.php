<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganPencari;
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
            $lokers = LowonganPencari::select('id', 'judul_lowongan', 'tanggal_start', 'tanggal_end', 'deskripsi')
            ->where('status_id', '1')
            // ->where('deleted_at', null)
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

        return view('backend.lowonganpencari.index', $data);
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
            $data = LowonganPencari::all()->findOrFail($id);
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

            // Cek apakah sudah ada pencari_id yang sama di tabel etam_lamaran
            // $existingLamaran = DB::table('etam_lamaran')
            //     ->where('pencari_id', $userId)
            //     ->where('lowongan_id', $dataLow['id'])
            //     ->first();

            // Kunci baris untuk mencegah race condition
            $existingLamaran = DB::table('etam_lamaran')
                ->where('pencari_id', $userId)
                ->where('lowongan_id', $dataLow->id)
                ->lockForUpdate() // Kunci baris selama transaksi
                ->first();

            // Jika sudah ada, kembalikan error
            if ($existingLamaran) {
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Gagal menyimpan lamaran, anda sudah melamar untuk lowongan ini'
                ]);
            }

            $dataInsert = array(
                'pencari_id' => $userId,
                'lowongan_id' => $dataLow['id'],
                'kabkota_penempatan_id' => $dataLow['kabkota_id'],
                'progres_id' => 4,
                'keterangan' => null
            );

            // Panggil model LowonganPencari dan gunakan fungsi insertLamaran
            $lowonganPencari = new LowonganPencari();
            $result = $lowonganPencari->insertLamaran($dataInsert);

            return response()->json(['success' => true, 'message' => 'Berhasil melamar pekerjaan']);

            // Commit transaksi jika sukses
            DB::commit();
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
