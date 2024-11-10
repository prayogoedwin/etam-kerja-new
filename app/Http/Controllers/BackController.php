<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackController extends Controller
{
    //
    public function index () {
        return view('backend.dashboard.index');
    }

    public function sample () {
        return view('backend.sample.index');
    }

    public function settingBanner () {
        return view('backend.setting.banner.index');
    }
}
