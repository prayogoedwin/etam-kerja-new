<?php

namespace App\Http\Controllers\Tte;

use App\Http\Controllers\Controller;
use App\Models\EtamTte;
use App\Models\EtamTteLog;
use App\Services\Tte\TteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TteController extends Controller
{
    public function __construct(private TteService $tte) {}

    public function menunggu(Request $request)
    {
        return $this->index($request, true);
    }

    public function sudah(Request $request)
    {
        return $this->index($request, false);
    }

    public function log(Request $request)
    {
        if (! $this->tte->canAccess(Auth::user())) {
            abort(403);
        }

        if ($request->ajax()) {
            $query = EtamTteLog::query()->with('user:id,name')->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('waktu', function (EtamTteLog $row) {
                    return optional($row->created_at)->format('d/m/Y H:i:s');
                })
                ->addColumn('petugas', function (EtamTteLog $row) {
                    return $row->user->name ?? '-';
                })
                ->addColumn('status_label', function (EtamTteLog $row) {
                    return EtamTteLog::statusBadge((int) $row->status);
                })
                ->rawColumns(['status_label'])
                ->make(true);
        }

        return view('backend.tte.log');
    }

    public function tandatangani(Request $request)
    {
        if (! $this->tte->canAccess(Auth::user())) {
            abort(403);
        }

        $passphrase = (string) $request->input('passphrase', '');
        $pilihSemua = $request->boolean('pilih_semua');
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('tte_ids', [])))));

        if (! $pilihSemua && $ids === []) {
            return response()->json([
                'count' => 0,
                'berhasil' => 0,
                'gagal' => 0,
                'kode' => null,
                'message' => 'Pilih minimal satu dokumen untuk ditandatangani',
                'items' => [],
            ], 422);
        }

        $hasil = $this->tte->tandatangani($ids, $pilihSemua, $request->input('filter_modul'), $passphrase);

        $http = 200;
        if (in_array($hasil['kode'], ['passphrase_salah', 'koneksi_putus', 'nik_kosong'], true)) {
            $http = 422;
        }

        return response()->json($hasil, $http);
    }

    public function berkas(string $token)
    {
        $tte = EtamTte::query()->where('token_verifikasi', $token)->firstOrFail();
        $path = $tte->path_file_after ?: $tte->path_file_before;
        $adaBerkas = $path && Storage::disk('local')->exists($path);

        if ($tte->status === EtamTte::STATUS_BERHASIL && $adaBerkas && request()->boolean('unduh')) {
            return response()->streamDownload(function () use ($path) {
                echo Storage::disk('local')->get($path);
            }, 'berkas-tte-'.$tte->id.'.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return view('backend.tte.berkas', [
            'tte' => $tte,
            'judul' => $this->tte->judulUntuk($tte),
            'adaBerkas' => $adaBerkas,
            'urlUnduh' => $adaBerkas && $tte->status === EtamTte::STATUS_BERHASIL
                ? route('tte.berkas', ['token' => $token, 'unduh' => 1])
                : null,
        ]);
    }

    private function index(Request $request, bool $waiting)
    {
        if (! $this->tte->canAccess(Auth::user())) {
            abort(403);
        }

        $filterModul = trim((string) $request->input('filter_modul', ''));
        $modulList = $this->tte->daftarModul();

        if ($request->ajax()) {
            $query = $this->tte->visibleQuery(Auth::user());
            if ($waiting) {
                $query->where('status', EtamTte::STATUS_MENUNGGU);
            } else {
                $query->whereIn('status', [EtamTte::STATUS_BERHASIL, EtamTte::STATUS_GAGAL]);
            }
            $this->tte->applyFilterModul($query, $filterModul);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function (EtamTte $row) use ($waiting) {
                    if (! $waiting) {
                        return '';
                    }

                    return '<input type="checkbox" class="tte-check" name="tte_ids[]" value="'.$row->id.'">';
                })
                ->addColumn('judul', function (EtamTte $row) {
                    return $this->tte->judulUntuk($row);
                })
                ->addColumn('modul', function (EtamTte $row) {
                    return $row->nama_modul;
                })
                ->addColumn('status_label', function (EtamTte $row) {
                    return EtamTte::statusBadge((int) $row->status);
                })
                ->addColumn('options', function (EtamTte $row) use ($waiting) {
                    $html = '';
                    if ($waiting) {
                        $html .= '<button type="button" class="btn btn-primary btn-sm btn-ttd-satu" data-id="'.$row->id.'" data-judul="'.e($this->tte->judulUntuk($row)).'">TTD</button>';
                    }
                    if ($row->token_verifikasi) {
                        $html .= ' <a class="btn btn-outline-secondary btn-sm" target="_blank" href="'.route('tte.berkas', $row->token_verifikasi).'">Berkas</a>';
                    }

                    return $html;
                })
                ->rawColumns(['checkbox', 'status_label', 'options'])
                ->make(true);
        }

        $judul = $waiting
            ? ($this->tte->isKepalaDinas() ? 'Menunggu TTD Kepala Dinas' : 'Menunggu TTD')
            : 'Sudah ditandatangani';

        return view('backend.tte.index', compact('waiting', 'judul', 'modulList', 'filterModul'));
    }
}
