<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardPimpinanController extends Controller
{
    public function index()
    {
        // $resPencari = Http::get(url('/api/dashboard/pencari'))->json()['data'];
        $resPencari = Http::withOptions(['verify' => false])->get(url('/api/dashboard/pencari'))->json()['data'];
        // echo $resPencari->body();

        // $resLamaran = Http::get(url('/api/dashboard/lamaran'))->json()['data'];
        $resLamaran = Http::withOptions(['verify' => false])->get(url('/api/dashboard/lamaran'))->json()['data'];
        // echo $resLamaran->body();

        // $resPerusahaan = Http::get(url('/api/dashboard/perusahaan'))->json()['data'];
        $resPerusahaan = Http::withOptions(['verify' => false])->get(url('/api/dashboard/perusahaan'))->json()['data'];

        // $resLowongan = Http::get(url('/api/dashboard/lowongan'))->json()['data'];
        $resLowongan = Http::withOptions(['verify' => false])->get(url('/api/dashboard/lowongan'))->json()['data'];

        // $resPenempatan = Http::get(url('/api/dashboard/penempatan'))->json()['data'];
        $resPenempatan = Http::withOptions(['verify' => false])->get(url('/api/dashboard/penempatan'))->json()['data'];

        // $resTopPendidikan = Http::get(url('/api/dashboard/top_pendidikan'))->json()['data'];
        $resTopPendidikan = Http::withOptions(['verify' => false])->get(url('/api/dashboard/top_pendidikan'))->json()['data'];
        // $resTopJurusan = Http::get(url('/api/dashboard/top_jurusan'))->json()['data'];
        $resTopJurusan = Http::withOptions(['verify' => false])->get(url('/api/dashboard/top_jurusan'))->json()['data'];
        // $resTopSektor = Http::get(url('/api/dashboard/top_sektor'))->json()['data'];
        $resTopSektor = Http::withOptions(['verify' => false])->get(url('/api/dashboard/top_sektor'))->json()['data'];

        return view('backend.dashboardpimpinan.index',
        compact('resPencari','resLamaran', 'resPerusahaan', 'resLowongan', 'resPenempatan', 'resTopPendidikan', 'resTopJurusan', 'resTopSektor'));
    }
}
