<?php

namespace App\Http\Controllers;

use App\Models\Depan; // Import model Depan
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class DepanController extends Controller
{
    //index
    public function index()
    {
        return view('depan.depan_index');
    }

    public function bkk(){
       // Mengambil data yang kolom deleted_at nya NULL (belum di-soft delete)
        $data_bkk = Depan::whereNull('deleted_at')->get();

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

    public function lowongan_kerja(){
        return view('depan.depan_lowongan_kerja');
    }

    public function lowongan_kerja_disabilitas(){
        return view('depan.depan_lowongan_kerja_disabilitas');
    }

    public function infografis(){
        return view('depan.depan_infografis');
    }

    public function galeri(){
        return view('depan.depan_galeri');
    }

    public function berita(){
        return view('depan.depan_berita');
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
        if($decode_rl != 'pencari-kerja' && $decode_rl != 'penyedia-kerja'){
            return abort(404);
        }

        $nm_role = '';
        if($decode_rl == 'pencari-kerja'){
            $nm_role = 'Pencari Kerja';
        } else if($decode_rl == 'penyedia-kerja'){
            $nm_role = 'Penyedia Kerja';
        }

        $depanModel = new Depan();
        $data['agama'] = $depanModel->getAllAgama(); // Mendapatkan semua data agama
        $data['kabkota'] = $depanModel->getKabkotaByProvince();

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
                'status' => 0,
                'message' => 'Email sudah pernah terdaftar'
            ]);
        }

        $userWa = User::where('whatsapp', $request->wa)->first();
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
            'message' => 'lanjut daftar',
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
}
