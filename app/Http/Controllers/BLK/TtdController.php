<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TtdController extends Controller
{
    use ManagesBlkAccess;

    public function menunggu(Request $request)
    {
        return $this->index($request, 'menunggu');
    }

    public function sudah(Request $request)
    {
        return $this->index($request, 'sudah');
    }

    public function tte(Request $request)
    {
        if (! $this->canAccessTtdMenu()) {
            abort(403);
        }

        $waitingStatus = $this->waitingStatus();

        if ($request->boolean('pilih_semua')) {
            $allowed = $this->baseQuery($waitingStatus)
                ->pluck('id')
                ->all();
        } else {
            $ids = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('peserta_ids', [])))));
            if ($ids === []) {
                return redirect()->back()->with('error', 'Pilih minimal satu peserta untuk ditandatangani');
            }

            $allowed = $this->baseQuery($waitingStatus)
                ->whereIn('id', $ids)
                ->pluck('id')
                ->all();
        }

        if ($allowed === []) {
            return redirect()->back()->with('error', 'Peserta terpilih tidak ada dalam antrean TTD Anda');
        }

        $now = now();
        if ($this->isKepalaDinas()) {
            EtamBlkPelatihanPeserta::query()
                ->whereIn('id', $allowed)
                ->update([
                    'status_ttd' => EtamBlkPelatihanPeserta::TTD_TTD_DINAS,
                    'ttd_dinas_at' => $now,
                    'updated_by' => Auth::id(),
                ]);
            $pesan = count($allowed).' peserta ditandatangani Kepala Dinas (demo alur, TTE belum dijalankan)';
        } else {
            EtamBlkPelatihanPeserta::query()
                ->whereIn('id', $allowed)
                ->update([
                    'status_ttd' => EtamBlkPelatihanPeserta::TTD_TTD_UPTD,
                    'ttd_uptd_at' => $now,
                    'updated_by' => Auth::id(),
                ]);
            $pesan = count($allowed).' peserta ditandatangani Kepala UPTD dan diajukan ke Kepala Dinas (demo alur, TTE belum dijalankan)';
        }

        return redirect()->route('blk.ttd.menunggu')->with('success', $pesan);
    }

    public function maintenance()
    {
        if (! $this->canAccessTtdMenu()) {
            abort(403);
        }

        $ids = session('tte_peserta_ids', []);
        $peserta = EtamBlkPelatihanPeserta::query()
            ->with(['pelatihan.blk'])
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get();

        return view('backend.blk.ttd.maintenance', compact('peserta'));
    }

    private function index(Request $request, string $jenis)
    {
        if (! $this->canAccessTtdMenu()) {
            abort(403);
        }

        $waiting = $jenis === 'menunggu';
        $status = $waiting ? $this->waitingStatus() : $this->signedStatus();

        if ($request->ajax()) {
            $query = $this->baseQuery($status);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function (EtamBlkPelatihanPeserta $row) use ($waiting) {
                    if (! $waiting) {
                        return '';
                    }

                    return '<input type="checkbox" class="peserta-check" name="peserta_ids[]" value="'.$row->id.'">';
                })
                ->addColumn('pelatihan_nama', function (EtamBlkPelatihanPeserta $row) {
                    return $row->pelatihan->nama_pelatihan ?? '-';
                })
                ->addColumn('blk_nama', function (EtamBlkPelatihanPeserta $row) {
                    return $row->pelatihan->blk->nama_lembaga ?? '-';
                })
                ->addColumn('status_label', function (EtamBlkPelatihanPeserta $row) {
                    return EtamBlkPelatihanPeserta::statusTtdBadge((int) $row->status_ttd);
                })
                ->addColumn('options', function (EtamBlkPelatihanPeserta $row) use ($waiting) {
                    $html = '<a href="'.route('blk.pelatihan.peserta.sertifikat', [$row->blk_pelatihan_id, $row->id, 'sertifikat']).'" class="btn btn-secondary btn-sm" target="_blank">Sertifikat</a>';
                    $html .= ' <a href="'.route('blk.pelatihan.peserta.sertifikat', [$row->blk_pelatihan_id, $row->id, 'kompetensi']).'" class="btn btn-outline-secondary btn-sm" target="_blank">Unit Kompetensi</a>';
                    if ($waiting) {
                        $html .= ' <button type="button" class="btn btn-primary btn-sm btn-ttd-satu" data-id="'.$row->id.'">TTD</button>';
                    }

                    return $html;
                })
                ->rawColumns(['checkbox', 'status_label', 'options'])
                ->make(true);
        }

        $role = $this->currentRoleName();
        $judul = $waiting
            ? ($this->isKepalaDinas() ? 'Menunggu TTD Kepala Dinas' : 'Menunggu TTD Kepala UPTD')
            : 'Sudah ditandatangani';

        return view('backend.blk.ttd.index', compact('jenis', 'waiting', 'role', 'judul'));
    }

    private function waitingStatus(): int
    {
        return $this->isKepalaDinas()
            ? EtamBlkPelatihanPeserta::TTD_TTD_UPTD
            : EtamBlkPelatihanPeserta::TTD_DIAJUKAN_UPTD;
    }

    /**
     * @return array<int, int>
     */
    private function signedStatus(): array
    {
        return $this->isKepalaDinas()
            ? [EtamBlkPelatihanPeserta::TTD_TTD_DINAS]
            : [EtamBlkPelatihanPeserta::TTD_TTD_UPTD, EtamBlkPelatihanPeserta::TTD_TTD_DINAS];
    }

    private function baseQuery(int|array $statusTtd)
    {
        $query = EtamBlkPelatihanPeserta::query()
            ->with(['pelatihan.blk'])
            ->where('status_pendaftaran', EtamBlkPelatihanPeserta::STATUS_LULUS);

        if (is_array($statusTtd)) {
            $query->whereIn('status_ttd', $statusTtd);
        } else {
            $query->where('status_ttd', $statusTtd);
        }

        if ($this->isKepalaUptd()) {
            $kode = Auth::user()?->kode_struktur;
            if (! $kode) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('pelatihan.blk', function ($q) use ($kode) {
                    $q->where('kode_struktur', $kode);
                });
            }
        }

        return $query;
    }
}
