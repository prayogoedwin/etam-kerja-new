<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lamaran;
use Yajra\DataTables\Facades\DataTables;

class PenempatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $penempatans = Lamaran::with([
                'user:id,name,email,whatsapp',
                'profilPencari:id,user_id,alamat,ktp,name,gender',
                'lowongan:id,judul_lowongan,deskripsi',
                'penyedia'
            ])
            ->where('progres_id', 3)
            ->get();

            return DataTables::of($penempatans)
                ->editColumn('status', function ($pelamar) {
                    return '<span class="badge rounded-pill bg-success">Diterima</span>';
                })
                ->addIndexColumn()
                // ->addColumn('options', function ($penem) {
                //     // <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $data->id . ')">Edit</button>
                //     // return '
                //     //     <a href="' . route('lowongan.pelamar', encode_url($loker->id)) . '" class="btn btn-info btn-sm">Lihat Pelamar</a>
                //     //     <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $loker->id . ')">Delete</button>
                //     // ';
                //     return $penem->id . ' ID LAMARAN';
                // })
                ->rawColumns(['status'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.penempatan.index');

        // $penempatans = Lamaran::with([
        //     'user:id,name,email,whatsapp',
        //     'profilPencari:id,user_id,alamat,ktp,name,gender',
        //     'lowongan:id,judul_lowongan,deskripsi',
        //     'penyedia'
        // ])
        // ->where('progres_id', 3)
        // ->get();
        // $data['penempatans'] = $penempatans;

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
