<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPenyedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Lowongan;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = UserPenyedia::select(
                '*' // Menambahkan kolom 'name' dari tabel etam_progres
            )
            // ->join('etam_progres', 'etam_lowongan.status_id', '=', 'etam_progres.kode') // Join tabel
            ->whereNull('deleted_at') // Memastikan data tidak terhapus
            ->where('by_bkk_id', auth()->user()->id) // Kondisi where
            ->get();

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('options', function ($datas) {


                    return '
                        <a href="' . route('perusahaan.lowongan', encode_url($datas->user_id)) . '" class="btn btn-info btn-sm">Lowongan</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $datas->user_id . ')">Hapus</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.perusahaan.index');
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function daftarperusahaan(Request $request){
        $rl = $request->input('rl'); // atau bisa juga menggunakan $request->query('rl')
        $decode_rl = decode_url($rl);

        // dd($decode_rl);
        // die();
        if (!in_array($decode_rl, ['pencari-kerja', 'penyedia-kerja', 'admin-bkk', 'admin-blk'])) {
            return abort(404);
        }

        $nm_role = '';
        if ($decode_rl == 'pencari-kerja') {
            $nm_role = 'Pencari Kerja';
        } else if ($decode_rl == 'penyedia-kerja') {
            $nm_role = 'Penyedia Kerja';
        } else if ($decode_rl == 'admin-bkk') {
            $nm_role = 'BKK';
        } else if ($decode_rl == 'admin-blk') {
            $nm_role = 'BLK';
        }


        $data['agama'] = getAgama(); // Mendapatkan semua data agama
        $data['kabkota'] = getKabkota();

        $data['dt'] = array(
            'role' => $decode_rl,
            'role_name' => $nm_role
        );

        session()->forget('email_registered');

        return view('backend.perusahaan.daftar_bybkk', $data);
    }

    public function akhir_daftar_akun_perush_bybkk(Request $request)
    {
        $imel = session('email_registered');
        $user = User::where('email', $imel)->first();

        // dd($user->id);

        DB::beginTransaction();
        try {
            $userId = Auth::user()->id;
            UserPenyedia::where('user_id', $user->id)
                ->update([
                // 'user_id' => $user->id,
                'name' => $request->nama_perusahaan,
                'luar_negri' => $request->luar_negri,
                'deskripsi' => $request->deskripsi,
                'jenis_perusahaan' => $request->jenis_perusahaan,
                'nomor_sip3mi' => null,
                'nib' => $request->nib,
                'id_sektor' => $request->sektor_id,
                'id_provinsi' => $request->provinsi_id,
                'id_kota' => $request->kabkota_id,
                'id_kecamatan' => $request->kecamatan_id,
                // 'id_desa' => $request->desa_id,
                'alamat' => $request->alamat,
                'kodepos' => $request->kodepos,
                'telpon' => $request->telpon,
                'jabatan' => $request->jabatan,
                'website' => $request->website,
                'status_id' => 1,
                'foto' => null,
                'by_bkk_id' => $userId,
                'shared_by_id' => null,
                'posted_by' => $user->id,
                'created_at' => date('Y-m-d H:i:s'),
                // 'updated_at',
                // 'deleted_at',
            ]);

            DB::commit();

            // return redirect()->to('login')->with('success', 'Berhasil membuat akun perusahaan silahkan login');
            return redirect()->route('perusahaan.index')->with('success', 'Berhasil membuat akun');
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
    }

    public function softdelete($userid)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($userid);

            // soft delete user (mengisi deleted_at otomatis jika pakai SoftDeletes)
            $user->deleted_at = now();
            $user->save();

            // jika relasi hasOne
            if ($user->penyedia) {
                $user->penyedia->deleted_at = now();
                $user->penyedia->save();
            }

            // jika relasi hasMany, pakai loop / update
            // $user->penyedias()->update(['deleted_at' => now()]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'hapus data berhasil.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error A: ' . $e->getMessage()]);
        }
    }

    public function cekaja(){
        return redirect()->route('perusahaan.index')->with('success', 'Berhasil membuat akun perusahaan');
    }

    public function perusahaan_lowongan(Request $request, $perusahaan_id){
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
            ->where('etam_lowongan.posted_by', decode_url($perusahaan_id)) // Kondisi where
            ->where('etam_lowongan.tipe_lowongan', '2')
            ->get();

            return DataTables::of($lokers)
                ->addIndexColumn()
                ->addColumn('options', function ($loker) {
                    // <a href="' . route('lowongan.pelamar', encode_url($loker->id)) . '" class="btn btn-info btn-sm">Lihat Pelamar</a>
                    // <a href="javascript:void(0)" onclick="showData(' . $loker->id . ')" class="btn btn-warning btn-sm">Edit</a>
                    return '
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
        $data['perusahaan_id'] = $perusahaan_id;

        return view('backend.perusahaan.perusahaan_lowongan', $data);
    }

    public function store_lowongan(Request $request){
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
            // $userId = auth()->user()->id;

            $perusahaan_id = decode_url($request->perusahaan_id);
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
                'tipe_lowongan' => '2', //2 default lowongan bkk
                'posted_by' => $perusahaan_id,
                // 'updated_by' => $userId,
            ]);

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {

            DB::rollBack();
            Log::error($th);

            return response()->json([
                'status' => 0,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function softdelete_lowongan($id){
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
