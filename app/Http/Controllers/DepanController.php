<?php

namespace App\Http\Controllers;

use App\Models\Depan; // Import model Depan
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
}
