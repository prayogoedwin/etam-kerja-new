<?php

namespace App\Http\Controllers;

use App\Models\ProfilPenyedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfilPenyediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userid = auth()->user()->id;
        $data['sektors'] = getSektor();
        $data['provinsis'] = getProvinsi();
        $data['profil'] = ProfilPenyedia::where('user_id', $userid)->first();

        return view('backend.profilpenyedia.index', $data);
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

        // dd($request->all());

        // Validasi data
        $validatedData = $request->validate([
            'name' => 'required',
            'deskripsi' => 'required',
            'jenis_perusahaan' => 'required',
            // 'nomor_sip3mi' => 'required',
            'nib' => 'required',
            'id_sektor' => 'required',
            // 'id_provinsi' => 'required',
            // 'id_kota' => 'required',
            // 'id_kecamatan' => 'required',
            // 'id_desa' => 'required',
            'alamat' => 'required',
            'kodepos' => 'required',
            'telpon' => 'required',
            'jabatan' => 'required',
            'website' => 'required',
            // 'status_id' => 'required',
            'foto' => 'required',
            // 'shared_by_id' => 'required',
            'posted_by' => 'required',
        ]);

        // dd($validatedData);

        // Upload file
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('logo_perusahaan', 'public'); // Menyimpan file di folder storage/app/public/logo_perusahaan
            $validatedData['foto'] = $filePath; // Tambahkan path file ke data yang akan di-update
        }

        // Cari data berdasarkan ID
        $post = ProfilPenyedia::findOrFail($id);
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
