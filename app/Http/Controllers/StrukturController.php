<?php

namespace App\Http\Controllers;

use App\Models\EtamStruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class StrukturController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamStruktur::query()
                ->select('id', 'tipe', 'kode_lokasi', 'kode_bidang', 'nama', 'alamat', 'slug', 'created_at')
                ->orderBy('kode_lokasi')
                ->orderBy('kode_bidang');

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('tipe_label', function ($row) {
                    return $row->tipe_label;
                })
                ->addColumn('alamat_short', function ($row) {
                    $alamat = trim((string) $row->alamat);
                    if ($alamat === '') {
                        return '<span class="text-muted">Belum diisi</span>';
                    }

                    return e(Str::limit(preg_replace('/\s+/', ' ', $alamat), 80));
                })
                ->addColumn('options', function ($row) {
                    return '<button class="btn btn-primary btn-sm" onclick="showEditStruktur('.$row->id.')">Edit Alamat</button>';
                })
                ->rawColumns(['alamat_short', 'options'])
                ->make(true);
        }

        return view('backend.setting.struktur.index');
    }

    public function show(string $id)
    {
        $data = EtamStruktur::query()
            ->select('id', 'tipe', 'kode_lokasi', 'kode_bidang', 'nama', 'alamat')
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $struktur = EtamStruktur::findOrFail($id);
        $struktur->alamat = $request->alamat ? trim($request->alamat) : null;
        $struktur->save();

        return response()->json(['success' => true, 'message' => 'Alamat struktur berhasil disimpan']);
    }
}
