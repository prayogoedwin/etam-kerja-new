<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBkk;

class ProfilBkkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userid = auth()->user()->id;
        $data['kategoris'] = getKategoriBkk();
        $data['provinsis'] = getProvinsiKaltim();
        $data['profil'] = UserBkk::where('user_id', $userid)->first();

        return view('backend.profilbkk.index', $data);
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
        $validatedData = $request->validate([
            'no_sekolah' => 'required',
            'name' => 'required',
            'alamat' => 'required',
            'kodepos' => 'required',
            'telpon' => 'required',
            'jabatan' => 'required',
            'website' => 'required',
            'foto' => 'required',
        ]);

        // dd($validatedData);

        // Upload file
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('logo_perusahaan', 'public'); // Menyimpan file di folder storage/app/public/logo_perusahaan
            $validatedData['foto'] = $filePath; // Tambahkan path file ke data yang akan di-update
        }

        // Cari data berdasarkan ID
        $post = UserBkk::findOrFail($id);
        // Update data
        $post->update($validatedData);

        // Response sukses
        return response()->json([
            'status' => 1,
            'message' => 'Berhasil update data',
            // 'data' => $post
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
