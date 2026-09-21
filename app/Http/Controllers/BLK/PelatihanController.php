<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanFasilitas;
use App\Models\BLK\EtamBlkPelatihanSyarat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PelatihanController extends Controller
{
    use ManagesBlkAccess;

    public function index(Request $request)
    {
        $role = $this->currentRoleName();

        if ($request->ajax()) {
            $datas = EtamBlkPelatihan::query()
                ->with(['blk:id,nama_lembaga'])
                ->select(
                    'id',
                    'pelatihan_untuk',
                    'blk_id',
                    'nama_pelatihan',
                    'sumber_pembiayaan',
                    'tanggal_pendaftaran',
                    'tanggal_pendaftaran_selesai',
                    'tanggal_pelaksanaan',
                    'tanggal_pelaksanaan_selesai',
                    'tipe_pelatihan',
                    'status'
                );

            if (in_array($role, ['pencari-kerja', 'penyedia-kerja'], true)) {
                $datas->where('status', EtamBlkPelatihan::STATUS_AKTIF)
                    ->where('pelatihan_untuk', $role === 'pencari-kerja'
                        ? EtamBlkPelatihan::UNTUK_PENCARI
                        : EtamBlkPelatihan::UNTUK_PENYEDIA);
            } else {
                $blkIds = $this->accessibleBlkIds();
                if ($blkIds !== null) {
                    $datas->whereIn('blk_id', $blkIds);
                }
            }

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('blk_nama', function (EtamBlkPelatihan $data) {
                    return $data->blk->nama_lembaga ?? '-';
                })
                ->addColumn('untuk_nama', function (EtamBlkPelatihan $data) {
                    return EtamBlkPelatihan::untukLabels()[(int) $data->pelatihan_untuk] ?? '-';
                })
                ->addColumn('periode_daftar', function (EtamBlkPelatihan $data) {
                    return $this->formatPeriode($data->tanggal_pendaftaran, $data->tanggal_pendaftaran_selesai);
                })
                ->addColumn('periode_pelaksanaan', function (EtamBlkPelatihan $data) {
                    return $this->formatPeriode($data->tanggal_pelaksanaan, $data->tanggal_pelaksanaan_selesai);
                })
                ->addColumn('status_label', function (EtamBlkPelatihan $data) {
                    return $this->statusBadge((int) $data->status);
                })
                ->addColumn('options', function (EtamBlkPelatihan $data) use ($role) {
                    if (in_array($role, ['pencari-kerja', 'penyedia-kerja'], true)) {
                        $daftarUrl = route('blk.pelatihan.daftar', $data->id);

                        return '<a href="'.$daftarUrl.'" class="btn btn-success btn-sm">Daftar</a>';
                    }

                    $editUrl = route('blk.pelatihan.edit', $data->id);
                    $pesertaUrl = route('blk.pelatihan.peserta', $data->id);

                    return '
                        <a href="'.$pesertaUrl.'" class="btn btn-info btn-sm">Peserta</a>
                        <a href="'.$editUrl.'" class="btn btn-primary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('.$data->id.')">Delete</button>
                    ';
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        $canManage = $this->canCreatePelatihan();

        return view('backend.blk.pelatihan.index', compact('canManage', 'role'));
    }

    public function create()
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $blkOptions = $this->blkOptions();
        $pelatihan = new EtamBlkPelatihan;
        $syarat = collect();
        $fasilitas = collect();

        return view('backend.blk.pelatihan.form', compact('blkOptions', 'pelatihan', 'syarat', 'fasilitas'));
    }

    public function store(Request $request)
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $validator = $this->validator($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (! $this->canAccessBlk((int) $request->blk_id)) {
            return redirect()->back()->with('error', 'Anda tidak berhak menambah pelatihan untuk BLK ini')->withInput();
        }

        DB::beginTransaction();
        try {
            $pelatihan = new EtamBlkPelatihan;
            $this->fillPelatihan($pelatihan, $request);
            $pelatihan->slug = $pelatihan->generateSlug();
            $pelatihan->created_by = Auth::id();
            $pelatihan->save();

            $this->syncSyaratFasilitas($pelatihan, $request);

            DB::commit();

            return redirect()->route('blk.pelatihan.index')->with('success', 'Pelatihan berhasil ditambahkan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($id);
        if (! $this->canAccessBlk((int) $pelatihan->blk_id)) {
            abort(403);
        }

        $blkOptions = $this->blkOptions();
        $syarat = $pelatihan->syarat()->get();
        $fasilitas = $pelatihan->fasilitas()->get();

        return view('backend.blk.pelatihan.form', compact('blkOptions', 'pelatihan', 'syarat', 'fasilitas'));
    }

    public function update(Request $request, string $id)
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($id);
        if (! $this->canAccessBlk((int) $pelatihan->blk_id)) {
            abort(403);
        }

        $validator = $this->validator($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (! $this->canAccessBlk((int) $request->blk_id)) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengubah pelatihan ke BLK ini')->withInput();
        }

        DB::beginTransaction();
        try {
            $this->fillPelatihan($pelatihan, $request);
            $pelatihan->updated_by = Auth::id();
            $pelatihan->save();

            $pelatihan->syarat()->update(['deleted_by' => Auth::id()]);
            $pelatihan->syarat()->delete();
            $pelatihan->fasilitas()->update(['deleted_by' => Auth::id()]);
            $pelatihan->fasilitas()->delete();
            $this->syncSyaratFasilitas($pelatihan, $request);

            DB::commit();

            return redirect()->route('blk.pelatihan.index')->with('success', 'Pelatihan berhasil diupdate');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($id);
        if (! $this->canAccessBlk((int) $pelatihan->blk_id)) {
            abort(403);
        }

        $pelatihan->deleted_by = Auth::id();
        $pelatihan->save();
        $pelatihan->delete();

        return response()->json(['success' => true, 'message' => 'Hapus data berhasil']);
    }

    private function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'pelatihan_untuk' => 'required|in:0,1',
            'blk_id' => 'required|integer',
            'nama_pelatihan' => 'required|string|max:255',
            'sumber_pembiayaan' => 'required|in:0,1,2',
            'tanggal_pendaftaran' => 'required|date',
            'tanggal_pendaftaran_selesai' => 'required|date|after_or_equal:tanggal_pendaftaran',
            'tanggal_pelaksanaan' => 'required|date',
            'tanggal_pelaksanaan_selesai' => 'required|date|after_or_equal:tanggal_pelaksanaan',
            'tipe_pelatihan' => 'required|in:0,1,2,3',
            'info_lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2,3',
            'deskripsi' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'syarat' => 'nullable|array',
            'syarat.*' => 'nullable|string',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'nullable|string',
        ]);
    }

    private function fillPelatihan(EtamBlkPelatihan $pelatihan, Request $request): void
    {
        $pelatihan->fill([
            'pelatihan_untuk' => $request->pelatihan_untuk,
            'blk_id' => $request->blk_id,
            'nama_pelatihan' => $request->nama_pelatihan,
            'sumber_pembiayaan' => $request->sumber_pembiayaan,
            'tanggal_pendaftaran' => $request->tanggal_pendaftaran,
            'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'tanggal_pelaksanaan_selesai' => $request->tanggal_pelaksanaan_selesai,
            'tipe_pelatihan' => $request->tipe_pelatihan,
            'info_lokasi' => $request->info_lokasi,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
            'updated_by' => Auth::id(),
        ]);

        if ($request->hasFile('poster')) {
            if ($pelatihan->poster) {
                Storage::disk('public')->delete($pelatihan->poster);
            }
            $pelatihan->poster = $request->file('poster')->store('blk/pelatihan', 'public');
        }
    }

    private function syncSyaratFasilitas(EtamBlkPelatihan $pelatihan, Request $request): void
    {
        foreach ((array) $request->syarat as $item) {
            if (trim((string) $item) === '') {
                continue;
            }

            EtamBlkPelatihanSyarat::create([
                'blk_pelatihan_id' => $pelatihan->id,
                'persyaratan' => $item,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        foreach ((array) $request->fasilitas as $item) {
            if (trim((string) $item) === '') {
                continue;
            }

            EtamBlkPelatihanFasilitas::create([
                'blk_pelatihan_id' => $pelatihan->id,
                'fasilitas' => $item,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }

    private function formatPeriode($start, $end): string
    {
        $from = $start ? $start->format('d-m-Y') : '-';
        $to = $end ? $end->format('d-m-Y') : '-';

        return $from.' s/d '.$to;
    }

    private function statusBadge(int $status): string
    {
        $map = [
            0 => 'secondary',
            1 => 'success',
            2 => 'warning',
            3 => 'dark',
        ];
        $label = EtamBlkPelatihan::statusLabels()[$status] ?? '-';
        $color = $map[$status] ?? 'secondary';

        return '<span class="badge bg-'.$color.'">'.$label.'</span>';
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
