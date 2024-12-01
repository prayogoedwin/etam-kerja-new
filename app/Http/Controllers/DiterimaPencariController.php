<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lamaran;
use Yajra\DataTables\Facades\DataTables;

class DiterimaPencariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $lamars = Lamaran::select(
                'etam_lamaran.id',
                'etam_lowongan.judul_lowongan',
                'users_pencari.name',
                'etam_lamaran.created_at as waktu_lamar'
            )
            ->join('etam_lowongan', 'etam_lamaran.lowongan_id', '=', 'etam_lowongan.id') // Join tabel
            ->join('users_pencari', 'etam_lamaran.pencari_id', '=', 'users_pencari.user_id') // Join tabel
            ->whereNull('etam_lowongan.deleted_at') // Memastikan data tidak terhapus
            ->where('etam_lowongan.posted_by', auth()->user()->id)
            ->where('etam_lamaran.progres_id', '3') // Filter status lamaran
            ->get();

            return DataTables::of($lamars)
                ->addIndexColumn()
                // ->editColumn('waktu_lamar', function ($lamar) {
                //     return $lamar->waktu_lamar->format('Y M d H:i:s');
                // })
                // ->addColumn('options', function ($lamar) {
                //     return '
                //         <a href="' . route('lowongan.pelamar', encode_url($loker->id)) . '" class="btn btn-info btn-sm">Lihat Pelamar</a>
                //         <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $loker->id . ')">Delete</button>
                //     ';
                // })
                ->addColumn('status', function ($lamar) {
                    return '
                        <span class="badge rounded-pill bg-success">Diterima</span>
                    ';
                })
                ->rawColumns(['status'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }
        return view('backend.pencariditerima.index');
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
