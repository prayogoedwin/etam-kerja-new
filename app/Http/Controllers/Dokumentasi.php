<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
class Dokumentasi extends Controller
{

    public function index(){
        return view('docs.index');
    }

    public function activity_penempatan_kerja(){
        return view('docs.penempatan');
    }

    public function penempatan_kerja(){
        return view('docs.flow-penempatan');
    }

    public function magang_pemerintah(){
        return view('docs.flow-magang-pemerintah');
    }

    public function magang_mandiri(){
        return view('docs.flow-magang-mandiri');
    }

    public function job_fair(){
        return view('docs.flow-job-fair');
    }
    

}