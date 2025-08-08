<?php

namespace App\Http\Controllers;

use App\Models\EtamInfografis;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;



class EtamInfografisController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamInfografis::select('id', 'name', 'path_file', 'status');

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    return $data->status ? '1' : '0';
                })
                ->addColumn('options', function ($data) {
                    return '
                        <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $data->id . ')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $data->id . ')">Delete</button>
                    ';
                })
                ->rawColumns(['options']) // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.setting.infografis.index');
    }

    public function store(Request $request)
    {
        $userId = auth()->user()->id;

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'path_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // Upload file
        if ($request->hasFile('path_file')) {
            $file = $request->file('path_file');
            $filePath = $file->store('infografis', 'public'); // Menyimpan file di folder storage/app/public/infografis
        }

        // Simpan data ke tabel
        EtamInfografis::create([
            'name' => $request->name,
            'path_file' => $filePath,
            'status' => $request->status,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $data = EtamInfografis::find($id);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data not found']);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = EtamInfografis::find($id);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data not found']);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'path_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // Update file jika ada file baru
        if ($request->hasFile('path_file')) {
            // Hapus file lama
            if (Storage::exists('public/' . $data->path_file)) {
                Storage::delete('public/' . $data->path_file);
            }

            // Simpan file baru
            $file = $request->file('path_file');
            $filePath = $file->store('infografis', 'public');
            $data->path_file = $filePath;
        }

        // Update data lainnya
        $data->name = $request->name;
        $data->status = $request->status;
        $data->updated_by = auth()->user()->id;
        $data->save();

        return response()->json(['success' => true, 'message' => 'Data updated successfully']);
    }


    public function destroy($id)
    {
        // Cari data berdasarkan ID
        $data = EtamInfografis::findOrFail($id);

        // Hapus file terkait jika ada
        if ($data->path_file) {
            // Gunakan disk 'public' untuk menghapus file
            Storage::disk('public')->delete($data->path_file);
        }

        // Hapus data dari database
        $data->delete();

        // Return response
        return response()->json(['success' => true, 'message' => 'Data dan file berhasil dihapus']);
    }
}
