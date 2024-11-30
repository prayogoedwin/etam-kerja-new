<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Back; // Import model Depan
use App\Models\EtamAk1;
use App\Models\UserPencari;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables


class Ak1Controller extends Controller
{
    //

    public function daftar_akun(Request $request)
    {
        $request->validate([
            'role_dipilih' => 'required',
        ]);

        $url_role = encode_url($request->role_dipilih);

        return redirect()->to('back/daftar?rl=' . $url_role);
    }

    public function daftar(Request $request)
    {
        $rl = $request->input('rl'); // atau bisa juga menggunakan $request->query('rl')
        $decode_rl = decode_url($rl);

        if (!in_array($decode_rl, ['tenaga-kerja', 'penyedia-kerja', 'admin-bkk', 'admin-blk'])) {
            return abort(404);
        }

        $nm_role = '';
        if ($decode_rl == 'tenaga-kerja') {
            $nm_role = 'Tenaga Kerja';
        } else if ($decode_rl == 'penyedia-kerja') {
            $nm_role = 'Penyedia Kerja';
        } else if ($decode_rl == 'admin-bkk') {
            $nm_role = 'BKK';
        } else if ($decode_rl == 'admin-blk') {
            $nm_role = 'BLK';
        }


        $depanModel = new Back();
        $data['agama'] = $depanModel->getAllAgama(); // Mendapatkan semua data agama
        $data['kabkota'] = $depanModel->getKabkotaByProvince();

        $data['dt'] = array(
            'role' => $decode_rl,
            'role_name' => $nm_role
        );

        session()->forget('email_registered');
        // dd(session('email_registered'));

        // dd($data);
        return view('backend.ak1.create', $data);
        // echo json_encode($data);
    }


    public function cek_awal_akun(Request $request)
    {
        // return response()->json([
        //     'email' => $request->email,
        //     'wa' => $request->wa,
        // ]);

        $userEmail = User::where('email', $request->email)->first();
        if ($userEmail) {
            return response()->json([
                'status' => 0,
                'message' => 'Email sudah pernah terdaftar'
            ]);
        }

        $userWa = User::where('whatsapp', $request->wa)->first();
        if ($userWa) {
            return response()->json([
                'status' => 0,
                'message' => 'Nomor whatsapp sudah pernah terdaftar'
            ]);
        }

        $role = Role::where('name', $request->role)->first();

        //create users
        $user = User::create([
            'name' => $request->email,
            'email' => $request->email,
            'whatsapp' => $request->wa,
            'password' => $request->password
        ]);
        $user->syncRoles($role->name);

        $otp = generateOtp();
        $user->update([
            'otp' => $otp,
            // 'otp_created_at' => now()
        ]);
        // dd($userWa);
        sendWa($user->whatsapp, 'Lanjutkan pendaftaran dengan memasukkan Kode OTP berikut : *' . $otp . '*');

        session(['email_registered' => $request->email]);
        // dd(session('email_registered'));

        return response()->json([
            'status' => 1,
            'message' => 'Email dan nomor Whatsapp dapat digunakan',
            'data' => $user
        ]);
    }

