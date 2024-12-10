<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilPencari;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\EtamPencariPendidikan;
use App\Models\EtamPencariKeahlian;

class ProfilPencariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userid = auth()->user()->id;
        $data['agamas'] = getAgama();
        $data['pendidikans'] = getPendidikan();
        $data['provinsis'] = getProvinsi();
        $data['disabilitases'] = getJenisDisabilitas();
        // $data['profil'] = ProfilPencari::where('user_id', $userid)->first();
        $data['profil'] = ProfilPencari::with('user')->where('user_id', $userid)->first();

        return view('backend.profilpencari.index', $data);
        // echo json_encode($data);
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
        // Validasi input
        // $validator = Validator::make($request->all(), [
        //     'ktp' => 'required|string',
        //     'name' => 'required|string',
        //     'tempat_lahir' => 'required|string',
        //     'tanggal_lahir' => 'required|date',
        //     'id_agama' => 'required|numeric',
        //     'alamat' => 'required|string',
        //     'kodepos' => 'required|string',
        //     'id_pendidikan' => 'required|numeric',
        //     'id_jurusan' => 'required|numeric',
        //     'medsos' => 'nullable|string',
        //     'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'whatsapp' => 'nullable|string|max:15', // Validasi whatsapp
        // ]);

        $validatedData = $request->validate([
            'is_diterima' => 'required|numeric',
            'ktp' => 'required|string',
            'name' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'id_agama' => 'required|numeric',
            'alamat' => 'required|string',
            'kodepos' => 'required|string',
            'id_pendidikan' => 'required|numeric',
            'id_jurusan' => 'required|numeric',
            'medsos' => 'nullable|string',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'whatsapp' => 'nullable|string|max:15', // Validasi whatsapp
        ]);

         // Upload file
         if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('foto_pencarikerja', 'public'); // Menyimpan file di folder storage/app/public/foto_pencarikerja
            $validatedData['foto'] = $filePath; // Tambahkan path file ke data yang akan di-update
        }

        // Update data di tabel ProfilPencari
        $profil = ProfilPencari::find($id);
        if (!$profil) {
            return response()->json(['status' => 0, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Update data ProfilPencari
        // $profil->update($request->except('whatsapp')); // Jangan langsung update whatsapp di tabel ini
        $profil->update($validatedData); // Update data yang sudah divalidasi

        // Update whatsapp di tabel users
        $user = $profil->user; // Relasi ke tabel users
        if ($user && $request->filled('whatsapp')) {
            $user->whatsapp = $request->input('whatsapp');
            $user->save();
        }

        // // Update data
        // $profil = ProfilPencari::find($id);
        // $profil->update($validator);

        return response()->json(['status' => 1, 'message' => 'Data berhasil disimpan'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function pendidikanformal(Request $request)
    {
        $userid = auth()->user()->id;

        if ($request->ajax()) {
            $pendidikansf = EtamPencariPendidikan::select(
                'etam_pencari_pendidikan.id',
                'etam_pendidikan.name as pendidikanteks',
                'etam_jurusan.nama as jurusanteks',
                'etam_pencari_pendidikan.instansi',
                'etam_pencari_pendidikan.tahun'
            )
            ->join('etam_pendidikan', 'etam_pencari_pendidikan.pendidikan_id', '=', 'etam_pendidikan.id')
            ->join('etam_jurusan', 'etam_pencari_pendidikan.jurusan_id', '=', 'etam_jurusan.id')
            ->where('etam_pencari_pendidikan.user_id', $userid)
            ->get();

            return DataTables::of($pendidikansf)
                ->addIndexColumn()
                ->addColumn('options', function ($pend) {
                    return '
                        <button class="btn btn-danger btn-sm" onclick="confirmDeletePendidikan(' . $pend->id . ')"><i class="fa fa-trash"></i></button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }
    }

    public function store_pendidikanformal(Request $request)
    {
        // dd($request->all());
        $userid = auth()->user()->id;

        $validatedData = $request->validate([
            'pendidikan_id' => 'required|numeric',
            'jurusan_id' => 'required|numeric',
            'instansi' => 'required|string',
            'tahun' => 'required|numeric',
        ]);

        $pendidikan = new EtamPencariPendidikan();
        $pendidikan->user_id = $userid;
        $pendidikan->pendidikan_id = $request->pendidikan_id;
        $pendidikan->jurusan_id = $request->jurusan_id;
        $pendidikan->instansi = $request->instansi;
        $pendidikan->tahun = $request->tahun;
        $pendidikan->save();

        return response()->json(['status' => 1, 'message' => 'Data berhasil disimpan'], 200);
    }

    public function delete_pendidikanformal(Request $request)
    {
        $pendidikan = EtamPencariPendidikan::find($request->id);
        if (!$pendidikan) {
            return response()->json(['status' => 0, 'message' => 'Data tidak ditemukan'], 404);
        }

        $pendidikan->delete();

        return response()->json(['status' => 1, 'message' => 'Data berhasil dihapus'], 200);
    }

    public function keahlian(Request $request){
        $userid = auth()->user()->id;

        if ($request->ajax()) {
            $keahlians = EtamPencariKeahlian::select(
                'etam_pencari_keahlian.id',
                'etam_pencari_keahlian.keahlian'
            )
            ->where('etam_pencari_keahlian.user_id', $userid)
            ->get();

            return DataTables::of($keahlians)
                ->addIndexColumn()
                ->addColumn('options', function ($keahlian) {
                    return '
                        <button class="btn btn-danger btn-sm" onclick="confirmDeleteKeahlian(' . $keahlian->id . ')"><i class="fa fa-trash"></i></button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }
    }

    public function store_keahlian(Request $request)
    {
        $userid = auth()->user()->id;

        $validatedData = $request->validate([
            'keahlian' => 'required|string',
        ]);

        $keahlian = new EtamPencariKeahlian();
        $keahlian->user_id = $userid;
        $keahlian->keahlian = $request->keahlian;
        $keahlian->save();

        return response()->json(['status' => 1, 'message' => 'Data berhasil disimpan'], 200);
    }

    public function delete_keahlian(Request $request)
    {
        $keahlian = EtamPencariKeahlian::find($request->id);
        if (!$keahlian) {
            return response()->json(['status' => 0, 'message' => 'Data tidak ditemukan'], 404);
        }

        $keahlian->delete();

        return response()->json(['status' => 1, 'message' => 'Data berhasil dihapus'], 200);
    }
}
