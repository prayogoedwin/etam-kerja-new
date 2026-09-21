<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlk;
use App\Models\EtamStruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BlkController extends Controller
{
    use ManagesBlkAccess;

    public function index(Request $request)
    {
        if (! $this->isBlkMasterAdmin()) {
            abort(403);
        }

        if ($request->ajax()) {
            $datas = EtamBlk::query()
                ->with(['kabkota:id,name'])
                ->select('id', 'tipe_lembaga', 'nama_lembaga', 'email', 'whatsapp', 'telepon', 'website', 'kabkota_id', 'kode_struktur', 'alamat_lengkap');

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('tipe_lembaga_nama', function (EtamBlk $data) {
                    return EtamBlk::tipeLembagaLabels()[(int) $data->tipe_lembaga] ?? '-';
                })
                ->addColumn('kabkota_nama', function (EtamBlk $data) {
                    return $data->kabkota->name ?? '-';
                })
                ->addColumn('struktur_nama', function (EtamBlk $data) {
                    return $this->strukturLabel($data->kode_struktur);
                })
                ->addColumn('options', function (EtamBlk $data) {
                    return '
                        <button class="btn btn-primary btn-sm" onclick="showEditModal('.$data->id.')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('.$data->id.')">Delete</button>
                    ';
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        $kabkota = getKabkota();
        $strukturs = $this->strukturOptions();

        return view('backend.blk.lembaga.index', compact('kabkota', 'strukturs'));
    }

    public function store(Request $request)
    {
        if (! $this->isBlkMasterAdmin()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'nama_lembaga' => 'required|string|max:255',
            'tipe_lembaga' => 'required|in:1,2',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'kabkota_id' => 'nullable|integer',
            'kode_struktur' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        EtamBlk::create([
            'tipe_lembaga' => $request->tipe_lembaga,
            'nama_lembaga' => $request->nama_lembaga,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'telepon' => $request->telepon,
            'website' => $request->website,
            'instagram' => $request->instagram,
            'provinsi_id' => 64,
            'kabkota_id' => $request->kabkota_id ?: null,
            'kode_struktur' => $request->kode_struktur ?: null,
            'alamat_lengkap' => $request->alamat_lengkap,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'BLK berhasil ditambahkan']);
    }

    public function show(string $id)
    {
        if (! $this->isBlkMasterAdmin()) {
            abort(403);
        }

        $data = EtamBlk::findOrFail($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, string $id)
    {
        if (! $this->isBlkMasterAdmin()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'nama_lembaga' => 'required|string|max:255',
            'tipe_lembaga' => 'required|in:1,2',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'kabkota_id' => 'nullable|integer',
            'kode_struktur' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $data = EtamBlk::findOrFail($id);
        $data->update([
            'tipe_lembaga' => $request->tipe_lembaga,
            'nama_lembaga' => $request->nama_lembaga,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'telepon' => $request->telepon,
            'website' => $request->website,
            'instagram' => $request->instagram,
            'kabkota_id' => $request->kabkota_id ?: null,
            'kode_struktur' => $request->kode_struktur ?: null,
            'alamat_lengkap' => $request->alamat_lengkap,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'BLK berhasil diupdate']);
    }

    public function destroy(string $id)
    {
        if (! $this->isBlkMasterAdmin()) {
            abort(403);
        }

        $data = EtamBlk::findOrFail($id);
        $data->deleted_by = Auth::id();
        $data->save();
        $data->delete();

        return response()->json(['success' => true, 'message' => 'Hapus data berhasil']);
    }

    /**
     * @return \Illuminate\Support\Collection<int, EtamStruktur>
     */
    private function strukturOptions()
    {
        return EtamStruktur::query()
            ->orderBy('kode_bidang')
            ->get(['kode_bidang', 'nama']);
    }

    private function strukturLabel(?string $kodeStruktur): string
    {
        if (! $kodeStruktur) {
            return '-';
        }

        $struktur = EtamStruktur::query()
            ->where('kode_bidang', $kodeStruktur)
            ->first();

        if (! $struktur) {
            return $kodeStruktur;
        }

        return $struktur->kode_bidang.' - '.$struktur->nama;
    }
}
