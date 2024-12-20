<?php

namespace App\Http\Controllers;

use App\Models\EtamAk1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Kecamatan;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\UserBkk;
use App\Models\UserPencari;
use App\Models\UserPenyedia;

class BackController extends Controller
{
    //
    public function index()
    {
        $lowonganHariIni = Lowongan::where('status_id', 1)->whereDate('created_at', now())->count();
        $lowonganAktif = Lowongan::where('status_id', 1)->whereNull('deleted_at')->count();
        $lamaran = Lamaran::where('pencari_id', Auth::user()->id)->count();

        $lowonganPending = Lowongan::where('status_id', 0);
        $lamaranBelumProses = Lamaran::where('progres_id', 4);
        $ak1 = EtamAk1::where('tanggal_cetak', now());
        $pencariKerja = UserPencari::where('created_at', now());
        $penyedia = UserPenyedia::where('created_at', now());
        $bkk = UserBkk::where('created_at', now());

        if (Auth::user()->roles[0]['name'] == 'super-admin') {
            $lowonganPending = $lowonganPending->count();
            $lamaranBelumProses = $lamaranBelumProses->count();
            $ak1 = $ak1->count();
            $pencariKerja = $pencariKerja->count();
            $penyedia = $penyedia->count();
            $bkk = $bkk->count();
            return view('backend.dashboard.index', compact('lowonganPending', 'lamaranBelumProses', 'ak1', 'pencariKerja', 'penyedia', 'bkk'));
        }

        if (Auth::user()->roles[0]['name'] == 'pencari-kerja') {
            return view('backend.dashboard.index_pencari', compact('lowonganHariIni', 'lowonganAktif', 'lamaran'));
        }

        if (Auth::user()->roles[0]['name'] == 'penyedia-kerja') {
            $lowonganIds = Lowongan::where('posted_by', Auth::user()->id)->pluck('id');
            $jumlah_lamaran = Lamaran::whereIn('lowongan_id', $lowonganIds)->count();
            return view('backend.dashboard.index_penyedia', compact('lowonganHariIni', 'lowonganAktif', 'jumlah_lamaran'));
        }

        if (Auth::user()->roles[0]['name'] == 'admin-kabkota' || Auth::user()->roles[0]['name'] == 'admin-kabkota-officer') {
            $lowonganPending = $lowonganPending->where('kabkota_id', Auth::user()->admin->kabkota_id)->count();
            $lamaranBelumProses = $lamaranBelumProses->where('kabkota_penempatan_id', Auth::user()->admin->kabkota_id)->count();
            $pencariKerja = $pencariKerja->where('id_kota', Auth::user()->admin->kabkota_id)->count();
            $penyedia = $penyedia->where('id_kota', Auth::user()->admin->kabkota_id)->count();
            $bkk = $bkk->where('id_kota', Auth::user()->admin->kabkota_id)->count();
            return view('backend.dashboard.index_kabkota', compact('lowonganPending', 'lamaranBelumProses', 'pencariKerja', 'penyedia', 'bkk'));
        }

        if (Auth::user()->roles[0]['name'] == 'admin-bkk') {
            return view('backend.dashboard.index_bkk');
        }
    }

    public function sample()
    {
        return view('backend.sample.index');
    }

    public function settingBanner()
    {
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
