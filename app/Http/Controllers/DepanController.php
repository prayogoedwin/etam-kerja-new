<?php

namespace App\Http\Controllers;

use App\Models\Depan; // Import model Depan
use App\Models\User;
use Illuminate\Http\Request;

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
        if($decode_rl != '2' && $decode_rl != '3'){
            return abort(404);
        }

        $nm_role = '';
        if($decode_rl == '2'){
            $nm_role = 'Pencari Kerja';
        } else if($decode_rl == '3'){
            $nm_role = 'Penyedia Kerja';
        }

        $data['dt'] = array(
            'role' => $decode_rl,
            'role_name' => $nm_role
        );
        // dd($data);
        return view('depan.depan_registerbaru', $data);
    }

    public function cek_awal_akun(Request $request){
        // return response()->json([
        //     'email' => $request->email,
        //     'wa' => $request->wa,
        // ]);

        $user = User::where('email', $request->email)->first();
        if($user){
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

        //create users
        $user = User::create([
            'name' => $request->nama_merchant,
            'email' => $request->email,
            'no_wa' => $request->no_wa,
            'password' => 'merchant'
        ]);
        $user->syncRoles($role->name);

        // $otp = generateOtp();
        // $userWa->update([
        //     'otp' => $otp,
        //     // 'otp_created_at' => now()
        // ]);
        // dd($userWa);
        // sendWa($userWa->whatsapp, 'Lanjutkan pendaftaran dengan memasukkan Kode OTP berikut : ' . $otp);

        return response()->json([
            'status' => 1,
            'message' => 'lanjut daftar'
        ]);
    }
}
