<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtamFaq;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables
use Illuminate\Support\Facades\Validator;

class EtamFaqController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamFaq::select('id', 'name', 'description');

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('options', function ($data) {
                    return '
                        <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $data->id . ')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $data->id . ')">Delete</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.setting.faq.index');
    }

        // Method untuk menyimpan data user baru
        public function store(Request $request)
        {
            $userId = auth()->user()->id; 
            // Validasi input
            $validator = Validator::make($request->all(), [
                'pertanyaan' => 'required|string',
                'jawaban' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()]);
            }
            
    
            // Menyimpan data ke tabel users
            $user = EtamFaq::create([
                'name' => $request->pertanyaan,
                'description' => $request->jawaban,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
    
            return response()->json(['success' => true]);
        }

        public function getData($id)
        {
            try {
                $data = EtamFaq::select('id', 'name', 'description')->findOrFail($id);

                return response()->json(['success' => true, 'data' => $data]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }


        public function update(Request $request, $id)
        {
            try {
                // Validasi input
                $validatedData = $request->validate([
                    'name' => 'required|string',
                    'description' => 'required|string', 
                ]);

                // Cari admin berdasarkan ID
                $data = EtamFaq::findOrFail($id);

                $data->update([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                return response()->json(['success' => true, 'message' => 'update data berhasil']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        public function softdelete($id)
        {
            try {
                // Cari admin berdasarkan ID
                $data = EtamFaq::findOrFail($id);
    
                // Set is_deleted = 1 untuk soft delete admin
                $data->is_deleted = 1;
                $data->save();  // Simpan perubahan
                $data->delete();
                return response()->json(['success' => true, 'message' => 'hapus data  berhasil.']);

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error A: ' . $e->getMessage()]);
            }
        }


}
