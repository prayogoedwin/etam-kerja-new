<?php

namespace App\Http\Controllers\Pengawasan\K3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\K3\EtamPengawasanK3Ajuan;
use App\Models\Pengawasan\K3\EtamPengawasanK3Spt;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AdminBidangK3Controller extends Controller
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
                'is_kasi_dispo',
                'pengawas_id',
                'created_at'
            )
            ->with([
                'kategori:id,nama',
                'jenis:id,nama',
                'penyedia:id,name',
                'pengawas:id,name',
                'spt' // Relasi ke tabel SPT untuk mengecek apakah SPT sudah dibuat
            ])
            ->where('is_kasi_dispo', 1); // Admin Bidang memproses data yang sudah didisposisi Kasi

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('perusahaan_nama', function ($data) {
                    return $data->penyedia->name ?? '-';
                })
                ->addColumn('pengawas_nama', function ($data) {
                    return $data->pengawas->name ?? '<span class="text-danger">Belum dipilih Kasi</span>';
                })
                ->addColumn('status_spt', function ($data) {
                    // Cek apakah SPT sudah dibuat (relasi spt ada isinya)
                    if ($data->spt()->exists()) {
                        return '<span class="badge bg-success">SPT Sudah Dibuat</span>';
                    }
                    return '<span class="badge bg-warning text-dark">Belum Ada SPT</span>';
                })
                ->addColumn('options', function ($data) {
                    $buatSptUrl = route('pengawasan.k3.admin_bidang.create_spt', $data->id);
                    return '<a href="' . $buatSptUrl . '" class="btn btn-primary btn-sm"><i class="feather icon-file-text"></i> Buat / Kelola SPT</a>';
                })
                ->rawColumns(['pengawas_nama', 'status_spt', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.k3.admin_bidang.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function createSpt($id)
    {
        $ajuan = EtamPengawasanK3Ajuan::with(['kategori', 'jenis', 'penyedia', 'pengawas', 'spt'])->findOrFail($id);

        return view('backend.pengawasan.k3.admin_bidang.create_spt', compact('ajuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function storeSpt(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomor_spt' => 'required|string|max:255',
            'tanggal_tugas' => 'required|date',
            'lokasi' => 'required|string',
            'uraian_tugas' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, mohon lengkapi form SPT.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ajuan = EtamPengawasanK3Ajuan::findOrFail($id);

            // Pastikan pengawas sudah dipilih pada tahap Kasi
            if (!$ajuan->pengawas_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengawas belum ditentukan oleh Kasi!'
                ], 400);
            }

            // Simpan atau update data SPT
            EtamPengawasanK3Spt::updateOrCreate(
                ['ajuan_id' => $ajuan->id],
                [
                    'pengawas_id' => $ajuan->pengawas_id,
                    'nomor_spt' => $request->nomor_spt,
                    'tanggal_tugas' => $request->tanggal_tugas,
                    'lokasi' => $request->lokasi,
                    'uraian_tugas' => $request->uraian_tugas,
                    'created_by' => auth()->id(),
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Surat Perintah Tugas (SPT) berhasil diterbitkan oleh Admin Bidang!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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
}
