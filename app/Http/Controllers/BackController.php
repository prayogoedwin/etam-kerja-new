<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackController extends Controller
{
    //
    public function index () {
        if(Auth::user()->roles[0]['name'] == 'super-admin'){
            return view('backend.dashboard.index');
        }

        if(Auth::user()->roles[0]['name'] == 'pencari-kerja'){
            return view('backend.dashboard.index_pencari');
        }

        if(Auth::user()->roles[0]['name'] == 'penyedia-kerja'){
            return view('backend.dashboard.index_penyedia');
        }

        
        
    }

    public function sample () {
        return view('backend.sample.index');
    }

    public function settingBanner () {
        return view('backend.setting.banner.index');
    }
}
