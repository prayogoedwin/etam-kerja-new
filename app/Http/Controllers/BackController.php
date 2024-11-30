<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Kecamatan;

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

        if(Auth::user()->roles[0]['name'] == 'admin-kabkota'){
            return view('backend.dashboard.index_kabkota');
        }

        if(Auth::user()->roles[0]['name'] == 'admin-bkk'){
            return view('backend.dashboard.index_bkk');
        }

    }

    public function sample () {
        return view('backend.sample.index');
    }

    public function settingBanner () {
        return view('backend.setting.banner.index');
    }

    public function ubahPassword(Request $request)
    {
        // dd($request->all());
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => 0, 'message' => 'Password saat ini salah.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['status' => 1, 'message' => 'Password berhasil diubah.']);
    }

    public function getKecamatan(Request $request)
    {
        $kabkotaId = $request->query('kabkota_id');

        if (!$kabkotaId) {
            return response()->json(['message' => 'kabkota_id is required'], 400);
        }

        try {
            // Ambil kecamatan berdasarkan kabkota_id
            $kecamatanList = Kecamatan::where('regency_id', $kabkotaId)
                ->select('id', 'name') // Hanya ambil field yang diperlukan
                ->get();

            return response()->json(['data' => $kecamatanList], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    
}
