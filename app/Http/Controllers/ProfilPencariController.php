<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilPencari;
use Illuminate\Support\Facades\Validator;

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
}
