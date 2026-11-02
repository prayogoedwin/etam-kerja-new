<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdminSuketController extends Controller
{
    use ManagesPjk3Access;

    public function pengajuanIndex(Request $request)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);

        if ($request->ajax()) {
            $query = EtamPjk3Suket::query()
                ->with(['profilPjk3:id,user_id,name', 'profilPerusahaan:id,user_id,name', 'kategori:id,nama,kode']);

            if ($this->currentRoleName() === 'petugas-bidang') {
                $query->where('pengawas_id', Auth::id());
            }

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('pjk3', fn (EtamPjk3Suket $row) => $row->profilPjk3->name ?? '-')
                ->addColumn('perusahaan', fn (EtamPjk3Suket $row) => $row->profilPerusahaan->name ?? '-')
                ->addColumn('kategori_nama', fn (EtamPjk3Suket $row) => $row->kategori->nama ?? '-')
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusBadge($row))
                ->addColumn('options', function (EtamPjk3Suket $row) {
                    return '<a href="'.route('pengawasan.suket.admin.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.suket.admin-pengajuan-index');
    }

    public function terbitIndex(Request $request)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);

        if ($request->ajax()) {
            $query = EtamPjk3Suket::query()
                ->with(['profilPjk3:id,user_id,name', 'profilPerusahaan:id,user_id,name', 'kategori:id,nama,kode'])
                ->where('status', '>=', EtamPjk3Suket::STATUS_DRAFT);

            if ($this->currentRoleName() === 'petugas-bidang') {
                $query->where('pengawas_id', Auth::id());
            }

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('nomor', fn (EtamPjk3Suket $row) => $row->nomor_suket ?? '-')
                ->addColumn('pjk3', fn (EtamPjk3Suket $row) => $row->profilPjk3->name ?? '-')
                ->addColumn('perusahaan', fn (EtamPjk3Suket $row) => $row->profilPerusahaan->name ?? '-')
                ->addColumn('hasil', function (EtamPjk3Suket $row) {
                    if ($row->is_layak === null) {
                        return '-';
                    }

                    return (int) $row->is_layak === 1
                        ? '<span class="badge bg-success">Layak</span>'
                        : '<span class="badge bg-danger">Tidak layak</span>';
                })
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusBadge($row))
                ->addColumn('options', function (EtamPjk3Suket $row) {
                    return '<a href="'.route('pengawasan.suket.admin.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['hasil', 'status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.suket.admin-terbit-index');
    }

    public function show(string $id)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);
        $suket = $this->findVisible($id);

        return view('backend.pengawasan.pjk3.suket.admin-show', [
            'suket' => $suket->load(['profilPjk3', 'profilPerusahaan', 'kategori', 'jenis', 'pengawas', 'kasi']),
            'dokumenKeys' => EtamPjk3Suket::dokumenKeys(),
            'daftarPengawas' => $this->daftarPengawasBidangDua(),
            'canDispo' => $this->isKasiK3() && (int) $suket->status === EtamPjk3Suket::STATUS_DIAJUKAN,
            'canAnalisis' => $this->isPengawasK3() && in_array((int) $suket->status, [EtamPjk3Suket::STATUS_DISPO, EtamPjk3Suket::STATUS_DRAFT], true)
                && ($this->currentRoleName() !== 'petugas-bidang' || (int) $suket->pengawas_id === (int) Auth::id()),
            'canTtePengawas' => $this->isPengawasK3() && (int) $suket->status === EtamPjk3Suket::STATUS_DRAFT
                && ($this->currentRoleName() !== 'petugas-bidang' || (int) $suket->pengawas_id === (int) Auth::id()),
            'canTteKadis' => $this->isKadis() && (int) $suket->status === EtamPjk3Suket::STATUS_TTD_PENGAWAS,
        ]);
    }

    public function file(string $id, string $key)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);

        return $this->streamSuketDokumen($this->findVisible($id), $key);
    }

    public function disposisi(Request $request, string $id)
    {
        abort_unless($this->isKasiK3(), 403);
        $suket = EtamPjk3Suket::findOrFail($id);
        abort_unless((int) $suket->status === EtamPjk3Suket::STATUS_DIAJUKAN, 422);

        $validator = Validator::make($request->all(), [
            'pengawas_id' => 'required|exists:users,id',
            'catatan_disposisi' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $pengawasId = (int) $request->pengawas_id;
        abort_unless(
            $this->daftarPengawasBidangDua()->contains(fn ($user) => (int) $user->id === $pengawasId),
            422
        );

        $suket->update([
            'pengawas_id' => $request->pengawas_id,
            'kasi_id' => Auth::id(),
            'catatan_disposisi' => $request->catatan_disposisi,
            'status' => EtamPjk3Suket::STATUS_DISPO,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Disposisi ke pengawas berhasil.']);
    }

    public function analisis(Request $request, string $id)
    {
        abort_unless($this->isPengawasK3(), 403);
        $suket = $this->findVisible($id);
        abort_unless(in_array((int) $suket->status, [EtamPjk3Suket::STATUS_DISPO, EtamPjk3Suket::STATUS_DRAFT], true), 422);

        $validator = Validator::make($request->all(), [
            'tanggal_pemeriksaan' => 'required|date',
            'is_layak' => 'required|in:0,1',
            'hasil_analisis' => 'required|string',
            'rekomendasi' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $nomor = $suket->nomor_suket;
        if (! $nomor) {
            $kode = $suket->kategori()->value('kode') ?: 'K3';
            $nomor = $this->nextNomorSuket((int) $suket->kategori_id, $kode);
        }

        $suket->update([
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'is_layak' => $request->is_layak,
            'hasil_analisis' => $request->hasil_analisis,
            'rekomendasi' => $request->rekomendasi,
            'nomor_suket' => $nomor,
            'status' => EtamPjk3Suket::STATUS_DRAFT,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hasil analisis disimpan. Draft LHPP dan Suket muncul di akun perusahaan yang diuji.',
        ]);
    }

    public function ttePengawas(string $id)
    {
        abort_unless($this->isPengawasK3(), 403);
        $suket = $this->findVisible($id);
        abort_unless((int) $suket->status === EtamPjk3Suket::STATUS_DRAFT, 422);

        $suket->update([
            'status' => EtamPjk3Suket::STATUS_TTD_PENGAWAS,
            'ttd_pengawas_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('pengawasan.suket.admin.show', $suket->id)
            ->with('success', 'TTD pengawas tercatat (demo alur). Diajukan ke Kepala Dinas.');
    }

    public function tteKadis(string $id)
    {
        abort_unless($this->isKadis(), 403);
        $suket = EtamPjk3Suket::findOrFail($id);
        abort_unless((int) $suket->status === EtamPjk3Suket::STATUS_TTD_PENGAWAS, 422);

        $suket->update([
            'status' => EtamPjk3Suket::STATUS_TTD_KADIS,
            'ttd_kadis_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('pengawasan.suket.admin.show', $suket->id)
            ->with('success', 'TTD Kepala Dinas tercatat (demo alur). Suket selesai.');
    }

    public function cetakLhpp(string $id)
    {
        $suket = $this->cetakContext($id);

        return view('backend.pengawasan.pjk3.suket.cetak-lhpp', compact('suket'));
    }

    public function cetakSuket(string $id)
    {
        $suket = $this->cetakContext($id);

        return view('backend.pengawasan.pjk3.suket.cetak-suket', compact('suket'));
    }

    private function cetakContext(string $id): EtamPjk3Suket
    {
        $suket = EtamPjk3Suket::with(['profilPjk3', 'profilPerusahaan', 'kategori', 'jenis', 'pengawas'])->findOrFail($id);
        abort_unless((int) $suket->status >= EtamPjk3Suket::STATUS_DRAFT, 404);

        $userId = (int) Auth::id();
        $ok = $this->canAccessSuketAdmin()
            || $userId === (int) $suket->pjk3_penyedia_id
            || $userId === (int) $suket->perusahaan_id;
        abort_unless($ok, 403);

        return $suket;
    }

    private function findVisible(string $id): EtamPjk3Suket
    {
        $query = EtamPjk3Suket::query();
        if ($this->currentRoleName() === 'petugas-bidang') {
            $query->where('pengawas_id', Auth::id());
        }

        return $query->findOrFail($id);
    }

    private function statusBadge(EtamPjk3Suket $row): string
    {
        $map = [
            EtamPjk3Suket::STATUS_DIAJUKAN => 'warning',
            EtamPjk3Suket::STATUS_DISPO => 'info',
            EtamPjk3Suket::STATUS_DRAFT => 'secondary',
            EtamPjk3Suket::STATUS_TTD_PENGAWAS => 'primary',
            EtamPjk3Suket::STATUS_TTD_KADIS => 'success',
        ];
        $color = $map[(int) $row->status] ?? 'secondary';

        return '<span class="badge bg-'.$color.'">'.$row->statusLabel().'</span>';
    }
}
