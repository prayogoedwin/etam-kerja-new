<?php

namespace App\Http\Controllers\HI\PP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\HI\PP\EtamHiPpAjuan;
use App\Models\HI\PP\EtamHiPpSyaratdokumen;

class KasiBidangPpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // SoftDeletes otomatis menambahkan WHERE deleted_at IS NULL
            $datas = EtamHiPpAjuan::select(
                    'id',
                    'jenis_ajuan',
                    'perusahaan_id',
                    'nomor',
                    'tanggal',
                    'verifikasi_admin',
                    'verifikasi_kasi',
                    'created_at'
                )
                ->with(['jenisAjuan:id,nama'])
                ->where('verifikasi_admin', 1)          // ← hanya yang sudah ACC admin
                ->orderBy('verifikasi_admin_at', 'asc'); // FIFO: yang lebih dulu diverifikasi admin, tampil dulu

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('jenis_ajuan_nama', function ($data) {
                    return $data->jenisAjuan->nama ?? '-';
                })
                ->addColumn('tanggal_fmt', function ($data) {
                    return $data->tanggal
                        ? \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y')
                        : '-';
                })
                ->addColumn('status_admin', function ($data) {
                    return $this->labelVerifikasi($data->verifikasi_admin);
                })
                ->addColumn('status_kasi', function ($data) {
                    return $this->labelVerifikasi($data->verifikasi_kasi);
                })
                ->addColumn('options', function ($data) {
                    $detailUrl = route('hi.pp.kasibidang.detail', $data->id);
                    return '<a href="' . $detailUrl . '" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['options', 'status_admin', 'status_kasi'])
                ->make(true);
        }

        return view('backend.hi.pp.kasibidang.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Halaman detail pengajuan (read-only) + tombol verifikasi.
     */
    public function detail($id)
    {
        $ajuan = EtamHiPpAjuan::with([
            'jenisAjuan:id,nama',
            'syaratDokumen.syaratDokumen:id,nama',
        ])
        ->where('verifikasi_admin', 1) // pastikan hanya ajuan yang sudah ACC admin
        ->findOrFail($id);

        $syaratDokumen = EtamHiPpSyaratdokumen::where('id', '!=', 8)
            ->orderBy('id', 'asc')
            ->get();

        $uploadedDokumen = $ajuan->syaratDokumen
            ->pluck('path_dokumen', 'syaratdokumen_id')
            ->toArray();

        return view('backend.hi.pp.kasibidang.detail', compact(
            'ajuan',
            'syaratDokumen',
            'uploadedDokumen'
        ));
    }

    /**
     * Form verifikasi kasi.
     */
    public function formVerifikasi($id)
    {
        $ajuan = EtamHiPpAjuan::with(['jenisAjuan:id,nama'])
            ->where('verifikasi_admin', 1)
            ->findOrFail($id);

        return view('backend.hi.pp.kasibidang.verifikasi', compact('ajuan'));
    }

    /**
     * Proses verifikasi kasi.
     * - type = 'acc'    → verifikasi_kasi = 1
     * - type = 'revisi' → verifikasi_kasi = 2 + keterangan_revisi_kasi + batas_revisi
     */
    public function submitVerifikasi(Request $request, $id)
    {
        $request->validate([
            'type'         => 'required|in:acc,revisi',
            'keterangan'   => 'required_if:type,revisi|nullable|string|max:255',
            'batas_revisi' => 'required_if:type,revisi|nullable|date|after_or_equal:today',
        ], [
            'type.required'              => 'Pilih jenis verifikasi.',
            'type.in'                    => 'Jenis verifikasi tidak valid.',
            'keterangan.required_if'     => 'Keterangan wajib diisi jika melakukan revisi.',
            'keterangan.max'             => 'Keterangan maksimal 255 karakter.',
            'batas_revisi.required_if'   => 'Batas revisi wajib diisi jika melakukan revisi.',
            'batas_revisi.date'          => 'Batas revisi harus berupa tanggal yang valid.',
            'batas_revisi.after_or_equal'=> 'Batas revisi tidak boleh sebelum hari ini.',
        ]);

        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::where('verifikasi_admin', 1)->findOrFail($id);

            $data = [
                'verifikasi_kasi'    => $request->type === 'acc' ? 1 : 2,
                'verifikasi_kasi_by' => auth()->id(),
                'verifikasi_kasi_at' => now(),
            ];

            if ($request->type === 'revisi') {
                $data['keterangan_revisi_kasi'] = $request->keterangan;
                $data['batas_revisi']           = $request->batas_revisi;
            } else {
                $data['keterangan_revisi_kasi'] = null;
                $data['batas_revisi']           = null;
            }

            $ajuan->update($data);

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => $request->type === 'acc'
                                ? 'Pengajuan berhasil di-ACC oleh Kasi.'
                                : 'Pengajuan dikembalikan untuk revisi oleh Kasi.',
                'redirect' => route('hi.pp.kasibidang.index'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memproses verifikasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Label status verifikasi.
     * 0 menunggu, 1 acc, 2 revisi
     */
    private function labelVerifikasi($val)
    {
        switch ((int) $val) {
            case 1:
                return '<span class="badge bg-success">ACC</span>';
            case 2:
                return '<span class="badge bg-warning">Revisi</span>';
            default:
                return '<span class="badge bg-secondary">Menunggu</span>';
        }
    }
}
