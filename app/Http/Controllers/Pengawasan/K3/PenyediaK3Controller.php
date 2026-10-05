<?php

namespace App\Http\Controllers\Pengawasan\K3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\K3\EtamPengawasanK3Ajuan;
use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PenyediaK3Controller extends Controller
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
                'kapasitas_alat',
                'jumlah_unit',
                'is_kadis_dispo',
                'is_kabid_dispo',
                'is_kasi_dispo',
                'created_at',
                'dok_unggah_penyedia'
            )
            ->with(['kategori:id,nama', 'jenis:id,nama'])
            ->where('penyedia_id', auth()->id()); // Menampilkan data ajuan milik penyedia yang sedang login

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('kategori_nama', function ($data) {
                    return $data->kategori->nama ?? '-';
                })
                ->addColumn('jenis_nama', function ($data) {
                    return $data->jenis->nama ?? '-';
                })
                ->addColumn('tanggal_fmt', function ($data) {
                    return $data->created_at
                        ? Carbon::parse($data->created_at)->format('d-m-Y H:i')
                        : '-';
                })
                ->addColumn('status_disposisi', function ($data) {
                    // Contoh badge status disposisi berjenjang
                    $kadis = $data->is_kadis_dispo ? '<span class="badge bg-success">Kadis (Dispo)</span>' : '<span class="badge bg-secondary">Kadis (Menunggu)</span>';
                    $kabid = $data->is_kabid_dispo ? '<span class="badge bg-success">Kabid (Dispo)</span>' : '<span class="badge bg-secondary">Kabid (Menunggu)</span>';
                    $kasi = $data->is_kasi_dispo ? '<span class="badge bg-success">Kasi (Dispo)</span>' : '<span class="badge bg-secondary">Kasi (Menunggu)</span>';

                    return '<div class="d-flex gap-1">'.$kadis.' '.$kabid.' '.$kasi.'</div>';
                })
                ->addColumn('options', function ($data) {
                    $html = '-';

                    if ((int) $data->is_kasi_dispo === 0 && (int) $data->is_kabid_dispo === 0 && (int) $data->is_kadis_dispo === 0) {
                        $html = '
                        <button class="btn btn-primary btn-sm" onclick="editData(' . $data->id . ')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $data->id . ')">Delete</button>
                        ';
                    } else {
                        $html = '<span class="text-muted small">Sedang diproses</span>';
                    }

                    if (!empty($data->dok_unggah_penyedia)) {
                        $html .= '
                        <a href="'. asset('storage/' . $data->dok_unggah_penyedia) .'" target="_blank" class="btn btn-sm btn-info ms-1">
                            <i class="feather icon-download"></i> Dokumen
                        </a>
                        ';
                    }

                    return $html;
                })
                ->rawColumns(['options', 'status_disposisi'])
                ->make(true);
        }

        return view('backend.pengawasan.k3.penyedia.index');
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
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:etam_pengawasan_k3_kategori,id',
            'jenis_id' => 'required|exists:etam_pengawasan_k3_jenis,id',
            'nama_alat' => 'required|string|max:255',
            'lokasi_alat' => 'required|string|max:255',
            'kapasitas_alat' => 'required|string|max:255',
            'jumlah_unit' => 'required|integer|min:1',
            'dok_unggah_penyedia' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Maks 2MB
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa kembali form.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $filePath = null;
            if ($request->hasFile('dok_unggah_penyedia')) {
                $file = $request->file('dok_unggah_penyedia');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/k3/permohonan', $filename, 'public');
            }

            EtamPengawasanK3Ajuan::create([
                'penyedia_id' => auth()->id(),
                'kategori_id' => $request->kategori_id,
                'jenis_id' => $request->jenis_id,
                'nama_alat' => $request->nama_alat,
                'lokasi_alat' => $request->lokasi_alat,
                'kapasitas_alat' => $request->kapasitas_alat,
                'jumlah_unit' => $request->jumlah_unit,
                'keterangan' => $request->keterangan,
                'dok_unggah_penyedia' => $filePath,
                'is_kadis_dispo' => 0,
                'is_kabid_dispo' => 0,
                'is_kasi_dispo' => 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengajuan berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
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
    public function edit($id)
    {
        $data = EtamPengawasanK3Ajuan::where('penyedia_id', auth()->id())->findOrFail($id);

        // Cek disposisi
        if ($data->is_kadis_dispo == 1 || $data->is_kabid_dispo == 1 || $data->is_kasi_dispo == 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang sudah didisposisi tidak dapat diubah.'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:etam_pengawasan_k3_kategori,id',
            'jenis_id' => 'required|exists:etam_pengawasan_k3_jenis,id',
            'nama_alat' => 'required|string|max:255',
            'lokasi_alat' => 'required|string|max:255',
            'kapasitas_alat' => 'required|string|max:255',
            'jumlah_unit' => 'required|integer|min:1',
            'dok_unggah_penyedia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa kembali form.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ajuan = EtamPengawasanK3Ajuan::where('penyedia_id', auth()->id())->findOrFail($id);

            if ($ajuan->is_kadis_dispo == 1 || $ajuan->is_kabid_dispo == 1 || $ajuan->is_kasi_dispo == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data yang sudah didisposisi tidak dapat diubah!'
                ], 403);
            }

            $filePath = $ajuan->dok_unggah_penyedia;
            if ($request->hasFile('dok_unggah_penyedia')) {
                // Hapus file lama jika ada
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $file = $request->file('dok_unggah_penyedia');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/k3/permohonan', $filename, 'public');
            }

            $ajuan->update([
                'kategori_id' => $request->kategori_id,
                'jenis_id' => $request->jenis_id,
                'nama_alat' => $request->nama_alat,
                'lokasi_alat' => $request->lokasi_alat,
                'kapasitas_alat' => $request->kapasitas_alat,
                'jumlah_unit' => $request->jumlah_unit,
                'keterangan' => $request->keterangan,
                'dok_unggah_penyedia' => $filePath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengajuan berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = EtamPengawasanK3Ajuan::where('penyedia_id', auth()->id())->findOrFail($id);

            // Cek apakah sudah didisposisi
            if ($data->is_kadis_dispo == 1 || $data->is_kabid_dispo == 1 || $data->is_kasi_dispo == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data yang sudah didisposisi tidak dapat dihapus!'
                ], 403);
            }

            // Hapus file dokumen fisik jika ada
            if ($data->dok_unggah_penyedia && Storage::disk('public')->exists($data->dok_unggah_penyedia)) {
                Storage::disk('public')->delete($data->dok_unggah_penyedia);
            }

            // Catat deleted_by jika kolomnya ada
            $data->deleted_by = auth()->id();
            $data->save();

            // Lakukan soft delete / delete
            $data->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengajuan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mengambil jenis alat berdasarkan kategori (untuk AJAX dropdown dependent)
    public function getJenisByKategori($kategori_id)
    {
        $jenis = EtamPengawasanK3Jenis::where('kategori_id', $kategori_id)->get(['id', 'nama']);
        return response()->json($jenis);
    }

    // Helper untuk label status jika diperlukan
    private function labelVerifikasi($status)
    {
        return $status == 1 ? '<span class="badge bg-success">Disetujui</span>' : '<span class="badge bg-warning">Menunggu</span>';
    }
}
