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
use App\Services\HI\PP\PpLogService;
use App\Models\HI\PP\EtamHiPpLog;
use App\Models\HI\PP\EtamHiPpJenisajuan;
use App\Services\Tte\TteQueue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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
            // 'logs.user:id,name',
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
            // 'keterangan'   => 'required_if:type,revisi|nullable|string|max:255',
            'keterangan'   => 'required_if:type,revisi|nullable|string',
            // 'batas_revisi' => 'required_if:type,revisi|nullable|date|after_or_equal:today',
            'dok_revisi_admin'       => 'nullable|file|mimes:pdf|max:1024', // max 1 MB
        ], [
            'type.required'              => 'Pilih jenis verifikasi.',
            'type.in'                    => 'Jenis verifikasi tidak valid.',
            'keterangan.required_if'     => 'Keterangan wajib diisi jika melakukan revisi.',
            // 'keterangan.max'             => 'Keterangan maksimal 255 karakter.',
            // 'batas_revisi.required_if'   => 'Batas revisi wajib diisi jika melakukan revisi.',
            // 'batas_revisi.date'          => 'Batas revisi harus berupa tanggal yang valid.',
            // 'batas_revisi.after_or_equal'=> 'Batas revisi tidak boleh sebelum hari ini.',
            'dok_revisi_admin.mimes'     => 'Dokumen revisi harus berformat PDF.',
            'dok_revisi_admin.max'       => 'Ukuran dokumen revisi maksimal 1 MB.',
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
                // $data['batas_revisi']            = $request->batas_revisi;
                // Upload dok revisi admin (opsional)
                if ($request->hasFile('dok_revisi_admin')) {
                    // Hapus file lama kalau ada
                    if ($ajuan->dok_revisi_admin && Storage::disk('public')->exists($ajuan->dok_revisi_admin)) {
                        Storage::disk('public')->delete($ajuan->dok_revisi_admin);
                    }

                    // $path = $request->file('dok_revisi_admin')->store('hi/pp/revisi/admin', 'public');
                    $path = $request->file('dok_revisi_admin')->store(upload_path_pp('revisi'), 'public');
                    $data['dok_revisi_admin'] = $path;
                }

                PpLogService::log(
                    $ajuan->id,
                    EtamHiPpLog::ACTION_ADMIN_REVISI,
                    'Admin Bidang mengembalikan pengajuan untuk revisi',
                    [
                        'keterangan' => $request->keterangan,
                        'dok_revisi' => $data['dok_revisi_admin'] ?? null,
                    ]
                );
            } else {
                // Kalau ACC, kosongkan keterangan & batas revisi
                $data['keterangan_revisi_admin'] = null;
                $data['batas_revisi']            = null;
                $data['dok_revisi_admin']        = null;

                PpLogService::log(
                    $ajuan->id,
                    EtamHiPpLog::ACTION_ADMIN_ACC,
                    'Admin Bidang menyetujui pengajuan',
                    []
                );
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

        $generatedPath = null;
        $tteSourcePath = null;

        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::where('verifikasi_admin', 1)
                ->where('verifikasi_kasi', 1)
                ->findOrFail($id);

            $ajuan->update([
                'nomor_sk'                => $request->nomor_sk,
                'tanggal_berlaku_pp_baru' => $request->tanggal_berlaku_pp_baru,
            ]);

            $ajuan->load(['jenisAjuan:id,nama', 'perusahaan.penyedia']);
            $viewData = $this->dataCetakSk($ajuan);
            $pdfBytes = Pdf::loadView('backend.hi.pp.admbidang.cetak_sk', $viewData)
                ->setPaper('a4', 'portrait')
                ->setOption('defaultMediaType', 'print')
                ->setOption('dpi', 96)
                ->setOption('isRemoteEnabled', false)
                ->setOption('isHtml5ParserEnabled', true)
                ->output();

            $directory = upload_path_pp('sk', now());
            $generatedPath = $directory.'/sk-'.$ajuan->id.'-'.Str::uuid().'.pdf';
            if (! Storage::disk('public')->put($generatedPath, $pdfBytes)) {
                throw new \RuntimeException('PDF SK gagal disimpan.');
            }

            // TTE membaca berkas dari disk local, sedangkan dok_produk_akhir
            // memakai disk public agar dapat ditampilkan pada halaman PP.
            $tteSourcePath = 'tte/before/hi-pp/'.$ajuan->id.'/'.basename($generatedPath);
            if (! Storage::disk('local')->put($tteSourcePath, $pdfBytes)) {
                throw new \RuntimeException('Salinan PDF untuk antrean TTE gagal disimpan.');
            }

            // $ajuan->update([
            //     'dok_produk_akhir' => $generatedPath,
            //     'status_ttd' => 0,
            // ]);

            $tte = app(TteQueue::class)->ajukan([
                'nama_modul' => 'hi_pp',
                'nama_table' => 'etam_hi_pp_ajuan',
                'id_refrensi' => $ajuan->id,
                'kolom_status_refrensi' => 'status_ttd',
                'kode_dokumen' => 'suket_hi_pp',
                'id_struktur' => NULL,
                'judul' => 'Surat Pengesahan Peraturan Perusahaan - '.($ajuan->perusahaan?->penyedia?->name ?? '-'),
                'path_file_before' => $tteSourcePath,
            ]);
            if ($tte->path_file_before !== $tteSourcePath) {
                $tte->path_file_before = $tteSourcePath;
                $tte->save();
            }

            PpLogService::log(
                $ajuan->id,
                EtamHiPpLog::ACTION_INPUT_SK,
                'Admin Bidang menginput nomor SK, membuat PDF, dan mengajukan TTE',
                [
                    'nomor_sk' => $request->nomor_sk,
                    'tanggal_berlaku_pp_baru' => $request->tanggal_berlaku_pp_baru,
                    'path' => $generatedPath,
                    'tte_id' => $tte->id,
                ]
            );

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Nomor SK berhasil disimpan, PDF dibuat, dan dokumen masuk antrean TTE.',
                'redirect' => route('hi.pp.admbidang.cetakSk', $ajuan->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($generatedPath) {
                Storage::disk('public')->delete($generatedPath);
            }
            if ($tteSourcePath) {
                Storage::disk('local')->delete($tteSourcePath);
            }

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

        return view('backend.hi.pp.admbidang.cetak_sk', $this->dataCetakSk($ajuan));
    }

    /** Data bersama untuk preview HTML dan pembuatan PDF SK. */
    private function dataCetakSk(EtamHiPpAjuan $ajuan): array
    {
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

        return compact(
            'ajuan',
            'tanggalMulai',
            'tanggalBerakhir',
            'rangeTahun'
        ) + [
            'logoDataUri' => $this->logoDataUri(),
            'bsreLogoDataUri' => $this->bsreLogoDataUri(),
            'bsreFooterText' => "Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik\nyang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).",
        ];
    }

    private function logoDataUri(): ?string
    {
        $logoPath = public_path('assets/etam_fe/images/logo/logo_kaltim.png');
        if (! is_file($logoPath)) {
            return null;
        }

        $mime = mime_content_type($logoPath) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($logoPath));
    }

    private function bsreLogoDataUri(): ?string
    {
        $logoPath = public_path('assets/etam_be/images/logo/logo_bsre.png');
        if (! is_file($logoPath)) {
            return null;
        }

        $mime = mime_content_type($logoPath) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($logoPath));
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
        // ===== DEBUG SEMENTARA =====
        // \Log::info('Upload debug', [
        //     'hasFile'            => $request->hasFile('dok_produk_akhir'),
        //     'files_raw'          => $_FILES['dok_produk_akhir'] ?? null,
        //     'php_upload_limit'   => ini_get('upload_max_filesize'),
        //     'php_post_limit'     => ini_get('post_max_size'),
        //     'php_memory_limit'   => ini_get('memory_limit'),
        //     'post_content_length'=> $request->server('CONTENT_LENGTH'),
        //     'error_code'         => $request->file('dok_produk_akhir')?->getError(),
        // ]);
        // ==========================

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

                // $path = $request->file('dok_produk_akhir')->store('hi/pp/sk', 'public');
                $path = $request->file('dok_produk_akhir')->store(upload_path_pp('sk'), 'public');
                $ajuan->update(['dok_produk_akhir' => $path]);
            }

            DB::commit();

            PpLogService::log(
                $ajuan->id,
                EtamHiPpLog::ACTION_UPLOAD_SK_FINAL,
                'Admin Bidang mengunggah dokumen SK final',
                [
                    'path' => $path,
                ]
            );

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

    public function formFinalKonsep($id)
    {
        $ajuan = EtamHiPpAjuan::with(['jenisAjuan:id,nama'])
            ->where('verifikasi_admin', 1)
            ->where('verifikasi_kasi', 1)
            ->findOrFail($id);

        return view('backend.hi.pp.admbidang.final_konsep', compact('ajuan'));
    }

    public function submitFinalKonsep(Request $request, $id)
    {
        $request->validate([
            'dok_final_konsep_pp' => 'required|file|mimes:pdf|max:10240', // max 10 MB
        ], [
            'dok_final_konsep_pp.required' => 'Dokumen final konsep PP wajib diunggah.',
            'dok_final_konsep_pp.mimes'    => 'Dokumen harus berformat PDF.',
            'dok_final_konsep_pp.max'      => 'Ukuran dokumen maksimal 10 MB.',
        ]);

        DB::beginTransaction();
        try {
            $ajuan = EtamHiPpAjuan::where('verifikasi_admin', 1)
                ->where('verifikasi_kasi', 1)
                ->findOrFail($id);

            if ($request->hasFile('dok_final_konsep_pp')) {
                // Hapus file lama kalau ada
                if ($ajuan->dok_final_konsep_pp && Storage::disk('public')->exists($ajuan->dok_final_konsep_pp)) {
                    Storage::disk('public')->delete($ajuan->dok_final_konsep_pp);
                }

                $path = $request->file('dok_final_konsep_pp')->store(upload_path_pp('final-konsep'), 'public');

                $ajuan->update([
                    'dok_final_konsep_pp' => $path,
                    'verifikasi_admin'    => 8,
                    'verifikasi_kasi'     => 8,
                ]);
            }

            DB::commit();

            PpLogService::log(
                $ajuan->id,
                EtamHiPpLog::ACTION_UPLOAD_FINAL_PP,
                'Admin Bidang mengunggah dokumen final konsep PP — Status selesai',
                [
                    'path' => $path,
                ]
            );

            return response()->json([
                'status'   => true,
                'message'  => 'Dokumen final konsep PP berhasil diunggah. Status: Selesai.',
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

    public function expired(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamHiPpAjuan::select(
                    'id',
                    'jenis_ajuan',
                    'perusahaan_id',
                    'nomor',
                    'tanggal_berlaku_pp_baru',
                    'nomor_sk',
                    'created_at'
                )
                ->with([
                    'jenisAjuan:id,nama',
                    'perusahaan:id,name',
                    'perusahaan.penyedia:user_id,name',
                ])
                ->whereNotNull('tanggal_berlaku_pp_baru');

            // Filter default: yang expired dalam 6 bulan ke depan (termasuk yang sudah expired)
            $rangeBulan = (int) ($request->range_bulan ?? 6);
            $batasAkhir = now()->addMonths($rangeBulan)->endOfDay();

            $datas->where('tanggal_berlaku_pp_baru', '<=', $batasAkhir->format('Y-m-d'));

            // Filter opsional: hanya yang sudah expired
            if ($request->filter === 'expired') {
                $datas->whereRaw("
                    DATE_ADD(tanggal_berlaku_pp_baru, INTERVAL
                        CASE jenis_ajuan
                            WHEN 2 THEN 2
                            WHEN 4 THEN 2
                            ELSE 1
                        END YEAR
                    ) <= CURDATE()
                ");
            }

            // Filter opsional: yang akan expired (belum expired)
            if ($request->filter === 'akan_expired') {
                $datas->whereRaw("
                    DATE_ADD(tanggal_berlaku_pp_baru, INTERVAL
                        CASE jenis_ajuan
                            WHEN 2 THEN 2
                            WHEN 4 THEN 2
                            ELSE 1
                        END YEAR
                    ) > CURDATE()
                ");
            }

            $datas->orderBy('tanggal_berlaku_pp_baru', 'asc');

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('perusahaan_nama', function ($data) {
                    return $data->perusahaan->penyedia->name
                        ?? $data->perusahaan->name
                        ?? '-';
                })
                ->addColumn('jenis_ajuan_nama', function ($data) {
                    return $data->jenisAjuan->nama ?? '-';
                })
                ->addColumn('masa_berlaku', function ($data) {
                    return in_array((int) $data->jenis_ajuan, [2, 4]) ? '2 Tahun' : '1 Tahun';
                })
                ->addColumn('tanggal_mulai', function ($data) {
                    return $data->tanggal_berlaku_pp_baru
                        ? \Carbon\Carbon::parse($data->tanggal_berlaku_pp_baru)->format('d-m-Y')
                        : '-';
                })
                ->addColumn('tanggal_expired', function ($data) {
                    return $this->hitungExpired($data)->format('d-m-Y');
                })
                ->addColumn('sisa_hari', function ($data) {
                    $expired = $this->hitungExpired($data);
                    $hari    = (int) now()->startOfDay()->diffInDays($expired->startOfDay(), false);

                    if ($hari < 0) {
                        return '<span class="badge bg-danger">Sudah expired ' . abs($hari) . ' hari</span>';
                    } elseif ($hari <= 30) {
                        return '<span class="badge bg-warning">' . $hari . ' hari lagi</span>';
                    } elseif ($hari <= 90) {
                        return '<span class="badge bg-info">' . $hari . ' hari lagi</span>';
                    } else {
                        return '<span class="badge bg-success">' . $hari . ' hari lagi</span>';
                    }
                })
                ->addColumn('options', function ($data) {
                    $detailUrl = route('hi.pp.admbidang.detail', $data->id);
                    return '<a href="' . $detailUrl . '" class="btn btn-sm btn-info">Detail</a>';
                })
                ->rawColumns(['sisa_hari', 'options'])
                ->make(true);
        }

        return view('backend.hi.pp.admbidang.expired');
    }

    /**
     * Halaman Report & Rekap.
     */
    public function report(Request $request)
    {
        $jenisAjuan = EtamHiPpJenisajuan::orderBy('id')->get(['id', 'nama']);

        return view('backend.hi.pp.admbidang.report', compact('jenisAjuan'));
    }

    /**
     * Data report (JSON) — dipanggil via AJAX dengan filter periode.
     */
    public function reportData(Request $request)
    {
        $tahun      = (int) ($request->tahun ?? now()->year);
        $jenisId    = $request->jenis_ajuan; // optional

        // Base query
        $base = EtamHiPpAjuan::query()
            ->whereYear('created_at', $tahun);

        if ($jenisId) {
            $base->where('jenis_ajuan', $jenisId);
        }

        // ====== 1. RINGKASAN ======
        $ringkasan = [
            'total'            => (clone $base)->count(),
            'total_perusahaan' => (clone $base)->distinct('perusahaan_id')->count('perusahaan_id'),
            'menunggu'         => (clone $base)->where('verifikasi_admin', 0)->where('verifikasi_kasi', 0)->count(),
            'acc_admin'        => (clone $base)->where('verifikasi_admin', 1)->count(),
            'revisi_admin'     => (clone $base)->where('verifikasi_admin', 2)->count(),
            'acc_kasi'         => (clone $base)->where('verifikasi_kasi', 1)->count(),
            'revisi_kasi'      => (clone $base)->where('verifikasi_kasi', 2)->count(),
            'selesai'          => (clone $base)->where('verifikasi_admin', 8)->where('verifikasi_kasi', 8)->count(),
        ];

        // ====== 2. PER JENIS AJUAN ======
        $perJenis = (clone $base)
            ->select('jenis_ajuan', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_ajuan')
            ->with('jenisAjuan:id,nama')
            ->get()
            ->map(fn ($row) => [
                'jenis_ajuan' => $row->jenisAjuan->nama ?? '-',
                'total'       => (int) $row->total,
            ]);

        // ====== 3. REKAP DETAIL PER JENIS (status breakdown) ======
        $rekapPerJenis = EtamHiPpJenisajuan::orderBy('id')
            ->get(['id', 'nama'])
            ->map(function ($j) use ($tahun) {
                $q = EtamHiPpAjuan::whereYear('created_at', $tahun)
                    ->where('jenis_ajuan', $j->id);

                return [
                    'jenis'    => $j->nama,
                    'total'    => (clone $q)->count(),
                    'menunggu' => (clone $q)->where('verifikasi_admin', 0)->where('verifikasi_kasi', 0)->count(),
                    'revisi'   => (clone $q)->where('verifikasi_admin', 2)->orWhere('verifikasi_kasi', 2)->count(),
                    'acc'      => (clone $q)->where('verifikasi_admin', 1)->orWhere('verifikasi_kasi', 1)->count(),
                    'selesai'  => (clone $q)->where('verifikasi_admin', 8)->where('verifikasi_kasi', 8)->count(),
                ];
            });

        // ====== 4. TREN BULANAN (12 bulan) ======
        $bulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulan[] = [
                'bulan'   => \Carbon\Carbon::create($tahun, $i, 1)->translatedFormat('M'),
                'total'   => EtamHiPpAjuan::whereYear('created_at', $tahun)
                                ->whereMonth('created_at', $i)
                                ->when($jenisId, fn ($q) => $q->where('jenis_ajuan', $jenisId))
                                ->count(),
                'selesai' => EtamHiPpAjuan::whereYear('created_at', $tahun)
                                ->whereMonth('created_at', $i)
                                ->where('verifikasi_admin', 8)
                                ->where('verifikasi_kasi', 8)
                                ->when($jenisId, fn ($q) => $q->where('jenis_ajuan', $jenisId))
                                ->count(),
            ];
        }

        // ====== 5. TOP PERUSAHAAN ======
        $topPerusahaan = EtamHiPpAjuan::whereYear('created_at', $tahun)
            ->when($jenisId, fn ($q) => $q->where('jenis_ajuan', $jenisId))
            ->with(['perusahaan:id,name', 'perusahaan.penyedia:user_id,name'])
            ->select('perusahaan_id', DB::raw('COUNT(*) as total_ajuan'))
            ->groupBy('perusahaan_id')
            ->orderByDesc('total_ajuan')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'perusahaan'  => $row->perusahaan->penyedia->name
                                    ?? $row->perusahaan->name
                                    ?? '-',
                'total_ajuan' => (int) $row->total_ajuan,
            ]);

        return response()->json([
            'status'          => true,
            'tahun'           => $tahun,
            'ringkasan'       => $ringkasan,
            'per_jenis'       => $perJenis,
            'rekap_per_jenis' => $rekapPerJenis,
            'tren_bulanan'    => $bulan,
            'top_perusahaan'  => $topPerusahaan,
        ]);
    }

    /**
     * Hitung tanggal expired berdasarkan jenis ajuan.
     * - Jenis 2 (Baru) & 4 (Perubahan) → 2 tahun
     * - Jenis 1 (Perpanjangan) & 3 (Perpanjangan 1 Tahun) → 1 tahun
     * Kurangi 1 hari supaya pas di hari terakhir.
     */
    private function hitungExpired($data): \Carbon\Carbon
    {
        $mulai = \Carbon\Carbon::parse($data->tanggal_berlaku_pp_baru);
        $tahun = in_array((int) $data->jenis_ajuan, [2, 4]) ? 2 : 1;

        return $mulai->copy()->addYears($tahun)->subDay();
    }

    private function labelVerifikasi($val)
    {
        switch ((int) $val) {
            case 8:
                return '<span class="badge bg-primary">Selesai</span>';
            case 1:
                return '<span class="badge bg-success">ACC</span>';
            case 2:
                return '<span class="badge bg-warning">Revisi</span>';
            default:
                return '<span class="badge bg-secondary">Menunggu</span>';
        }
    }
}
