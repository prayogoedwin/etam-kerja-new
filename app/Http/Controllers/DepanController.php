<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepanController extends Controller
{
    //index
    public function index()
    {
        return view('depan.depan_index');
    }

    public function bkk(){
        // return view('depan.depan_bkk');
        $sample = array(
            'cek' => 'ini cek',
            'bkk' => 'ini bkk'
        );
        // echo 'ini bkk';

        return view('depan.depan_bkk', $sample);
    }
}
