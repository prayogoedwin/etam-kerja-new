<?php

namespace App\Http\Controllers\Pengawasan\K3;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengawasan\Pjk3\ManagesPjk3Access;
use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use App\Models\Pengawasan\Pjk3\EtamPjk3SuketPerangkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MasterK3Controller extends Controller
{
    use ManagesPjk3Access;

    public function kategoriIndex(Request $request)
    {
        abort_unless($this->canKelolaMasterK3(), 403);

        if ($request->ajax()) {
            return DataTables::of(EtamPengawasanK3Kategori::query()->withCount('jenis')->latest('id'))
                ->addIndexColumn()
                ->addColumn('jumlah_jenis', fn (EtamPengawasanK3Kategori $row) => $row->jenis_count)
                ->addColumn('options', function (EtamPengawasanK3Kategori $row) {
                    return '<button type="button" class="btn btn-primary btn-sm js-edit-kategori" data-id="'.$row->id.'">Edit</button> '
                        .'<button type="button" class="btn btn-danger btn-sm js-hapus-kategori" data-id="'.$row->id.'">Hapus</button>';
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        return view('backend.pengawasan.k3.master.kategori');
    }

    public function kategoriShow(string $id)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $row = EtamPengawasanK3Kategori::query()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $row->id,
                'nama' => $row->nama,
                'kode' => $row->kode,
                'keterangan' => $row->keterangan,
            ],
        ]);
    }

    public function kategoriStore(Request $request)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $validator = $this->validatorKategori($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        EtamPengawasanK3Kategori::create($this->dataKategori($request));

        return response()->json(['success' => true, 'message' => 'Kategori K3 disimpan.']);
    }

    public function kategoriUpdate(Request $request, string $id)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $row = EtamPengawasanK3Kategori::query()->findOrFail($id);
        $validator = $this->validatorKategori($request, (int) $row->id);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $row->update($this->dataKategori($request));

        return response()->json(['success' => true, 'message' => 'Kategori K3 diperbarui.']);
    }

    public function kategoriDestroy(string $id)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $row = EtamPengawasanK3Kategori::query()->findOrFail($id);
        if ($row->jenis()->exists()
            || $row->ajuan()->exists()
            || EtamPjk3Suket::query()->where('kategori_id', $row->id)->exists()
            || EtamPjk3Suket::query()->where('ahli_k3_kategori_id', $row->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak bisa dihapus karena masih dipakai jenis atau pengajuan.',
            ], 422);
        }

        $row->delete();

        return response()->json(['success' => true, 'message' => 'Kategori K3 dihapus.']);
    }

    public function jenisIndex(Request $request)
    {
        abort_unless($this->canKelolaMasterK3(), 403);

        if ($request->ajax()) {
            $query = EtamPengawasanK3Jenis::query()->with('kategori:id,nama,kode')->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kategori_nama', fn (EtamPengawasanK3Jenis $row) => $row->kategori->nama ?? '-')
                ->addColumn('options', function (EtamPengawasanK3Jenis $row) {
                    return '<button type="button" class="btn btn-primary btn-sm js-edit-jenis" data-id="'.$row->id.'">Edit</button> '
                        .'<button type="button" class="btn btn-danger btn-sm js-hapus-jenis" data-id="'.$row->id.'">Hapus</button>';
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        return view('backend.pengawasan.k3.master.jenis', [
            'kategoriList' => EtamPengawasanK3Kategori::query()->orderBy('nama')->get(['id', 'nama', 'kode']),
        ]);
    }

    public function jenisShow(string $id)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $row = EtamPengawasanK3Jenis::query()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $row->id,
                'kategori_id' => $row->kategori_id,
                'nama' => $row->nama,
                'keterangan' => $row->keterangan,
            ],
        ]);
    }

    public function jenisStore(Request $request)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $validator = $this->validatorJenis($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        EtamPengawasanK3Jenis::create($this->dataJenis($request));

        return response()->json(['success' => true, 'message' => 'Jenis objek K3 disimpan.']);
    }

    public function jenisUpdate(Request $request, string $id)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $row = EtamPengawasanK3Jenis::query()->findOrFail($id);
        $validator = $this->validatorJenis($request, (int) $row->id);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $row->update($this->dataJenis($request));

        return response()->json(['success' => true, 'message' => 'Jenis objek K3 diperbarui.']);
    }

    public function jenisDestroy(string $id)
    {
        abort_unless($this->canKelolaMasterK3(), 403);
        $row = EtamPengawasanK3Jenis::query()->findOrFail($id);
        if ($row->ajuan()->exists()
            || EtamPjk3Suket::query()->where('jenis_id', $row->id)->exists()
            || EtamPjk3SuketPerangkat::query()->where('jenis_id', $row->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak bisa dihapus karena masih dipakai pengajuan atau LHPP.',
            ], 422);
        }

        $row->delete();

        return response()->json(['success' => true, 'message' => 'Jenis objek K3 dihapus.']);
    }

    private function validatorKategori(Request $request, ?int $ignoreId = null)
    {
        $kode = strtoupper(trim((string) $request->input('kode')));
        $request->merge(['kode' => $kode !== '' ? $kode : null]);

        return Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'kode' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('etam_pengawasan_k3_kategori', 'kode')->ignore($ignoreId)->whereNull('deleted_at'),
            ],
            'keterangan' => 'nullable|string|max:1000',
        ]);
    }

    /**
     * @return array{nama: string, kode: string|null, keterangan: string|null}
     */
    private function dataKategori(Request $request): array
    {
        $kode = strtoupper(trim((string) $request->input('kode')));

        return [
            'nama' => trim((string) $request->input('nama')),
            'kode' => $kode !== '' ? $kode : null,
            'keterangan' => trim((string) $request->input('keterangan')) ?: null,
        ];
    }

    private function validatorJenis(Request $request, ?int $ignoreId = null)
    {
        return Validator::make($request->all(), [
            'kategori_id' => 'required|exists:etam_pengawasan_k3_kategori,id',
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('etam_pengawasan_k3_jenis', 'nama')
                    ->where('kategori_id', $request->input('kategori_id'))
                    ->ignore($ignoreId)
                    ->whereNull('deleted_at'),
            ],
            'keterangan' => 'nullable|string|max:1000',
        ]);
    }

    /**
     * @return array{kategori_id: int, nama: string, keterangan: string|null}
     */
    private function dataJenis(Request $request): array
    {
        return [
            'kategori_id' => (int) $request->input('kategori_id'),
            'nama' => trim((string) $request->input('nama')),
            'keterangan' => trim((string) $request->input('keterangan')) ?: null,
        ];
    }
}
