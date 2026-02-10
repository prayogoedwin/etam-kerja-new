<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBkk;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserAdmin;
use App\Models\UserPencari;

class BkkAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       // Ambil id kota admin yang login
        $userId = Auth::user()->id;

        $userAdmin = UserAdmin::where('user_id', $userId)->first();

        // Safety check (hindari error jika data admin tidak ada)
        if (!$userAdmin) {
            abort(403, 'Data admin tidak ditemukan.');
        }

        $userIdKota = $userAdmin->kabkota_id;

        if ($request->ajax()) {

            $userRole = Auth::user()->roles->first()->name ?? null;
            $query = UserBkk::whereNull('deleted_at');
            if (in_array($userRole, ['admin-kabkota', 'admin-kabkota-officer']) && $userAdmin) {
                $query->where('id_kota', $userAdmin->kabkota_id);
            }
            $bkks = $query->get();

            return DataTables::of($bkks)
                ->addIndexColumn()
                ->addColumn('options', function ($bkk) {
                    return '
                        <a href="' . route('bkk.adminalumni.index', encode_url($bkk->user_id)) . '"
                        class="btn btn-info btn-sm"
                        title="Lihat Alumni">
                            <i class="fa fa-users"></i>
                        </a>
                    ';
                })
                ->rawColumns(['options'])
                ->make(true);

        }
        return view('backend.bkkadmin.index');
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

    public function data_alumni(Request $request, string $id){
        // echo 'halaman data alumni bkk admin dengan bkk id: ' . $id;
        $bkk_id = decode_url($id);
        // echo '<br>real id: ' . $realId;

        if ($request->ajax()) {
            $datas = UserPencari::select(
                '*' // Menambahkan kolom 'name' dari tabel etam_progres
            )
            // ->join('etam_progres', 'etam_lowongan.status_id', '=', 'etam_progres.kode') // Join tabel
            ->whereNull('deleted_at') // Memastikan data tidak terhapus
            ->where('is_alumni_bkk', 1) // Kondisi where
            ->where('bkk_id', $bkk_id) // Kondisi where
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

        return view('backend.bkkadmin.alumniindex');
    }

    public function data_unfinish(Request $request){
        if ($request->ajax()) {
            // Ambil user admin jika diperlukan
            $userId = Auth::id();
            $userAdmin = UserAdmin::where('user_id', $userId)->first();

            $bkks = User::select(
                'users.id',
                'users.email',
                'users.whatsapp',
                'users.created_at',
                'users_bkk.name' // Tambahkan kolom ktp untuk digunakan di options
            )
            ->join('users_bkk', 'users.id', '=', 'users_bkk.user_id')
            ->whereColumn('users.id', 'users_bkk.name') // Perbaikan: whereColumn bukan where
            ->whereNull('users.deleted_at'); // Tambahkan kondisi soft delete jika ada

            // Filter pencarian server-side untuk DataTables
            if (!empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $bkks->where(function($query) use ($searchValue) {
                    $query->where('users.email', 'like', "%{$searchValue}%")
                          ->orWhere('users.whatsapp', 'like', "%{$searchValue}%")
                          ->orWhere('users_bkk.name', 'like', "%{$searchValue}%");
                });
            }

            // Untuk debugging query (hapus setelah fix)
            // \Log::info($bkks->toSql());
            // \Log::info($bkks->getBindings());
            // echo json_encode($bkks->toSql());
            // die();

            return DataTables::of($bkks)
                ->addIndexColumn()
                ->editColumn('created_at', function ($penyedia) {
                    // Pastikan created_at adalah Carbon instance
                    return $penyedia->created_at ? $penyedia->created_at->format('d M Y H:i:s') : '-';
                })
                ->addColumn('options', function ($penyedia) {

                    return '<input type="checkbox" class="pelamar-checkbox" value="' . $penyedia->id . '">';
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        // echo json_encode($bkks);

        return view('backend.bkk.unfinish');
    }

    public function bulk_deletebkkunfinish(Request $request){
        $ids = $request->input('ids');

        if (!$ids) {
            return response()->json(['message' => 'Data tidak valid'], 400);
        }

        // Cari admin berdasarkan ID
        UserBkk::whereIn('user_id', $ids)->update([
            'deleted_at' => date('Y-m-h H:i:s')
        ]);

        $user = User::whereIn('id', $ids)->update([
            'is_deleted' => 1,
            'deleted_at' => date('Y-m-h H:i:s')
        ]);

        return response()->json(['message' => 'Berhasil hapus data']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
