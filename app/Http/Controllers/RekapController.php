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

    public function ak3titik3(){
        return view('backend.rekap.ak3.tiga');
    }

    public function ak3titik4(){
        return view('backend.rekap.ak3.empat');
    }

    public function ak3titik5(){
        return view('backend.rekap.ak3.lima');
    }

    public function ak3titik6(){
        return view('backend.rekap.ak3.enam');
    }
    
    public function ak3titik7(){
        return view('backend.rekap.ak3.tuju');
    }

    public function ak3titik8(){
        return view('backend.rekap.ak3.delapan');
    }
}
