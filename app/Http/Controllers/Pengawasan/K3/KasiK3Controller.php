<?php

namespace App\Http\Controllers\Pengawasan\K3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\K3\EtamPengawasanK3Ajuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class KasiK3Controller extends Controller
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
                'is_kabid_dispo',
                'is_kasi_dispo',
                'kasi_dispo_at',
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
            ->where('is_kabid_dispo', 1); // Kasi melihat data yang sudah didisposisi oleh Kabid

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
                    if ($data->is_kasi_dispo == 1) {
                        return '<span class="badge bg-success">Sudah Didisposisi ke Pengawas</span>';
                    }
                    return '<span class="badge bg-warning text-dark">Menunggu Disposisi Kasi</span>';
                })
                ->addColumn('options', function ($data) {
                    $detailUrl = route('pengawasan.k3.kasi.show', $data->id);
                    return '<a href="' . $detailUrl . '" class="btn btn-info btn-sm"><i class="feather icon-eye"></i> Detail</a>';
                })
                ->rawColumns(['status_dispo', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.k3.kasi.index');
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

        // Mengambil daftar user dengan kriteria:
        // 1. deleted_at is null (otomatis di-handle oleh trait SoftDeletes jika ada, atau filter manual)
        // 2. kode_struktur = '21'
        // 3. Memiliki role dengan id = 18 (petugas-bidang) menggunakan Spatie Permission
        $daftarPengawas = User::where('kode_struktur', '21')
            ->whereNull('deleted_at')
            ->whereHas('roles', function ($query) {
                $query->where('id', 18);
            })
            ->get();

        return view('backend.pengawasan.k3.kasi.show', compact('ajuan', 'daftarPengawas'));
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
            'pengawas_id' => 'required|exists:users,id',
            'catatan_disposisi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan pilih Pengawas yang ditunjuk.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ajuan = EtamPengawasanK3Ajuan::findOrFail($id);

            $ajuan->update([
                'is_kasi_dispo' => 1,
                'kasi_dispo_at' => now(),
                'catatan_disposisi_kasi' => $request->catatan_disposisi,
                'pengawas_id' => $request->pengawas_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Disposisi dan penunjukan Pengawas berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
