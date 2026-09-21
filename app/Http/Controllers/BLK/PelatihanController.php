<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkForm;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanFasilitas;
use App\Models\BLK\EtamBlkPelatihanJawaban;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\BLK\EtamBlkPelatihanPesertaPerusahaan;
use App\Models\BLK\EtamBlkPelatihanSyarat;
use App\Models\UserPencari;
use App\Models\UserPenyedia;
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
                    'status',
                    'wawancara_form_id',
                    'pretest_form_id'
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

            [$pesertaMap, $pretestSubmitted, $pesertaStatus] = $this->pesertaPretestState($role);

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
                ->addColumn('status_label', function (EtamBlkPelatihan $data) use ($role, $pesertaStatus) {
                    if (in_array($role, ['pencari-kerja', 'penyedia-kerja'], true)) {
                        if (! isset($pesertaStatus[$data->id])) {
                            return '<span class="badge bg-secondary">Belum daftar</span>';
                        }

                        return $this->pesertaStatusBadge((int) $pesertaStatus[$data->id]);
                    }

                    return $this->statusBadge((int) $data->status);
                })
                ->addColumn('options', function (EtamBlkPelatihan $data) use ($role, $pesertaMap, $pretestSubmitted) {
                    if (in_array($role, ['pencari-kerja', 'penyedia-kerja'], true)) {
                        $html = '<div class="d-flex flex-wrap gap-1">';
                        if (isset($pesertaMap[$data->id])) {
                            $html .= '<a href="'.route('blk.pelatihan.daftar', $data->id).'" class="btn btn-outline-primary btn-sm">Lihat Status</a>';
                            if ($data->pretest_form_id) {
                                $label = in_array((int) $data->id, $pretestSubmitted, true) ? 'Lihat Pretest' : 'Isi Pretest';
                                $html .= '<a href="'.route('blk.pelatihan.pretest', $data->id).'" class="btn btn-primary btn-sm">'.$label.'</a>';
                            }
                        } else {
                            $html .= '<a href="'.route('blk.pelatihan.daftar', $data->id).'" class="btn btn-success btn-sm">Daftar</a>';
                        }
                        $html .= '</div>';

                        return $html;
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
        if ($this->isBlkStaffRole() && $blkOptions->count() === 1) {
            $pelatihan->blk_id = $blkOptions->first()->id;
        }
        $syarat = collect();
        $fasilitas = collect();
        $isBlkStaff = $this->isBlkStaffRole();
        [$wawancaraTemplates, $pretestTemplates] = $this->formTemplates();

        return view('backend.blk.pelatihan.form', compact(
            'blkOptions',
            'pelatihan',
            'syarat',
            'fasilitas',
            'isBlkStaff',
            'wawancaraTemplates',
            'pretestTemplates'
        ));
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

        if (! $this->canAccessBlk((int) $this->requestBlkId($request))) {
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
        $isBlkStaff = $this->isBlkStaffRole();
        [$wawancaraTemplates, $pretestTemplates] = $this->formTemplates();

        return view('backend.blk.pelatihan.form', compact(
            'blkOptions',
            'pelatihan',
            'syarat',
            'fasilitas',
            'isBlkStaff',
            'wawancaraTemplates',
            'pretestTemplates'
        ));
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

        if (! $this->canAccessBlk((int) $this->requestBlkId($request))) {
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
            'wawancara_form_id' => 'nullable|integer',
            'pretest_form_id' => 'nullable|integer',
        ]);
    }

    private function fillPelatihan(EtamBlkPelatihan $pelatihan, Request $request): void
    {
        $pelatihan->fill([
            'pelatihan_untuk' => $request->pelatihan_untuk,
            'blk_id' => $this->requestBlkId($request),
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
            'wawancara_form_id' => $this->resolveFormId($request, EtamBlkForm::JENIS_WAWANCARA),
            'pretest_form_id' => $this->resolveFormId($request, EtamBlkForm::JENIS_PRETEST),
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

    private function pesertaStatusBadge(int $status): string
    {
        $map = [
            0 => 'warning',
            1 => 'info',
            2 => 'success',
            3 => 'danger',
            4 => 'secondary',
            5 => 'primary',
        ];
        $label = EtamBlkPelatihanPeserta::statusLabels()[$status] ?? '-';
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

    /**
     * @return array{0: \Illuminate\Support\Collection<int, EtamBlkForm>, 1: \Illuminate\Support\Collection<int, EtamBlkForm>}
     */
    private function formTemplates(): array
    {
        $query = EtamBlkForm::query()->orderBy('nama');
        $ids = $this->accessibleBlkIds();
        if ($ids !== null) {
            $query->whereIn('blk_id', $ids);
        }

        $all = $query->get(['id', 'blk_id', 'jenis', 'nama']);

        return [
            $all->where('jenis', EtamBlkForm::JENIS_WAWANCARA)->values(),
            $all->where('jenis', EtamBlkForm::JENIS_PRETEST)->values(),
        ];
    }

    private function resolveFormId(Request $request, string $jenis): ?int
    {
        $field = $jenis === EtamBlkForm::JENIS_PRETEST ? 'pretest_form_id' : 'wawancara_form_id';
        $id = $request->input($field);
        if (! $id) {
            return null;
        }

        $form = EtamBlkForm::query()
            ->where('id', $id)
            ->where('jenis', $jenis)
            ->where('blk_id', $this->requestBlkId($request))
            ->first();

        return $form ? (int) $form->id : null;
    }

    private function requestBlkId(Request $request): ?int
    {
        $id = $request->input('blk_id');
        if (is_array($id)) {
            $id = end($id);
        }

        return $id ? (int) $id : null;
    }

    /**
     * @return array{0: array<int, int>, 1: array<int, int>, 2: array<int, int>}
     */
    private function pesertaPretestState(?string $role): array
    {
        if (! in_array($role, ['pencari-kerja', 'penyedia-kerja'], true)) {
            return [[], [], []];
        }

        if ($role === 'pencari-kerja') {
            $pencari = UserPencari::where('user_id', Auth::id())->first();
            if (! $pencari) {
                return [[], [], []];
            }

            $rows = EtamBlkPelatihanPeserta::query()
                ->where('pencari_id', $pencari->id)
                ->get(['id', 'blk_pelatihan_id', 'status_pendaftaran']);

            return $this->mapPesertaState($rows, 'blk_peserta_id');
        }

        $penyedia = UserPenyedia::where('user_id', Auth::id())->first();
        if (! $penyedia) {
            return [[], [], []];
        }

        $rows = EtamBlkPelatihanPesertaPerusahaan::query()
            ->where('perusahaan_id', $penyedia->id)
            ->get(['id', 'blk_pelatihan_id', 'status_pendaftaran']);

        return $this->mapPesertaState($rows, 'perusahaan_peserta_id');
    }

    /**
     * @param  \Illuminate\Support\Collection<int, mixed>  $rows
     * @return array{0: array<int, int>, 1: array<int, int>, 2: array<int, int>}
     */
    private function mapPesertaState($rows, string $jawabanKey): array
    {
        $pesertaMap = [];
        $pesertaStatus = [];
        foreach ($rows as $row) {
            $pelatihanId = (int) $row->blk_pelatihan_id;
            $pesertaMap[$pelatihanId] = (int) $row->id;
            $pesertaStatus[$pelatihanId] = (int) $row->status_pendaftaran;
        }

        $pretestSubmitted = [];
        if ($pesertaMap !== []) {
            $pretestSubmitted = EtamBlkPelatihanJawaban::query()
                ->where('jenis', EtamBlkForm::JENIS_PRETEST)
                ->whereNotNull('submitted_at')
                ->whereIn($jawabanKey, array_values($pesertaMap))
                ->pluck('blk_pelatihan_id')
                ->unique()
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return [$pesertaMap, $pretestSubmitted, $pesertaStatus];
    }
}
