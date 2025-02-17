<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\ProfilPencari;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LowonganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $lokers = Lowongan::select(
                'etam_lowongan.id',
                'etam_lowongan.judul_lowongan',
                'etam_lowongan.tanggal_start',
                'etam_lowongan.tanggal_end',
                'etam_lowongan.deskripsi',
                'etam_progres.name as progres_name' // Menambahkan kolom 'name' dari tabel etam_progres
            )
            ->join('etam_progres', 'etam_lowongan.status_id', '=', 'etam_progres.kode') // Join tabel
            ->whereNull('etam_lowongan.deleted_at') // Memastikan data tidak terhapus
            ->where('etam_progres.modul', 'lowongan') // Kondisi where
            ->where('etam_lowongan.posted_by', auth()->user()->id) // Kondisi where
            ->get();

            return DataTables::of($lokers)
                ->addIndexColumn()
                ->addColumn('options', function ($loker) {
                    // <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $data->id . ')">Edit</button>
                    return '
                        <a href="' . route('lowongan.pelamar', encode_url($loker->id)) . '" class="btn btn-info btn-sm">Lihat Pelamar</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $loker->id . ')">Delete</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        $data['jabatans'] = getJabatan();
        $data['sektors'] = getSektor();
        $data['kabkotas'] = getKabkota();
        // $data['pendidikans'] = getPendidikan();
        $data['maritals'] = getMarital();

        return view('backend.lowongan.index', $data);
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
        // Validasi data
        $validator = Validator::make($request->all(), [
            'is_lowongan_disabilitas' => 'required|integer',
            'jabatan_id' => 'required|integer',
            'sektor_id' => 'required|integer',
            'tanggal_start' => 'required|date',
            'tanggal_end' => 'required|date',
            'judul_lowongan' => 'required|string|max:255',
            'kabkota_id' => 'required|integer',
            'lokasi_penempatan_text' => 'required|string',
            'kisaran_gaji' => 'required|integer',
            'kisaran_gaji_akhir' => 'nullable|integer',
            'jumlah_pria' => 'required|integer',
            'jumlah_wanita' => 'required|integer',
            'deskripsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            // Menyimpan data ke tabel users
            $user = Lowongan::create([
                'jabatan_id' => $request->jabatan_id,
                'sektor_id' => $request->sektor_id,
                'tanggal_start' => $request->tanggal_start,
                'tanggal_end' => $request->tanggal_end,
                'judul_lowongan' => $request->judul_lowongan,
                'kabkota_id' => $request->kabkota_id,
                'lokasi_penempatan_text' => $request->lokasi_penempatan_text,
                'kisaran_gaji' => $request->kisaran_gaji,
                'kisaran_gaji_akhir' => $request->kisaran_gaji_akhir,
                'jumlah_pria' => $request->jumlah_pria,
                'jumlah_wanita' => $request->jumlah_wanita,
                'deskripsi' => $request->deskripsi,
                'pendidikan_id' => $request->pendidikan_id,
                'jurusan_id' => $request->jurusan_id,
                'marital_id' => $request->marital_id,
                'is_lowongan_disabilitas' => $request->is_lowongan_disabilitas,
                'posted_by' => $userId,
                'updated_by' => $userId,
            ]);

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            // DB::rollBack();
            // return back()->with('error', $th->getMessage());
            DB::rollBack();
            Log::error($th);
            // return back()->with('error', $th->getMessage());
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage()
            ]);
        }


        // Simpan data lowongan
        // $lowongan = EtamLowongan::create($validatedData);
        // return response()->json($lowongan, 201); // Kode 201 untuk Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    public function pelamar(Request $request, string $id)
    {
        if ($request->ajax()) {
            $pelamars = Lamaran::select(
                'etam_lamaran.id',
                'users.id as user_id',
                'users.email',
                'users.whatsapp',
                'users_pencari.name',
                'etam_lamaran.keterangan',
                'etam_lamaran.created_at',
                'etam_progres.kode as kodelamaran',
                'etam_progres.name as statuslamaran' // Kolom statuslamaran dari tabel etam_progres
            )
            ->join('users', 'etam_lamaran.pencari_id', '=', 'users.id') // Join dengan tabel users
            ->join('users_pencari', 'users.id', '=', 'users_pencari.user_id') // Join dengan tabel users_pencari
            ->join('etam_progres', 'etam_lamaran.progres_id', '=', 'etam_progres.kode') // Join dengan tabel etam_progres
            ->where('etam_lamaran.lowongan_id', $id) // Filter berdasarkan lowongan_id
            ->where('etam_progres.modul', 'lamaran') // Filter berdasarkan modul pada etam_progres
            ->whereNull('etam_lamaran.deleted_at') // Pastikan data tidak terhapus
            ->get();

            return DataTables::of($pelamars)
                ->addIndexColumn()
                ->editColumn('created_at', function ($pelamar) {
                    return $pelamar->created_at->format('Y M d H:i:s');
                })
                ->editColumn('statuslamaran', function ($pelamar) {
                    $sts_lamar = '-';
                    if($pelamar->kodelamaran == '1' || $pelamar->kodelamaran == '2' || $pelamar->kodelamaran == '4'){
                        $sts_lamar = '<span class="badge rounded-pill bg-warning">'.$pelamar->statuslamaran.'</span>';
                    } else if($pelamar->kodelamaran == '5'){
                        $sts_lamar = '<span class="badge rounded-pill bg-danger">'.$pelamar->statuslamaran.'</span>';
                    } else if($pelamar->kodelamaran == '3'){
                        $sts_lamar = '<span class="badge rounded-pill bg-success">'.$pelamar->statuslamaran.'</span>';
                    }
                    return $sts_lamar;
                })
                ->addColumn('options', function ($pelamar) {
                    $fpel = "'" . short_encode_url($pelamar->user_id) . "'";
                    return '
                        <button class="btn btn-primary btn-sm" onclick="showDetailModal(' . $fpel . ')">Detail</button>
                        <input type="checkbox" class="pelamar-checkbox" value="' . $pelamar->id . '">
                    ';
                })
                ->rawColumns(['statuslamaran','options'])
                ->make(true);
        }

        $real_id = decode_url($id);
        $data['lowongan'] = Lowongan::find($real_id);
        $data['lowongan_id'] = $real_id;
        $data['progreses'] = getProgresLamaran();

        // return view('backend.lowongan.pelamar', ['lowongan_id' => $id]);
        return view('backend.lowongan.pelamar', $data);
        // echo json_encode($data);
    }

    public function detailpelamar(string $id){
        $userid = short_decode_url($id);
        // dd($userid);
        // $data['pelamar'] = DB::table('users_pencari')
        //     ->join('users', 'users_pencari.user_id', '=', 'users.id')
        //     ->where('users.id', $real_id)
        //     ->first();

        // return view('backend.lowongan.detailpelamar', $data);
        // $pelamar = ProfilPencari::with('user')->find($id);
        $pelamar = ProfilPencari::with('user')->where('user_id', $userid)->first();
        $pelamar->provinsi = $pelamar->provinsi();
        $pelamar->kabupaten = $pelamar->kabupaten();
        $pelamar->kecamatan = $pelamar->kecamatan();
        $pelamar->desa = $pelamar->desa();
        $pelamar->pendidikan = $pelamar->pendidikan();
        $pelamar->jurusan = $pelamar->jurusan();

        if(!$pelamar){
            return response()->json([
                'status' => 0,
                'message' => 'Data not found'
            ]);
        }

        return response()->json([
            'status' => 1,
            'data' => $pelamar
        ]);
    }

    public function bulkupdatepelamar(Request $request){
        $ids = $request->input('ids');
        $action = $request->input('action');
        $keterangan = $request->input('keterangan');

        if (!$ids || !$action) {
            return response()->json(['message' => 'Data tidak valid'], 400);
        }

        // Update status dan keterangan
        Lamaran::whereIn('id', $ids)->update([
            'progres_id' => $action,
            'keterangan' => $keterangan
        ]);

        //if $action == 3, update field is_diterima at table users_pencari
        if($action == 3){
            $user_ids = Lamaran::whereIn('id', $ids)->pluck('pencari_id');
            ProfilPencari::whereIn('user_id', $user_ids)->update([
                'is_diterima' => 1
            ]);
        }

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function softdelete($id)
    {
        try {
            // Cari admin berdasarkan ID
            $data = Lowongan::findOrFail($id);

            // Set is_deleted = 1 untuk soft delete admin
            $data->deleted_at = now();
            $data->save();  // Simpan perubahan
            // $data->delete();
            return response()->json(['success' => true, 'message' => 'hapus data  berhasil.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error A: ' . $e->getMessage()]);
        }
    }
}
