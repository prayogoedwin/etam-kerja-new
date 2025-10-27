<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPencari;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables
use App\Models\ProfilPencari;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = UserPencari::select(
                '*' // Menambahkan kolom 'name' dari tabel etam_progres
            )
            // ->join('etam_progres', 'etam_lowongan.status_id', '=', 'etam_progres.kode') // Join tabel
            ->whereNull('deleted_at') // Memastikan data tidak terhapus
            ->where('is_alumni_bkk', 1) // Kondisi where
            ->where('bkk_id', auth()->user()->id) // Kondisi where
            ->get();

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('options', function ($datas) {
                    // <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $data->id . ')">Edit</button>
                    return '
                        <a href="' . route('alumni.detail', encode_url($datas->user_id)) . '" class="btn btn-info btn-sm">Lihat</a>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.alumni.index');
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
        $userid = decode_url($id);
        $data['agamas'] = getAgama();
        $data['pendidikans'] = getPendidikan();
        $data['provinsis'] = getProvinsi();
        $data['disabilitases'] = getJenisDisabilitas();
        $data['profil'] = ProfilPencari::with('user')->where('user_id', $userid)->first();

        // echo json_encode($data);
        return view('backend.alumni.detail', $data);
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
}