    public function cek_awal_otp(Request $request)
    {

        $cek = User::where([
            ['email', '=', $request->email_registered],
            ['otp', '=', $request->otp]
        ])->first();

        if (!$cek) {
            return response()->json([
                'status' => 0,
                'message' => 'Kode OTP salah'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Verifikasi kode OTP berhasil',
            'session_email' => session('email_registered')
        ]);
    }

    public function akhir_daftar_akun(Request $request)
    {
        $imel = session('email_registered');
        $user = User::where('email', $imel)->first();

        DB::beginTransaction();
        try {
            // create affiliator
            UserPencari::create([
                'user_id' => $user->id,
                'ktp' => $request->nik,
                'name' => $request->nama_lengkap,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'gender' => $request->gender_id,
                'id_provinsi' => '64',
                'id_kota' => $request->kabkota_id,
                'id_kecamatan' => $request->kecamatan_id,
                'id_desa' => $request->desa_id,
                'alamat' => $request->alamat,
                'kodepos' => $request->kodepos,
                'id_pendidikan' => $request->pendidikan_id,
                'id_jurusan' => $request->jurusan_id,
                'tahun_lulus' => $request->tahun_lulus,
                'id_status_perkawinan' => $request->status_perkawinan_id,
                'id_agama' => $request->agama_id,
                'foto' => null,
                'status_id' => 1,
                'is_alumni_bkk' => 0,
                'bkk_id' => null,
                'toket' => null,
                'disabilitas' => null,
                'jenis_disabilitas' => null,
                'keterangan_disabilitas' => null,
                'posted_by' => $user->id,
                'created_at' => date('Y-m-d H:i:s'),
                'is_diterima' => 0,
                'medsos' => $request->medsos,
                'status_saat_ini' => $request->status_saat_ini,
                'sektor_pekerjaan_saat_ini' => $request->status_saat_ini === '1' ? $request->sektor_pekerjaan_saat_ini : null,
                'jam_kerja' => $request->status_saat_ini === '1' ? $request->jam_kerja : null,
                'gaji' => $request->status_saat_ini === '1' ? $request->gaji : null,
            ]);

            DB::commit();

            // Redirect ke halaman ak1.new atau ak1.existing
            return redirect()->route('ak1.existing')->with('success', 'Berhasil membuat akun');
            // Atau bisa juga mengarahkan ke halaman ak1.existing jika itu yang Anda inginkan
            // return redirect()->route('ak1.existing')->with('success', 'Berhasil membuat akun, silakan login');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function cetakExisting(Request $request)
    {
        $user = null;
        if ($request->has('ktp')) {
            $user = UserPencari::where('ktp', $request->ktp)->first();
        }
        return view('backend.ak1.existing', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        // dd($request);
        // die();
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|in:L,P',
            'id_provinsi' => '64',
            'kabkota_id' => 'required|integer',
            'kecamatan_id' => 'required|integer',
            'desa_id' => 'required|string|max:10',
            'alamat' => 'required|string|max:200',
            'kodepos' => 'required|string|max:5',
            'pendidikan_id' => 'required|integer',
            'jurusan_id' => 'required|integer',
            'tahun_lulus' => 'required|integer',
            'status_perkawinan_id' => 'required',
            'agama_id' => 'required|integer',
            'medsos' => 'required|string|max:200',
            'status_kerja_id' => 'required|integer',
            'sektor_pekerjaan_saat_ini' => 'nullable|integer',
            'jam_kerja' => 'nullable|integer',
            'gaji' => 'nullable|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // dd($request);
        // die();

        // Update data user
        $user->name = $request->name;
        $user->save();

        // Update data pencari kerja
        $pencari = $user->pencari;

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($pencari->foto && Storage::disk('public')->exists($pencari->foto)) {
                Storage::disk('public')->delete($pencari->foto);
            }

            // Simpan foto baru
            $fotoPath = $request->file('foto')->store('profile_photos', 'public');
            $pencari->foto = $fotoPath;
        }

        // Update atribut pencari
        $pencari->name = $request->name;
        $pencari->tempat_lahir = $request->tempat_lahir;
        $pencari->tanggal_lahir = $request->tanggal_lahir;
        $pencari->gender = $request->gender;
        $pencari->id_kota = $request->kabkota_id;
        $pencari->id_kecamatan = $request->kecamatan_id;
        $pencari->id_desa = $request->desa_id;
        $pencari->alamat = $request->alamat;
        $pencari->kodepos = $request->kodepos;
        $pencari->id_pendidikan = $request->pendidikan_id;
        $pencari->id_jurusan = $request->jurusan_id;
        $pencari->tahun_lulus = $request->tahun_lulus;
        $pencari->id_status_perkawinan = $request->status_perkawinan_id;
        $pencari->id_agama = $request->agama_id;
        $pencari->medsos = $request->medsos;
        $pencari->status_saat_ini = $request->status_kerja_id;

        // Kondisional untuk pekerjaan jika status kerja aktif
        if ($request->status_kerja_id == 1) {
            $pencari->sektor_pekerjaan_saat_ini = $request->sektor_pekerjaan_saat_ini;
            $pencari->jam_kerja = $request->jam_kerja;
            $pencari->gaji = $request->gaji;
        } else {
            $pencari->sektor_pekerjaan_saat_ini = null;
            $pencari->jam_kerja = null;
            $pencari->gaji = null;
        }

        $pencari->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }



    public function printAk1($id)
    {
        $id = Auth::user()->id; // Mendapatkan ID pengguna yang sedang login
        // $userAdmin = UserAdmin::where('user_id', $id)->first(); // Mencari data UserAdmin berdasarkan user_id

        // Ambil data user dan relasinya
        $pencari = UserPencari::findOrFail($id);
    
        // Periksa apakah ada data AK1 yang masih berlaku
        $nakerAk1 = EtamAk1::where('id_user', $pencari->user_id)
            ->where('berlaku_hingga', '>', Carbon::now()) // Jika berlaku_hingga lebih besar dari sekarang
            ->first();
    
        if (!$nakerAk1) {
            // Jika tidak ada, buat entri baru
            $uniqueCode = md5($id . Carbon::now()->toDateTimeString());
            $expiredDate = Carbon::now()->addMonths(6);
    
            // Buat instance baru untuk entri baru
            $nakerAk1 = new EtamAk1();
            $nakerAk1->id_user = $pencari->user_id;
            $nakerAk1->tanggal_cetak = Carbon::now();
            $nakerAk1->berlaku_hingga = $expiredDate;
            $nakerAk1->status_cetak = '1'; // 1 = mandiri
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

        $pendidikan = array();
        $keterampilan = array();
    
        // Mengembalikan tampilan untuk cetak AK1
        return view('backend.ak1.print', compact('pencari', 'nakerAk1', 'pendidikan', 'keterampilan'));
    }
    

    public function printAk1TenagaKerja($id)
    {
        // Ambil data user dan relasinya
        $user = User::with('pencari')->findOrFail($id);

        // Periksa apakah pengguna yang sedang login memiliki izin untuk mengakses data ini
        if (auth()->user()->id !== $user->id) {
            abort(403, 'Unauthorized action.'); // Kode 403: Forbidden
        }

        // Ambil data status kerja, pendidikan, keterampilan, dan pengalaman
        $statusKerjas = getStatusKerja();
        $pendidikan = NakerPencariPendidikan::select(
            'naker_pencari_pendidikan.*',
            'naker_jurusan.nama as jurusan_name',
            'naker_pendidikan.name as pendidikan_name'
        )
            ->leftJoin('naker_jurusan', 'naker_pencari_pendidikan.jurusan_id', '=', 'naker_jurusan.id')
            ->leftJoin('naker_pendidikan', 'naker_pencari_pendidikan.pendidikan_id', '=', 'naker_pendidikan.id')
            ->where('user_id', $user->id)
            ->get();

        $keterampilan = NakerPencariKeterampilan::where('user_id', $user->id)->get();
        $pengalaman = NakerPencariPengalaman::where('user_id', $user->id)->get();

        // Periksa apakah ada data AK1 yang masih berlaku
        $nakerAk1 = NakerAk1::where('id_user', $user->id)
            ->where('berlaku_hingga', '>', Carbon::now()) // Jika berlaku_hingga lebih besar dari sekarang
            ->first();

        if (!$nakerAk1) {
            // Jika tidak ada, buat entri baru
            $uniqueCode = md5($id . Carbon::now()->toDateTimeString());
            $expiredDate = Carbon::now()->addMonths(6);

            $nakerAk1 = new NakerAk1();
            $nakerAk1->id_user = $user->id;
            $nakerAk1->tanggal_cetak = Carbon::now();
            $nakerAk1->berlaku_hingga = $expiredDate;
            $nakerAk1->status_cetak = '0'; // 0 = mandiri (mandiri)
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
            $nakerAk1->save();
        }

        // Mengembalikan tampilan untuk cetak AK1
        return view('backend.ak1.print', compact('user', 'statusKerjas', 'pendidikan', 'keterampilan', 'pengalaman', 'nakerAk1'));
    }


    public function viewAk1($unik_kode)
    {
        // Mencari data AK1 berdasarkan kode unik
        $ak1 = NakerAk1::where('unik_kode', $unik_kode)->firstOrFail();

        // Menampilkan halaman dengan detail AK1
        return view('backend.ak1.view', compact('ak1'));
    }

    public function dataAk1(Request $request)
    {
        if ($request->ajax()) {
            // Ambil data AK1 beserta relasi user
            $datas = NakerAk1::with('user:id,name')
                ->select(
                    'id',
                    'id_user',
                    'tanggal_cetak',
                    'status_cetak',
                    'berlaku_hingga',
                    'qr'
                );

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('nama_tenaga_kerja', function ($data) {
                    return $data->user->name ?? '-';
                })
                ->addColumn('tanggal_cetak', function ($data) {
                    return \Carbon\Carbon::parse($data->tanggal_cetak)->format('d-m-Y');
                })
                ->addColumn('berlaku_hingga', function ($data) {
                    return \Carbon\Carbon::parse($data->berlaku_hingga)->format('d-m-Y');
                })
                ->addColumn('status_cetak', function ($data) {
                    return $data->status_cetak == '0' ? 'Mandiri' : 'Admin';
                })
                ->addColumn('qr_code', function ($data) {
                    return '<img src="' . asset('storage/' . $data->qr) . '" width="50" alt="QR Code">';
                })
                ->rawColumns(['qr_code']) // Membiarkan kolom qr_code di-render sebagai HTML
                ->make(true);
        }

        return view('backend.ak1.data');
    }

    public function dataAk1Tk(Request $request)
    {
        if ($request->ajax()) {
            // Ambil data AK1 milik pengguna yang sedang login
            $datas = NakerAk1::with('user:id,name')
                ->where('id_user', auth()->user()->id) // Filter berdasarkan pengguna yang login
                ->select(
                    'id',
                    'id_user',
                    'tanggal_cetak',
                    'status_cetak',
                    'berlaku_hingga',
                    'qr'
                );

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('nama_tenaga_kerja', function ($data) {
                    return $data->user->name ?? '-';
                })
                ->addColumn('tanggal_cetak', function ($data) {
                    return \Carbon\Carbon::parse($data->tanggal_cetak)->format('d-m-Y');
                })
                ->addColumn('berlaku_hingga', function ($data) {
                    return \Carbon\Carbon::parse($data->berlaku_hingga)->format('d-m-Y');
                })
                ->addColumn('status_cetak', function ($data) {
                    return $data->status_cetak == '0' ? 'Mandiri' : 'Admin';
                })
                ->addColumn('qr_code', function ($data) {
                    return '<img src="' . asset('storage/' . $data->qr) . '" width="50" alt="QR Code">';
                })
                ->rawColumns(['qr_code']) // Membiarkan kolom qr_code di-render sebagai HTML
                ->make(true);
        }

        return view('backend.ak1.data_tk'); // Sesuaikan dengan nama view untuk tenaga kerja
    }
}
