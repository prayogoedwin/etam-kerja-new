<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function ak3titik1(){
        return view('backend.rekap.ak3.satu');
    }

    public function ak3titik2(){
        return view('backend.rekap.ak3.dua');
    }

    public function ak3titik4(){
        return view('backend.rekap.ak3.empat');
    }

    public function ak3titik7(){
        return view('backend.rekap.ak3.tuju');
    }

    public function ak3titik8(){
        return view('backend.rekap.ak3.delapan');
    }
}
