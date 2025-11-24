<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganAdmin;
use App\Models\UserAdmin;
use App\Models\Progress;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class LowonganAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $id = Auth::user()->id; // Mendapatkan ID pengguna yang sedang login
            $userAdmin = UserAdmin::where('user_id', $id)->first(); // Mencari data UserAdmin berdasarkan user_id

            // $lokers = LowonganAdmin::select('id', 'judul_lowongan', 'tanggal_start', 'tanggal_end', 'deskripsi');

            $lokers = LowonganAdmin::with([
                'user:id,name,email,whatsapp',
                'user.penyedia:id,name,id_kota',
                'progress', // Hanya panggil relasi tanpa filter di sini
                'penyedia:id,user_id,name,id_kota' // Tambahkan relasi penyedia
            ])
            ->where('tipe_lowongan', 0) // lowongan umum, bukan bkk, bukan job fair
            ->whereHas('progress', function ($query) {
                // Memastikan 'progress' dengan 'modul' = 'lowongan' dan 'status_id' yang valid
                $query->where('modul', 'lowongan')
                      ->whereIn('kode', function($query) {
                          // Menambahkan check untuk nilai status_id yang valid
                          $query->select('status_id')->from('etam_lowongan');
                      });
            });

            // Filter untuk admin-kabkota dan admin-kabkota-officer
            $roles = Auth::user()->roles;
            if (!empty($roles) && in_array($roles[0]['name'], ['admin-kabkota', 'admin-kabkota-officer'])) {
                $lokers->whereHas('user.penyedia', function ($query) use ($userAdmin) {
                    $query->where('id_kota', $userAdmin->kabkota_id);
                });
            }

            return DataTables::of($lokers)
                ->addIndexColumn()
                ->addColumn('options', function ($loker) {

                    return '
                        <a href="' . route('lowongan.pelamar', encode_url($loker->id)) . '" class="btn btn-info btn-sm">Lihat Pelamar</a>
                        <button class="btn btn-warning btn-sm" onclick="showEditModal(' . $loker->id . ')">Lihat</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        $data['jabatans'] = getJabatan();
        $data['sektors'] = getSektor();
        $data['kabkotas'] = getKabkota();
        $data['pendidikans'] = getPendidikan();
        $data['maritals'] = getMarital();

        return view('backend.lowonganadmin.index', $data);
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
        try {
            $data = LowonganAdmin::all()->findOrFail($id);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
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
        try {
            // Validasi input
            // $validatedData = $request->validate([
            //     'name' => 'required|string',
            //     'description' => 'required|string',
            // ]);

            $userId = auth()->user()->id;
            $acc_by_role = Auth::user()->roles[0]['name'];

            // Cari admin berdasarkan ID
            $data = LowonganAdmin::findOrFail($id);

            $data->update([
                'status_id' => $request->status_id,
                'acc_by' => $userId,
                'acc_by_role' => $acc_by_role,
                'acc_at' => date('Y-m-d H:i:s')
            ]);

            return response()->json(['success' => true, 'message' => 'update data berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
