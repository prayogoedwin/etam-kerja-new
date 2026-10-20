<?php

namespace App\Http\Controllers\Pengawasan\K3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\K3\EtamPengawasanK3Ajuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class KabidK3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamPengawasanK3Ajuan::select(
                'id',
                'kategori_id',
                'jenis_id',
                'penyedia_id',
                'nama_alat',
                'lokasi_alat',
                'is_kadis_dispo',
                'is_kabid_dispo',
                'kabid_dispo_at',
                'created_at',
                'dok_unggah_penyedia'
            )
            ->with([
                'kategori:id,nama',
                'jenis:id,nama',
                'penyedia' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->where('is_kadis_dispo', 1); // Kabid hanya melihat yang sudah didispo oleh Kadis

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('perusahaan_nama', function ($data) {
                    return $data->penyedia->name ?? '-';
                })
                ->addColumn('kategori_nama', function ($data) {
                    return $data->kategori->nama ?? '-';
                })
                ->addColumn('jenis_nama', function ($data) {
                    return $data->jenis->nama ?? '-';
                })
                ->addColumn('tanggal_fmt', function ($data) {
                    return $data->created_at ? Carbon::parse($data->created_at)->format('d-m-Y H:i') : '-';
                })
                ->addColumn('status_dispo', function ($data) {
                    if ($data->is_kabid_dispo == 1) {
                        return '<span class="badge bg-success">Sudah Didisposisi ke Kasi</span>';
                    }
                    return '<span class="badge bg-warning text-dark">Menunggu Disposisi Kabid</span>';
                })
                ->addColumn('options', function ($data) {
                    $detailUrl = route('pengawasan.k3.kabid.show', $data->id);
                    return '<a href="' . $detailUrl . '" class="btn btn-info btn-sm"><i class="feather icon-eye"></i> Detail</a>';
                })
                ->rawColumns(['status_dispo', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.k3.kabid.index');
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
    public function show($id)
    {
        $ajuan = EtamPengawasanK3Ajuan::with(['kategori', 'jenis', 'penyedia', 'spt', 'spt.pengawas'])->findOrFail($id);

        return view('backend.pengawasan.k3.kabid.show', compact('ajuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

    public function updateDisposisi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'catatan_disposisi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ajuan = EtamPengawasanK3Ajuan::findOrFail($id);

            // Update status disposisi Kabid dan catatannya
            $ajuan->update([
                'is_kabid_dispo' => 1,
                'kabid_dispo_at' => now(),
                'catatan_disposisi_kabid' => $request->catatan_disposisi,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Disposisi berhasil diteruskan ke Kepala Seksi (Kasi)!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
