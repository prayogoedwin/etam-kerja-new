<?php

namespace App\Http\Controllers\HI\PP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\HI\PP\EtamHiPpAjuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\HI\PP\EtamHiPpDokunggahpenyedia;
use App\Models\HI\PP\EtamHiPpSyaratdokumen;

class AdmBidangPpController extends Controller
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
                ->orderBy('created_at', 'desc');

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
                    $detailUrl = route('hi.pp.admbidang.detail', $data->id);
                    $html = '<a href="' . $detailUrl . '" class="btn btn-info btn-sm">Detail</a>';

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

        return view('backend.hi.pp.admbidang.index');
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

    public function detail($id)
    {
        $ajuan = EtamHiPpAjuan::with([
            'jenisAjuan:id,nama',
            'syaratDokumen.syaratDokumen:id,nama',
            'perusahaan:id,name,email',      // data user (perusahaan)
            'profilPenyedia',                 // data profil penyedia (users_penyedia)
        ])->findOrFail($id);

        $syaratDokumen = EtamHiPpSyaratdokumen::where('id', '!=', 8)
            ->orderBy('id', 'asc')
            ->get();

        $uploadedDokumen = $ajuan->syaratDokumen
            ->pluck('path_dokumen', 'syaratdokumen_id')
            ->toArray();

        return view('backend.hi.pp.admbidang.detail', compact(
            'ajuan',
            'syaratDokumen',
            'uploadedDokumen'
        ));
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

    public function formVerifikasi($id)
    {
        $ajuan = EtamHiPpAjuan::with(['jenisAjuan:id,nama'])->findOrFail($id);

        return view('backend.hi.pp.admbidang.verifikasi', compact('ajuan'));
    }

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
            $ajuan = EtamHiPpAjuan::findOrFail($id);

            $data = [
                'verifikasi_admin'    => $request->type === 'acc' ? 1 : 2,
                'verifikasi_admin_by' => auth()->id(),
                'verifikasi_admin_at' => now(),
            ];

            if ($request->type === 'revisi') {
                $data['keterangan_revisi_admin'] = $request->keterangan;
                $data['batas_revisi']            = $request->batas_revisi;
            } else {
                // Kalau ACC, kosongkan keterangan & batas revisi
                $data['keterangan_revisi_admin'] = null;
                $data['batas_revisi']            = null;
            }

            $ajuan->update($data);

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => $request->type === 'acc'
                                ? 'Pengajuan berhasil di-ACC.'
                                : 'Pengajuan dikembalikan untuk revisi.',
                'redirect' => route('hi.pp.admbidang.index'),
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
     * Halaman cetak pengajuan (full HTML sederhana).
     * Hanya bisa diakses kalau sudah ACC admin & kasi.
     */
    public function cetak($id)
    {
        $ajuan = EtamHiPpAjuan::with([
            'jenisAjuan:id,nama',
            'syaratDokumen.syaratDokumen:id,nama',
        ])
        ->where('verifikasi_admin', 1)
        ->where('verifikasi_kasi', 1)
        ->findOrFail($id);

        $syaratDokumen = EtamHiPpSyaratdokumen::where('id', '!=', 8)
            ->orderBy('id', 'asc')
            ->get();

        $uploadedDokumen = $ajuan->syaratDokumen
            ->pluck('path_dokumen', 'syaratdokumen_id')
            ->toArray();

        // return view('backend.hi.pp.admbidang.cetak', compact(
        //     'ajuan',
        //     'syaratDokumen',
        //     'uploadedDokumen'
        // ));

        return view('backend.hi.pp.admbidang.cetak_sk', compact(
            'ajuan',
            'syaratDokumen',
            'uploadedDokumen'
        ));
    }

    /**
    * Form input SK — hanya bisa diakses kalau admin & kasi sudah ACC.
    */
    public function formSk($id)
    {
        $ajuan = EtamHiPpAjuan::with(['jenisAjuan:id,nama'])
            ->where('verifikasi_admin', 1)
            ->where('verifikasi_kasi', 1)
            ->findOrFail($id);

        return view('backend.hi.pp.admbidang.form_sk', compact('ajuan'));
    }

    /**
    * Simpan nomor_sk & tanggal_berlaku_pp_baru.
    */
    public function submitSk(Request $request, $id)
    {
        $request->validate([
            'nomor_sk'                => 'required|string|max:255',
            'tanggal_berlaku_pp_baru' => 'required|date',
        ], [
            'nomor_sk.required'                => 'Nomor SK wajib diisi.',
            'nomor_sk.max'                     => 'Nomor SK maksimal 255 karakter.',
            'tanggal_berlaku_pp_baru.required' => 'Tanggal berlaku PP baru wajib diisi.',
            'tanggal_berlaku_pp_baru.date'     => 'Tanggal berlaku harus berupa tanggal yang valid.',
        ]);

        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::where('verifikasi_admin', 1)
                ->where('verifikasi_kasi', 1)
                ->findOrFail($id);

            $ajuan->update([
                'nomor_sk'                => $request->nomor_sk,
                'tanggal_berlaku_pp_baru' => $request->tanggal_berlaku_pp_baru,
            ]);

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Nomor SK & tanggal berlaku berhasil disimpan.',
                'redirect' => route('hi.pp.admbidang.cetakSk', $ajuan->id),
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
    * Preview cetak SK (HTML).
    */
    public function cetakSk($id)
    {
        $ajuan = EtamHiPpAjuan::with(['jenisAjuan:id,nama', 'perusahaan.penyedia'])
            ->where('verifikasi_admin', 1)
            ->where('verifikasi_kasi', 1)
            ->findOrFail($id);

        // return view('backend.hi.pp.admbidang.cetak_sk', compact('ajuan'));

        // ====== Hitung tanggal berakhir (asumsi 2 tahun dari tanggal_berlaku_pp_baru) ======
        $tanggalMulai = $ajuan->tanggal_berlaku_pp_baru
            ? \Carbon\Carbon::parse($ajuan->tanggal_berlaku_pp_baru)
            : null;

        $tanggalBerakhir = $tanggalMulai
            ? $tanggalMulai->copy()->addYears(2)->subDay()
            : null;

        // Range tahun untuk judul ("2023-2025")
        $rangeTahun = ($tanggalMulai && $tanggalBerakhir)
            ? $tanggalMulai->format('Y') . '-' . $tanggalBerakhir->format('Y')
            : now()->format('Y');

        // echo json_encode($ajuan);

        return view('backend.hi.pp.admbidang.cetak_sk', compact(
            'ajuan',
            'tanggalMulai',
            'tanggalBerakhir',
            'rangeTahun'
        ));
    }

    /**
    * Form unggah dokumen SK final.
    */
    public function formUnggahSk($id)
    {
        $ajuan = EtamHiPpAjuan::with(['jenisAjuan:id,nama'])
            ->where('verifikasi_admin', 1)
            ->where('verifikasi_kasi', 1)
            ->findOrFail($id);

        return view('backend.hi.pp.admbidang.unggah_sk', compact('ajuan'));
    }

    /**
    * Simpan dokumen SK final ke kolom dok_produk_akhir.
    */
    public function submitUnggahSk(Request $request, $id)
    {
        $request->validate([
            'dok_produk_akhir' => 'required|file|mimes:pdf|max:5120', // max 5 MB
        ], [
            'dok_produk_akhir.required' => 'Dokumen SK wajib diunggah.',
            'dok_produk_akhir.mimes'    => 'Dokumen harus berformat PDF.',
            'dok_produk_akhir.max'      => 'Ukuran dokumen maksimal 5 MB.',
        ]);

        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::where('verifikasi_admin', 1)
                ->where('verifikasi_kasi', 1)
                ->findOrFail($id);

            if ($request->hasFile('dok_produk_akhir')) {
                // Hapus file lama kalau ada
                if ($ajuan->dok_produk_akhir && Storage::disk('public')->exists($ajuan->dok_produk_akhir)) {
                    Storage::disk('public')->delete($ajuan->dok_produk_akhir);
                }

                $path = $request->file('dok_produk_akhir')->store('hi/pp/sk', 'public');
                $ajuan->update(['dok_produk_akhir' => $path]);
            }

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Dokumen SK berhasil diunggah.',
                'redirect' => route('hi.pp.admbidang.detail', $ajuan->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengunggah: ' . $e->getMessage(),
            ], 500);
        }
    }

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
