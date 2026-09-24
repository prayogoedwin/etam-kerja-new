<?php

namespace App\Http\Controllers\HI\PP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\HI\PP\EtamHiPpAjuan;
use App\Models\HI\PP\EtamHiPpJenisajuan;
use App\Models\HI\PP\EtamHiPpSyaratdokumen;
use App\Models\HI\PP\EtamHiPpDokunggahpenyedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PenyediaPpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
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
            ->where('created_by', auth()->id());

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

                    // <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $data->id . ')">Edit</button>
                    $editUrl = route('hi.pp.penyedia.edit', $data->id);
                    $html = '-';

                    if ((int) $data->verifikasi_admin === 0 && (int) $data->verifikasi_kasi === 0) {
                        $html = '
                        <a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $data->id . ')">Delete</button>
                        ';
                    }

                    $revUrl = route('hi.pp.penyedia.revisi', $data->id);
                    if ((int) $data->verifikasi_admin === 2) {
                        $html = '
                            <a href="' . $revUrl . '" class="btn btn-primary btn-sm">Revisi</a>
                        ';
                    }

                    // return '
                    //     <a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a>
                    //     <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $data->id . ')">Delete</button>
                    // ';

                    // Tombol cetak hanya muncul kalau admin & kasi sudah ACC
                    // if ((int) $data->verifikasi_admin === 1 && (int) $data->verifikasi_kasi === 1) {
                    //     $cetakUrl = route('hi.pp.admbidang.cetak', $data->id);
                    //     $html .= ' <a href="' . $cetakUrl . '" target="_blank" class="btn btn-success btn-sm">
                    //                 <i class="feather icon-printer"></i> Cetak
                    //             </a>';
                    // }

                    return $html;
                })
                ->rawColumns(['options', 'status_admin', 'status_kasi'])
                ->make(true);
        }

        return view('backend.hi.pp.penyedia.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisAjuan     = EtamHiPpJenisajuan::orderBy('id', 'asc')->get();
        $syaratDokumen = EtamHiPpSyaratdokumen::where('id', '!=', 8)->orderBy('id', 'asc')->get();

        return view('backend.hi.pp.penyedia.tambah', compact('jenisAjuan', 'syaratDokumen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'jenis_ajuan' => 'required|exists:etam_hi_pp_jenisajuan,id',
        //     'nomor'       => 'nullable|string|max:255',
        //     'tanggal'     => 'nullable|date',
        // ]);
        $request->validate([
            'jenis_ajuan' => 'required|exists:etam_hi_pp_jenisajuan,id',
            'nomor'       => 'nullable|string|max:255',
            'tanggal'     => 'nullable|date',
            'dokumen'     => 'nullable|array',
            'dokumen.*'   => 'nullable|file|mimes:pdf|max:1024', // max 1024 KB = 1 MB
        ], [
            'dokumen.*.mimes' => 'Dokumen harus berformat PDF.',
            'dokumen.*.max'   => 'Ukuran dokumen maksimal 1 MB.',
            'dokumen.*.file'  => 'Dokumen tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::create([
                'jenis_ajuan'                       => $request->jenis_ajuan,
                'perusahaan_id'                     => auth()->id() ?? 0,
                'surat_keputusan_izin_usaha'        => $request->surat_keputusan_izin_usaha,
                'nomor'                             => $request->nomor,
                'tanggal'                           => $request->tanggal,
                'nama_serikat_pekerja'              => $request->nama_serikat_pekerja,
                'nomor_peserta_bpjs'                => $request->nomor_peserta_bpjs,
                'jumlah_pekerja_pusat'              => $request->jumlah_pekerja_pusat ?? 0,
                'jumlah_pekerja_cabang'             => $request->jumlah_pekerja_cabang ?? 0,
                'upah_pekerja_bulanan_min'          => $request->upah_pekerja_bulanan_min,
                'upah_pekerja_bulanan_max'          => $request->upah_pekerja_bulanan_max,
                'upah_pekerja_harian_min'           => $request->upah_pekerja_harian_min,
                'upah_pekerja_harian_max'           => $request->upah_pekerja_harian_max,
                'sistem_hub_kerja_tertentu'         => $request->sistem_hub_kerja_tertentu ?? 0,
                'sistem_hub_kerja_tidak_tertentu'   => $request->sistem_hub_kerja_tidak_tertentu ?? 0,
                'link_gdrive_dokumen8'              => $request->link_gdrive_dokumen8,
                'verifikasi_admin'                  => 0,
                'verifikasi_kasi'                   => 0,
                'created_by'                        => auth()->id(),
            ]);

            // Upload dokumen dinamis
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $syaratId => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('hi/pp/dokumen', 'public');

                        EtamHiPpDokunggahpenyedia::create([
                            'ajuan_id'          => $ajuan->id,
                            'syaratdokumen_id'  => $syaratId,
                            'path_dokumen'      => $path,
                            'created_by'        => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Pengajuan berhasil dikirim.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
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

    public function editForm($id)
    {
        $ajuan = EtamHiPpAjuan::with([
            'syaratDokumen', // relasi ke EtamHiPpDokunggahpenyedia
        ])->findOrFail($id);

        $jenisAjuan    = EtamHiPpJenisajuan::orderBy('id')->get();
        // $syaratDokumen = EtamHiPpSyaratdokumen::orderBy('id')->get();
        $syaratDokumen = EtamHiPpSyaratdokumen::where('id', '!=', 8)->orderBy('id', 'asc')->get();

        // Map dokumen yang sudah diunggah: [syaratdokumen_id => path_dokumen]
        $uploadedDokumen = $ajuan->syaratDokumen
            ->pluck('path_dokumen', 'syaratdokumen_id')
            ->toArray();

        return view('backend.hi.pp.penyedia.edit', compact(
            'ajuan',
            'jenisAjuan',
            'syaratDokumen',
            'uploadedDokumen'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ajuan = EtamHiPpAjuan::with([
            'jenisAjuan:id,nama',
            'syaratDokumen.syaratDokumen:id,nama',
        ])->findOrFail($id);

        $jenisAjuan    = EtamHiPpJenisajuan::orderBy('id')->get();
        $syaratDokumen = EtamHiPpSyaratdokumen::orderBy('id')->get();

        // Map dokumen yang sudah diunggah: [syaratdokumen_id => path]
        $uploadedDokumen = $ajuan->syaratDokumen
            ->pluck('path_dokumen', 'syaratdokumen_id')
            ->toArray();

        return response()->json([
            'status' => true,
            'data'   => $ajuan,
            'jenis_ajuan_options'    => $jenisAjuan,
            'syarat_dokumen_options' => $syaratDokumen,
            'uploaded_dokumen'       => $uploadedDokumen,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'jenis_ajuan' => 'required|exists:etam_hi_pp_jenisajuan,id',
        //     'nomor'       => 'nullable|string|max:255',
        //     'tanggal'     => 'nullable|date',
        // ]);
        $request->validate([
            'jenis_ajuan' => 'required|exists:etam_hi_pp_jenisajuan,id',
            'nomor'       => 'nullable|string|max:255',
            'tanggal'     => 'nullable|date',
            'dokumen'     => 'nullable|array',
            'dokumen.*'   => 'nullable|file|mimes:pdf|max:1024', // max 1024 KB = 1 MB
        ], [
            'dokumen.*.mimes' => 'Dokumen harus berformat PDF.',
            'dokumen.*.max'   => 'Ukuran dokumen maksimal 1 MB.',
            'dokumen.*.file'  => 'Dokumen tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::findOrFail($id);

            $ajuan->update([
                'jenis_ajuan'                     => $request->jenis_ajuan,
                'surat_keputusan_izin_usaha'      => $request->surat_keputusan_izin_usaha,
                'nomor'                           => $request->nomor,
                'tanggal'                         => $request->tanggal,
                'nama_serikat_pekerja'            => $request->nama_serikat_pekerja,
                'nomor_peserta_bpjs'              => $request->nomor_peserta_bpjs,
                'jumlah_pekerja_pusat'            => $request->jumlah_pekerja_pusat ?? 0,
                'jumlah_pekerja_cabang'           => $request->jumlah_pekerja_cabang ?? 0,
                'upah_pekerja_bulanan_min'        => $request->upah_pekerja_bulanan_min,
                'upah_pekerja_bulanan_max'        => $request->upah_pekerja_bulanan_max,
                'upah_pekerja_harian_min'         => $request->upah_pekerja_harian_min,
                'upah_pekerja_harian_max'         => $request->upah_pekerja_harian_max,
                'sistem_hub_kerja_tertentu'       => $request->sistem_hub_kerja_tertentu ?? 0,
                'sistem_hub_kerja_tidak_tertentu' => $request->sistem_hub_kerja_tidak_tertentu ?? 0,
                'link_gdrive_dokumen8'            => $request->link_gdrive_dokumen8,
            ]);

            // Update file dokumen (kalau ada file baru)
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $syaratId => $file) {
                    if ($file && $file->isValid()) {

                        // Hapus file lama (opsional)
                        $old = EtamHiPpDokunggahpenyedia::where('ajuan_id', $ajuan->id)
                            ->where('syaratdokumen_id', $syaratId)
                            ->first();

                        if ($old && Storage::disk('public')->exists($old->path_dokumen)) {
                            Storage::disk('public')->delete($old->path_dokumen);
                            $old->forceDelete();
                        }

                        $path = $file->store('hi/pp/dokumen', 'public');

                        EtamHiPpDokunggahpenyedia::create([
                            'ajuan_id'         => $ajuan->id,
                            'syaratdokumen_id' => $syaratId,
                            'path_dokumen'     => $path,
                            'created_by'       => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Pengajuan berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function revisiForm($id)
    {
        $ajuan = EtamHiPpAjuan::with([
            'syaratDokumen', // relasi ke EtamHiPpDokunggahpenyedia
        ])->findOrFail($id);

        $jenisAjuan    = EtamHiPpJenisajuan::orderBy('id')->get();
        // $syaratDokumen = EtamHiPpSyaratdokumen::orderBy('id')->get();
        $syaratDokumen = EtamHiPpSyaratdokumen::where('id', '!=', 8)->orderBy('id', 'asc')->get();

        // Map dokumen yang sudah diunggah: [syaratdokumen_id => path_dokumen]
        $uploadedDokumen = $ajuan->syaratDokumen
            ->pluck('path_dokumen', 'syaratdokumen_id')
            ->toArray();

        return view('backend.hi.pp.penyedia.revisi', compact(
            'ajuan',
            'jenisAjuan',
            'syaratDokumen',
            'uploadedDokumen'
        ));
    }

    public function updateRevisi(Request $request, $id)
    {
        $request->validate([
            'jenis_ajuan' => 'required|exists:etam_hi_pp_jenisajuan,id',
            'nomor'       => 'nullable|string|max:255',
            'tanggal'     => 'nullable|date',
            'dokumen'     => 'nullable|array',
            'dokumen.*'   => 'nullable|file|mimes:pdf|max:1024', // max 1024 KB = 1 MB
        ], [
            'dokumen.*.mimes' => 'Dokumen harus berformat PDF.',
            'dokumen.*.max'   => 'Ukuran dokumen maksimal 1 MB.',
            'dokumen.*.file'  => 'Dokumen tidak valid.',
        ]);


        $ajuan = EtamHiPpAjuan::findOrFail($id);
        // ============ CEK BATAS REVISI ============
        if (!empty($ajuan->batas_revisi)) {
            $batasRevisi = \Carbon\Carbon::parse($ajuan->batas_revisi)->startOfDay();
            $hariIni     = \Carbon\Carbon::today();

            // Kalau hari ini SUDAH LEWAT dari batas revisi → tolak
            if ($hariIni->gt($batasRevisi)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Batas waktu revisi telah berakhir pada '
                                . $batasRevisi->format('d-m-Y')
                                . '. Anda tidak dapat mengirim revisi.',
                ]);
            }
        }
        // ==========================================

        DB::beginTransaction();
        try {

            $ajuan->update([
                'jenis_ajuan'                     => $request->jenis_ajuan,
                'surat_keputusan_izin_usaha'      => $request->surat_keputusan_izin_usaha,
                'nomor'                           => $request->nomor,
                'tanggal'                         => $request->tanggal,
                'nama_serikat_pekerja'            => $request->nama_serikat_pekerja,
                'nomor_peserta_bpjs'              => $request->nomor_peserta_bpjs,
                'jumlah_pekerja_pusat'            => $request->jumlah_pekerja_pusat ?? 0,
                'jumlah_pekerja_cabang'           => $request->jumlah_pekerja_cabang ?? 0,
                'upah_pekerja_bulanan_min'        => $request->upah_pekerja_bulanan_min,
                'upah_pekerja_bulanan_max'        => $request->upah_pekerja_bulanan_max,
                'upah_pekerja_harian_min'         => $request->upah_pekerja_harian_min,
                'upah_pekerja_harian_max'         => $request->upah_pekerja_harian_max,
                'sistem_hub_kerja_tertentu'       => $request->sistem_hub_kerja_tertentu ?? 0,
                'sistem_hub_kerja_tidak_tertentu' => $request->sistem_hub_kerja_tidak_tertentu ?? 0,
                'link_gdrive_dokumen8'            => $request->link_gdrive_dokumen8,
                'verifikasi_admin'                => 0,
                // 'verifikasi_kasi'                => 0,
                // 'keterangan_revisi_admin'         => null, // reset keterangan lama (opsional)
                // 'batas_revisi'                    => null, // reset batas revisi (opsional)
            ]);

            // Update file dokumen (kalau ada file baru)
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $syaratId => $file) {
                    if ($file && $file->isValid()) {

                        // Hapus file lama (opsional)
                        $old = EtamHiPpDokunggahpenyedia::where('ajuan_id', $ajuan->id)
                            ->where('syaratdokumen_id', $syaratId)
                            ->first();

                        if ($old && Storage::disk('public')->exists($old->path_dokumen)) {
                            Storage::disk('public')->delete($old->path_dokumen);
                            $old->forceDelete();
                        }

                        $path = $file->store('hi/pp/dokumen', 'public');

                        EtamHiPpDokunggahpenyedia::create([
                            'ajuan_id'         => $ajuan->id,
                            'syaratdokumen_id' => $syaratId,
                            'path_dokumen'     => $path,
                            'created_by'       => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Pengajuan berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::findOrFail($id);

            // Soft delete ajuan
            $ajuan->deleted_by = auth()->id();
            $ajuan->save();
            $ajuan->delete(); // soft delete

            // Soft delete dokumen terkait (opsional)
            EtamHiPpDokunggahpenyedia::where('ajuan_id', $ajuan->id)->delete();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Pengajuan berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper label untuk kolom verifikasi.
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
