<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPencari;
use App\Models\UserAdmin;
use App\Models\EtamAk1;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\EtamPencariPendidikan;
use App\Models\EtamPencariKeahlian;

class Ak1PencariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userid = auth()->user()->id;
        $user = UserPencari::where('user_id', $userid)->first();
        return view('backend.ak1.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function printAk1()
    {
        $id = auth()->user()->id;

        // Ambil data user dan relasinya
        $pencari = UserPencari::select(
            'users_pencari.id',
            'users_pencari.user_id',
            'users_pencari.ktp',
            'users_pencari.name',
            'users_pencari.tempat_lahir',
            'users_pencari.tanggal_lahir',
            'users_pencari.id_kota',
            'users_pencari.foto',)
            ->where('users_pencari.user_id', $id)
            ->first();
        // dd($pencari);
        // echo json_encode($pencari);
        // die();

        // $admins = UserAdmin::select(
        //     'user_admins.id',
        //     'user_admins.user_id',
        //     'user_admins.province_id',
        //     'user_admins.kabkota_id',
        // )
        // ->where('kabkota_id', $pencari->id_kota)->first();

        $admins = UserAdmin::with('kabkota')->where('kabkota_id', $pencari->id_kota)->first();
        // dd($admins);
        // echo json_encode($admins);
        // die();

        // // Periksa apakah ada data AK1 yang masih berlaku
        $nakerAk1 = EtamAk1::where('id_user', $pencari->user_id)
            ->where('berlaku_hingga', '>', Carbon::now()) // Jika berlaku_hingga lebih besar dari sekarang
            ->first();
        // dd($nakerAk1);

        if (!$nakerAk1) {
            // Jika tidak ada, buat entri baru
            $uniqueCode = md5($id . Carbon::now()->toDateTimeString());
            $expiredDate = Carbon::now()->addMonths(6);

            // Buat instance baru untuk entri baru
            $nakerAk1 = new EtamAk1();
            $nakerAk1->id_user = $pencari->user_id;
            $nakerAk1->tanggal_cetak = Carbon::now();
            $nakerAk1->berlaku_hingga = $expiredDate;
            $nakerAk1->status_cetak = '0'; // 0 mandiri, 1 cetak admin
            $nakerAk1->unik_kode = $uniqueCode;

            // Tambahkan id_user yang mencetak
            $nakerAk1->dicetak_oleh = auth()->user()->id;

            // Membuat QR Code
            $qrData = route('ak1.view', $nakerAk1->unik_kode);
            $qrCode = QrCode::size(200)->generate($qrData);

            // Menyimpan QR Code ke dalam penyimpanan
            $qrPath = 'qrcodes/' . $uniqueCode . '.svg';
            Storage::disk('public')->put($qrPath, $qrCode); // Menyimpan QR Code di folder storage/app/public/qrcodes
            $nakerAk1->qr = $qrPath;

            // Simpan entri baru
            $nakerAk1->save();
        }

        $pendidikans = EtamPencariPendidikan::select(
            'etam_pencari_pendidikan.id',
            'etam_pendidikan.name as pendidikanteks',
            'etam_jurusan.nama as jurusanteks',
            'etam_pencari_pendidikan.instansi',
            'etam_pencari_pendidikan.tahun'
        )
        ->join('etam_pendidikan', 'etam_pencari_pendidikan.pendidikan_id', '=', 'etam_pendidikan.id')
        ->join('etam_jurusan', 'etam_pencari_pendidikan.jurusan_id', '=', 'etam_jurusan.id')
        ->where('etam_pencari_pendidikan.user_id', $pencari->user_id)
        ->get();

        $keterampilans = EtamPencariKeahlian::select(
            'etam_pencari_keahlian.id',
            'etam_pencari_keahlian.keahlian'
        )
        ->where('etam_pencari_keahlian.user_id', $pencari->user_id)
        ->get();
        // dd($keterampilans);

        // Mengembalikan tampilan untuk cetak AK1
        return view('backend.ak1.printmandiri', compact('pencari', 'nakerAk1', 'pendidikans', 'keterampilans', 'admins'));
    }
}
