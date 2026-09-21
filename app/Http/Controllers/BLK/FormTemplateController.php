<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkForm;
use App\Models\BLK\EtamBlkFormPertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FormTemplateController extends Controller
{
    use ManagesBlkAccess;

    public function index(Request $request, string $jenis)
    {
        $this->assertJenis($jenis);
        $this->assertCanManage();

        if ($request->ajax()) {
            $datas = EtamBlkForm::query()
                ->with(['blk:id,nama_lembaga'])
                ->withCount('pertanyaan')
                ->where('jenis', $jenis)
                ->select('id', 'blk_id', 'jenis', 'nama', 'deskripsi', 'created_at');

            $blkIds = $this->accessibleBlkIds();
            if ($blkIds !== null) {
                $datas->whereIn('blk_id', $blkIds);
            }

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('blk_nama', function (EtamBlkForm $data) {
                    return $data->blk->nama_lembaga ?? '-';
                })
                ->addColumn('jumlah_soal', function (EtamBlkForm $data) {
                    return (int) $data->pertanyaan_count;
                })
                ->addColumn('options', function (EtamBlkForm $data) use ($jenis) {
                    $editUrl = route('blk.form.edit', [$jenis, $data->id]);

                    return '
                        <a href="'.$editUrl.'" class="btn btn-primary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('.$data->id.')">Hapus</button>
                    ';
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        $judul = EtamBlkForm::jenisLabels()[$jenis];

        return view('backend.blk.form.index', compact('jenis', 'judul'));
    }

    public function create(string $jenis)
    {
        $this->assertJenis($jenis);
        $this->assertCanManage();

        $form = new EtamBlkForm(['jenis' => $jenis]);
        $blkOptions = $this->blkOptions();
        if ($this->isBlkStaffRole() && $blkOptions->count() === 1) {
            $form->blk_id = $blkOptions->first()->id;
        }
        $pertanyaan = collect();
        $isBlkStaff = $this->isBlkStaffRole();
        $judul = EtamBlkForm::jenisLabels()[$jenis];

        return view('backend.blk.form.builder', compact('jenis', 'judul', 'form', 'blkOptions', 'pertanyaan', 'isBlkStaff'));
    }

    public function store(Request $request, string $jenis)
    {
        $this->assertJenis($jenis);
        $this->assertCanManage();

        $validator = $this->validator($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (! $this->canAccessBlk((int) $request->blk_id)) {
            return redirect()->back()->with('error', 'Anda tidak berhak membuat template untuk BLK ini')->withInput();
        }

        DB::beginTransaction();
        try {
            $form = EtamBlkForm::create([
                'blk_id' => $request->blk_id,
                'jenis' => $jenis,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $this->syncPertanyaan($form, $request);
            DB::commit();

            return redirect()->route('blk.form.index', $jenis)->with('success', 'Template berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function edit(string $jenis, string $id)
    {
        $this->assertJenis($jenis);
        $this->assertCanManage();

        $form = EtamBlkForm::where('jenis', $jenis)->findOrFail($id);
        if (! $this->canAccessBlk((int) $form->blk_id)) {
            abort(403);
        }

        $blkOptions = $this->blkOptions();
        $pertanyaan = $form->pertanyaan()->get();
        $isBlkStaff = $this->isBlkStaffRole();
        $judul = EtamBlkForm::jenisLabels()[$jenis];

        return view('backend.blk.form.builder', compact('jenis', 'judul', 'form', 'blkOptions', 'pertanyaan', 'isBlkStaff'));
    }

    public function update(Request $request, string $jenis, string $id)
    {
        $this->assertJenis($jenis);
        $this->assertCanManage();

        $form = EtamBlkForm::where('jenis', $jenis)->findOrFail($id);
        if (! $this->canAccessBlk((int) $form->blk_id)) {
            abort(403);
        }

        $validator = $this->validator($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (! $this->canAccessBlk((int) $request->blk_id)) {
            return redirect()->back()->with('error', 'Anda tidak berhak memindah template ke BLK ini')->withInput();
        }

        DB::beginTransaction();
        try {
            $form->update([
                'blk_id' => $request->blk_id,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'updated_by' => Auth::id(),
            ]);
            $form->pertanyaan()->update(['deleted_by' => Auth::id()]);
            $form->pertanyaan()->delete();
            $this->syncPertanyaan($form, $request);
            DB::commit();

            return redirect()->route('blk.form.index', $jenis)->with('success', 'Template berhasil diupdate');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(string $jenis, string $id)
    {
        $this->assertJenis($jenis);
        $this->assertCanManage();

        $form = EtamBlkForm::where('jenis', $jenis)->findOrFail($id);
        if (! $this->canAccessBlk((int) $form->blk_id)) {
            abort(403);
        }

        $form->deleted_by = Auth::id();
        $form->save();
        $form->pertanyaan()->update(['deleted_by' => Auth::id()]);
        $form->pertanyaan()->delete();
        $form->delete();

        return response()->json(['success' => true, 'message' => 'Template berhasil dihapus']);
    }

    private function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'blk_id' => 'required|integer',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'pertanyaan' => 'required|array|min:1',
            'pertanyaan.*.teks' => 'required|string',
            'pertanyaan.*.jenis_pertanyaan' => 'required|in:1,2,3,4',
            'pertanyaan.*.wajib' => 'nullable',
            'pertanyaan.*.pilihan' => 'nullable|array',
            'pertanyaan.*.pilihan.*' => 'nullable|string',
        ], [
            'pertanyaan.required' => 'Minimal satu pertanyaan harus dibuat.',
            'pertanyaan.*.teks.required' => 'Teks pertanyaan wajib diisi.',
        ]);
    }

    private function syncPertanyaan(EtamBlkForm $form, Request $request): void
    {
        foreach (array_values((array) $request->pertanyaan) as $index => $item) {
            $jenis = (int) ($item['jenis_pertanyaan'] ?? 1);
            $pilihan = [];
            if (in_array($jenis, [EtamBlkFormPertanyaan::JENIS_PILIHAN, EtamBlkFormPertanyaan::JENIS_CHECKBOX], true)) {
                $pilihan = array_values(array_filter(array_map('trim', (array) ($item['pilihan'] ?? [])), fn ($row) => $row !== ''));
            }

            EtamBlkFormPertanyaan::create([
                'form_id' => $form->id,
                'urutan' => $index + 1,
                'jenis_pertanyaan' => $jenis,
                'pertanyaan' => $item['teks'] ?? '',
                'pilihan' => $pilihan === [] ? null : json_encode($pilihan),
                'wajib' => ! empty($item['wajib']) ? 1 : 0,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }

    private function assertJenis(string $jenis): void
    {
        if (! in_array($jenis, [EtamBlkForm::JENIS_WAWANCARA, EtamBlkForm::JENIS_PRETEST], true)) {
            abort(404);
        }
    }

    private function assertCanManage(): void
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, EtamBlk>
     */
    private function blkOptions()
    {
        $query = EtamBlk::query()->orderBy('nama_lembaga');
        $ids = $this->accessibleBlkIds();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }

        return $query->get(['id', 'nama_lembaga']);
    }
}
