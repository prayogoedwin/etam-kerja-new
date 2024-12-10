<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPencari;
use App\Models\UserPenyedia;
use App\Models\UserBkk;
use App\Models\EtamInfografis;
use App\Models\EtamBerita;
use App\Models\EtamFaq;
use App\Models\EtamGaleri;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepanController extends Controller
{
    //index
    public function index()
    {
        // return view('depan.depan_index');
        $faq = EtamFaq::all();
        $beritaTerbaru = EtamBerita::select('id', 'name', 'cover', 'status')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->limit(4)
            ->get();

        // Query pencarian berdasarkan parameter
        $lowonganDisetujui15 = Lowongan::where('status_id', 1) // Lowongan yang disetujui
        ->with('postedBy:id,name')
        ->orderBy('tanggal_start', 'desc')
        ->limit(15)
        ->get();

        // dd($lowonganDisetujui15->toArray());

        // Mengirim faq ke view depan_index
        return view('depan.depan_index', compact('faq', 'beritaTerbaru', 'lowonganDisetujui15'));
    }



    public function bkk(){
       // Mengambil data yang kolom deleted_at nya NULL (belum di-soft delete)
        $data_bkk = UserBkk::whereNull('deleted_at')->get();

        // Melempar data ke view
        return view('depan.depan_bkk', ['data_bkk' => $data_bkk]);
        // echo json_encode($data_bkk);
    }

    public function login(){
        return view('depan.depan_login');
    }

    public function register(){
        // Mengambil data agama menggunakan model Depan
        $depanModel = new Depan();
        $data['agama'] = $depanModel->getAllAgama(); // Mendapatkan semua data agama
        $data['kabkota'] = $depanModel->getKabkotaByProvince();

        // Mengirim data agama ke view 'depan.depan_register'
        return view('depan.depan_register', $data);
        // echo json_encode($data);
    }

    public function lowongan_kerja(Request $request)
    {
        // Ambil parameter pencarian
        $judulLowongan = $request->input('judul_lowongan');
        $pendidikanId = $request->input('pendidikan_id');
        $lokasiId = $request->input('kabkota_id');

        // Query pencarian berdasarkan parameter
        $lowonganDisetujui = Lowongan::where('status_id', 1) // Lowongan yang disetujui
            ->where('is_lowongan_disabilitas', 0)
            ->where('deleted_at', null)
            ->when($judulLowongan, function ($query, $judulLowongan) {
                return $query->where('judul_lowongan', 'like', '%' . $judulLowongan . '%');
            })
            ->when($pendidikanId, function ($query, $pendidikanId) {
                return $query->where('pendidikan_id', $pendidikanId);
            })
            ->when($lokasiId, function ($query, $lokasiId) {
                return $query->where('kabkota_id', $lokasiId);
            })
            ->orderBy('tanggal_start', 'desc')
            ->paginate(9); // Pagination with 10 items per page

        // Kirim data hasil pencarian dan filter ke view
        return view('depan.depan_lowongan_kerja', compact('lowonganDisetujui'));
    }


    public function lowongan_kerja_disabilitas(Request $request)
    {
        // Ambil parameter pencarian
        $judulLowongan = $request->input('judul_lowongan');
        $pendidikanId = $request->input('pendidikan_id');
        $lokasiId = $request->input('kabkota_id');

        // Query pencarian berdasarkan parameter
        $lowonganDisetujui = Lowongan::where('status_id', 1) // Lowongan yang disetujui
            ->where('is_lowongan_disabilitas',1)
            ->where('deleted_at', null)
            ->when($judulLowongan, function ($query, $judulLowongan) {
                return $query->where('judul_lowongan', 'like', '%' . $judulLowongan . '%');
            })
            ->when($pendidikanId, function ($query, $pendidikanId) {
                return $query->where('pendidikan_id', $pendidikanId);
            })
            ->when($lokasiId, function ($query, $lokasiId) {
                return $query->where('kabkota_id', $lokasiId);
            })
            ->orderBy('tanggal_start', 'desc')
            ->paginate(9); // Pagination with 10 items per page

        // Kirim data hasil pencarian dan filter ke view
        return view('depan.depan_lowongan_kerja_disabilitas', compact('lowonganDisetujui'));
    }

    public function infografis(){
        $infografis = EtamInfografis::whereNull('deleted_at')
        ->where('status', 1)
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('depan.depan_infografis', compact('infografis'));
    }

    public function galeri(){
        $galeris = EtamGaleri::whereNull('deleted_at')
        ->where('status', 1)
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('depan.depan_galeri', compact('galeris'));
    }

    public function berita(){
        $beritas = EtamBerita::select('id', 'name', 'cover', 'status', 'created_at')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        $data['beritas'] = $beritas;
        // echo json_encode($data);
        // die();
        return view('depan.depan_berita', $data);
    }

    public function show($id)
    {
        $id = decode_url($id);
        $berita = EtamBerita::findOrFail($id);

        return view('depan.depan_berita_detail', compact('berita'));
    }

    public function lowongan_show($id){
        $id = decode_url($id);
        // $lowongan = Lowongan::findOrFail($id);
        // $lowongan = Lowongan::with('userPenyedia')->findOrFail($id); // Eager loading relasi userPenyedia
        $lowongan = Lowongan::findOrFail($id);

        // Lazy loading relasi userPenyedia
        $userPenyedia = $lowongan->userPenyedia;

        // echo json_encode($lowongan);
        // die();

        return view('depan.depan_lowongan_detail', compact('lowongan','userPenyedia'));
    }

    public function daftar_akun(Request $request){
        // dd($request->all());
        $request->validate([
            'role_dipilih' => 'required',
        ]);

        $url_role = encode_url($request->role_dipilih);
        // dd($url_role);

        return redirect()->to('depan/daftar?rl='.$url_role);
    }

    public function daftar(Request $request){
        // Tangkap parameter rl dari URL
        $rl = $request->input('rl'); // atau bisa juga menggunakan $request->query('rl')
        // echo 'Parameter rl: ' . $rl;
        $decode_rl = decode_url($rl);
        if($decode_rl != 'pencari-kerja' && $decode_rl != 'penyedia-kerja' && $decode_rl != 'admin-bkk'){
            return abort(404);
        }

        $nm_role = '';
        if($decode_rl == 'pencari-kerja'){
            $nm_role = 'Pencari Kerja';
        } else if($decode_rl == 'penyedia-kerja'){
            $nm_role = 'Penyedia Kerja';
        } else if($decode_rl == 'admin-bkk'){
            $nm_role = 'Bursa Kerja Khusus';
        }

        $data['agama'] = getAgama(); // Mendapatkan semua data agama
        $data['kabkota'] = getKabkota();
        $data['jabatans'] = getJabatan();

        $data['dt'] = array(
            'role' => $decode_rl,
            'role_name' => $nm_role
        );

        session()->forget('email_registered');
        // dd(session('email_registered'));

        // dd($data);
        return view('depan.depan_registerbaru', $data);
        // echo json_encode($data);
    }

    public function cek_awal_akun(Request $request){
        // return response()->json([
        //     'email' => $request->email,
        //     'wa' => $request->wa,
        // ]);

        $userEmail = User::where('email', $request->email)->first();
        if($userEmail){
            return response()->json([
                'status' => 5,
                'message' => 'Email sudah pernah terdaftar'
            ]);
        }

        $userWa = User::where('whatsapp', $request->wa)
        ->whereNull('deleted_at')
        ->first();

        if($userWa){
            return response()->json([
                'status' => 0,
                'message' => 'Nomor whatsapp sudah pernah terdaftar'
            ]);
        }

        $role = Role::where('name', $request->role)->first();

        //create users
        $user = User::create([
            'name' => $request->email,
            'email' => $request->email,
            'whatsapp' => $request->wa,
            'password' => $request->password
        ]);
        $user->syncRoles($role->name);

        $otp = generateOtp();
        $user->update([
            'otp' => $otp,
            // 'otp_created_at' => now()
        ]);
        // dd($userWa);
        sendWa($user->whatsapp, 'Lanjutkan pendaftaran dengan memasukkan Kode OTP berikut : *' . $otp . '*');

        session(['email_registered' => $request->email]);
        // dd(session('email_registered'));

        return response()->json([
            'status' => 1,
            'message' => 'Email dan nomor Whatsapp dapat digunakan',
            'data' => $user
        ]);
    }

    public function cek_awal_otp(Request $request){

        $cek = User::where([
            ['email', '=', $request->email_registered],
            ['otp', '=', $request->otp]
        ])->first();

        if(!$cek){
            return response()->json([
                'status' => 0,
                'message' => 'Kode OTP salah'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Verifikasi kode OTP berhasil',
            'session_email' => session('email_registered')
        ]);
    }

    public function akhir_daftar_akun(Request $request){

        $imel = session('email_registered');
        $user = User::where('email', $imel)->first();

        // dd($user->id);

        DB::beginTransaction();
        try {
            // create affiliator
            UserPencari::create([
                'user_id' => $user->id,
                'ktp' => $request->nik,
                'name' => $request->nama_lengkap,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'gender' => $request->gender_id,
                'id_provinsi' => '64',
                'id_kota' => $request->kabkota_id,
                'id_kecamatan' => $request->kecamatan_id,
                // 'id_desa' => $request->desa_id,
                'alamat' => $request->alamat,
                'kodepos' => $request->kodepos,
                'id_pendidikan' => $request->pendidikan_id,
                'id_jurusan' => $request->jurusan_id,
                'tahun_lulus' => $request->tahun_lulus,
                'id_status_perkawinan' => $request->status_perkawinan_id,
                'id_agama' => $request->agama_id,
                'id_jabatan_harapan' => $request->jabatan_harapan_id,
                'foto' => null,
                'status_id' => 1,
                'is_alumni_bkk' => 0,
                'bkk_id' => null,
                'toket' => null,
                'disabilitas' => $request->disabilitas,
                'jenis_disabilitas' => $request->jenis_disabilitas,
                'keterangan_disabilitas' => $request->keterangan_disabilitas,
                'posted_by' => $user->id,
                'created_at' => date('Y-m-d H:i:s'),
                // 'updated_at',
                // 'deleted_at',
                'is_diterima' => 0,
                'medsos' => $request->medsos
            ]);

            DB::commit();

            return redirect()->to('login')->with('success', 'Berhasil membuat akun silahkan login');
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

    public function akhir_daftar_akun_perush(Request $request){
        $imel = session('email_registered');
        $user = User::where('email', $imel)->first();

        // dd($user->id);

        DB::beginTransaction();
        try {
            // create affiliator
            UserPenyedia::create([
                'user_id' => $user->id,
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
                'shared_by_id' => null,
                'posted_by' => $user->id,
                'created_at' => date('Y-m-d H:i:s'),
                // 'updated_at',
                // 'deleted_at',
            ]);

            DB::commit();

            return redirect()->to('login')->with('success', 'Berhasil membuat akun perusahaan silahkan login');
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

    public function akhir_daftar_akun_bkk(Request $request){
        $imel = session('email_registered');
        $user = User::where('email', $imel)->first();

        // dd($user->id);

        DB::beginTransaction();
        try {
            // create affiliator
            UserBkk::create([
                'user_id' => $user->id,
                'no_sekolah' => $request->no_sekolah,
                'id_sekolah' => $request->id_sekolah,
                'name' => $request->name,
                'website' => $request->website,
                'id_provinsi' => $request->provinsi_id,
                'id_kota' => $request->kabkota_id,
                'id_kecamatan' => $request->kecamatan_id,
                'alamat' => $request->alamat,
                'kodepos' => $request->kodepos,
                // 'no_bkk' => $request->no_bkk,
                'tanggal_aktif_bkk' => date('Y-m-d H:i:s'),
                'tanggal_non_aktif_bkk' => null,
                'telpon' => $request->telpon,
                'hp' => $request->hp,
                'contact_person' => $request->contact_person,
                'jabatan' => $request->jabatan,
                'foto' => null,
                'status_id' => 1,
                'tanggal_register' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                // 'updated_at',
                // 'deleted_at',
            ]);

            DB::commit();

            return redirect()->to('login')->with('success', 'Berhasil membuat akun BKK silahkan login');
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

    public function getKabkotaByProv($prov_id){
        $kabkota = DB::table('etam_kabkota')->where('province_id', $prov_id)->get();

        return response()->json($kabkota);
    }

    public function getKecamatanByKabkota($kabkota_id)
    {
        // Misal mengambil data dari tabel 'etam_kecamatan' berdasarkan kabkota_id
        $kecamatan = DB::table('etam_kecamatan')->where('regency_id', $kabkota_id)->get();

        return response()->json($kecamatan);
    }

    public function getDesaByKec($kec_id){
        $desa = DB::table('etam_desa')->where('district_id', $kec_id)->get();

        return response()->json($desa);
    }

    public function getJurusanByPendidikan($pendidikan_id){
        $pendidikan = DB::table('etam_jurusan')->where('id_pendidikans', $pendidikan_id)->get();

        return response()->json($pendidikan);
    }
}
