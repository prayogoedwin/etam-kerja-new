<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lamaran;
use Yajra\DataTables\Facades\DataTables;

class HistoryLamaranPencariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $historys = Lamaran::select(
                'etam_lowongan.judul_lowongan',
                'etam_lamaran.progres_id',
                'etam_lamaran.keterangan',
                'etam_lamaran.created_at',
                'etam_progres.name as statuslamaran',
                'etam_lowongan.tipe_lowongan'
            )
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id') // Join tabel
            ->join('etam_progres', 'etam_lamaran.progres_id', '=', 'etam_progres.kode') // Join tabel
            ->whereNull('etam_lamaran.deleted_at') // Memastikan data tidak terhapus
            ->where('etam_lamaran.pencari_id', auth()->user()->id) // Hanya data yang sesuai dengan pencari yang sedang login
            ->where('etam_progres.modul', 'lamaran') // Hanya data yang memiliki modul lamaran
            ->get();

            return DataTables::of($historys)
                ->addIndexColumn()
                ->editColumn('created_at', function ($history) {
                    return $history->created_at->format('Y M d H:i:s');
                })
                ->addColumn('tipe_lowongan', function ($history) {
                    $tipe_lowongan = '-';
                    if($history->tipe_lowongan == '0'){
                        $tipe_lowongan = '<span class="badge rounded-pill bg-info">Umum</span>';
                    } else if($history->tipe_lowongan == '1'){
                        $tipe_lowongan = '<span class="badge rounded-pill bg-info">Job Fair</span>';;
                    } else if($history->tipe_lowongan == '2'){
                        $tipe_lowongan = '<span class="badge rounded-pill bg-info">BKK</span>';;
                    }
                    return $tipe_lowongan;
                })
                ->addColumn('progress', function ($history) {
                    $sts_lamar = '-';
                    if($history->progres_id == '1' || $history->progres_id == '2' || $history->progres_id == '4'){
                        $sts_lamar = '<span class="badge rounded-pill bg-warning">'.$history->statuslamaran.'</span>';
                    } else if($history->progres_id == '5'){
                        $sts_lamar = '<span class="badge rounded-pill bg-danger">'.$history->statuslamaran.'</span>';
                    } else if($history->progres_id == '3'){
                        $sts_lamar = '<span class="badge rounded-pill bg-success">'.$history->statuslamaran.'</span>';
                    }
                    return $sts_lamar;
                })
                ->rawColumns(['tipe_lowongan','progress'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.historylamaranpencari.index');
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
}
