<?php

namespace App\Http\Controllers;

use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\UserPencari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HistoryPelatihanPencariController extends Controller
{
    public function index(Request $request)
    {
        if ((Auth::user()->roles[0]['name'] ?? null) !== 'pencari-kerja') {
            abort(403);
        }

        if ($request->ajax()) {
            $pencari = UserPencari::where('user_id', Auth::id())->first();
            $pencariId = $pencari?->id ?? 0;

            $historys = EtamBlkPelatihanPeserta::query()
                ->with([
                    'pelatihan:id,nama_pelatihan,blk_id,tanggal_pelaksanaan,tanggal_pelaksanaan_selesai,pretest_form_id',
                    'pelatihan.blk:id,nama_lembaga',
                ])
                ->select('id', 'blk_pelatihan_id', 'status_pendaftaran', 'created_at')
                ->where('pencari_id', $pencariId)
                ->latest();

            if (! empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $historys->where(function ($query) use ($searchValue) {
                    $query->whereHas('pelatihan', function ($pelatihanQuery) use ($searchValue) {
                        $pelatihanQuery->where('nama_pelatihan', 'like', "%{$searchValue}%")
                            ->orWhereHas('blk', function ($blkQuery) use ($searchValue) {
                                $blkQuery->where('nama_lembaga', 'like', "%{$searchValue}%");
                            });
                    });
                });
            }

            $statusMap = [
                0 => 'warning',
                1 => 'info',
                2 => 'success',
                3 => 'danger',
                4 => 'secondary',
                5 => 'primary',
            ];

            return DataTables::of($historys)
                ->addIndexColumn()
                ->addColumn('nama_pelatihan', function (EtamBlkPelatihanPeserta $history) {
                    return $history->pelatihan->nama_pelatihan ?? '-';
                })
                ->addColumn('blk_nama', function (EtamBlkPelatihanPeserta $history) {
                    return $history->pelatihan->blk->nama_lembaga ?? '-';
                })
                ->editColumn('created_at', function (EtamBlkPelatihanPeserta $history) {
                    return optional($history->created_at)->format('d-m-Y H:i') ?? '-';
                })
                ->addColumn('pelaksanaan', function (EtamBlkPelatihanPeserta $history) {
                    $mulai = optional($history->pelatihan?->tanggal_pelaksanaan)->format('d-m-Y');
                    $selesai = optional($history->pelatihan?->tanggal_pelaksanaan_selesai)->format('d-m-Y');

                    if (! $mulai && ! $selesai) {
                        return '-';
                    }

                    return trim(($mulai ?: '-').' s/d '.($selesai ?: '-'));
                })
                ->addColumn('status', function (EtamBlkPelatihanPeserta $history) use ($statusMap) {
                    $status = (int) $history->status_pendaftaran;
                    $label = EtamBlkPelatihanPeserta::statusLabels()[$status] ?? '-';
                    $color = $statusMap[$status] ?? 'secondary';

                    return '<span class="badge bg-'.$color.'">'.$label.'</span>';
                })
                ->addColumn('options', function (EtamBlkPelatihanPeserta $history) {
                    $pelatihan = $history->pelatihan;
                    if (! $pelatihan) {
                        return '-';
                    }

                    $html = '<a href="'.route('blk.pelatihan.daftar', $pelatihan->id).'" class="btn btn-outline-primary btn-sm">Lihat Status</a>';
                    if ($pelatihan->pretest_form_id) {
                        $html .= ' <a href="'.route('blk.pelatihan.pretest', $pelatihan->id).'" class="btn btn-primary btn-sm">Pretest</a>';
                    }

                    return $html;
                })
                ->rawColumns(['status', 'options'])
                ->make(true);
        }

        return view('backend.historypelatihanpencari.index');
    }
}
