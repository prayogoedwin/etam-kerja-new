<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use App\Models\Pengawasan\Pjk3\EtamPjk3SuketKode;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenyediaSuketController extends Controller
{
    use ManagesPjk3Access;

    public function pengajuanIndex(Request $request)
    {
        abort_unless($this->isPerusahaanPjk3(), 403);

        if ($request->ajax()) {
            $datas = EtamPjk3Suket::query()
                ->with(['profilPerusahaan:id,user_id,name', 'kategori:id,nama,kode'])
                ->where('pjk3_penyedia_id', Auth::id())
                ->latest();

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('perusahaan', fn (EtamPjk3Suket $row) => $row->profilPerusahaan->name ?? '-')
                ->addColumn('kategori_nama', fn (EtamPjk3Suket $row) => $row->kategori->nama ?? '-')
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusTimeline($row, false))
                ->addColumn('options', function (EtamPjk3Suket $row) {
                    return '<a href="'.route('pengawasan.suket.penyedia.pengajuan.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.suket.penyedia-pengajuan-index');
    }

    public function create()
    {
        abort_unless($this->isPerusahaanPjk3(), 403);

        return view('backend.pengawasan.pjk3.suket.penyedia-form', [
            'kategoriList' => EtamPengawasanK3Kategori::orderBy('nama')->get(),
            'jenisList' => EtamPengawasanK3Jenis::query()->orderBy('nama')->get(['id', 'kategori_id', 'nama']),
            'dokumenKeys' => EtamPjk3Suket::dokumenKeys(),
        ]);
    }

    public function cariPerusahaan(Request $request)
    {
        abort_unless($this->isPerusahaanPjk3(), 403);

        $kode = EtamPjk3SuketKode::cariAktif($request->input('kode'));
        if (! $kode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tidak ditemukan atau masa berlakunya sudah habis.',
            ]);
        }
        if ((int) $kode->perusahaan_id === (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode itu milik akun Anda sendiri. Masukkan kode perusahaan yang diperiksa.',
            ]);
        }

        $profil = $kode->profilPerusahaan;
        if (! $profil) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan pada kode ini tidak memiliki akun yang valid.',
            ]);
        }

        return response()->json([
            'success' => true,
            'perusahaan' => [
                'id' => (int) $kode->perusahaan_id,
                'name' => $profil->name,
                'nib' => $profil->nib,
            ],
        ]);
    }

    public function kodeIndex()
    {
        $daftar = EtamPjk3SuketKode::query()
            ->where('perusahaan_id', Auth::id())
            ->latest('id')
            ->get();

        return view('backend.pengawasan.pjk3.suket.penyedia-kode', compact('daftar'));
    }

    public function kodeStore()
    {
        $kode = EtamPjk3SuketKode::buatUntuk((int) Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Kode baru dibuat. Berlaku 30 hari.',
            'kode' => $kode->kode,
            'berlaku_hingga' => $kode->berlaku_hingga?->format('d/m/Y H:i'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->isPerusahaanPjk3(), 403);

        $validator = $this->validator($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $kode = $this->kodePengajuanValid($request);
        if (! $kode) {
            return response()->json([
                'success' => false,
                'errors' => ['kode' => ['Kode perusahaan tidak valid atau masa berlakunya sudah habis.']],
            ]);
        }
        if ((int) $kode->perusahaan_id === (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'errors' => ['kode' => ['Tidak bisa mengajukan Suket untuk akun sendiri.']],
            ]);
        }

        $perangkat = $this->normalisasiPerangkat((array) $request->input('perangkat', []));
        if ($perangkat === []) {
            return response()->json([
                'success' => false,
                'errors' => ['perangkat' => ['Isi minimal satu alat (pilih jenis).']],
            ]);
        }

        $pertama = $perangkat[0];
        DB::beginTransaction();
        try {
            $suket = EtamPjk3Suket::create([
                'pjk3_penyedia_id' => Auth::id(),
                'perusahaan_id' => $kode->perusahaan_id,
                'kode_id' => $kode->id,
                'kategori_id' => $request->kategori_id,
                'jenis_id' => $pertama['jenis_id'] ?? null,
                'nama_alat' => $this->ringkasNamaAlat($perangkat),
                'lokasi_alat' => $this->ringkasLokasiAlat($perangkat),
                'kapasitas_alat' => null,
                'jumlah_unit' => $this->jumlahUnitDariPerangkat($perangkat),
                'dokumen' => $this->collectDokumen($request),
                'status' => EtamPjk3Suket::STATUS_DIAJUKAN,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $suket->sinkronkanPerangkatDanUji($perangkat);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan Suket Layak K3 berhasil dikirim.',
                'redirect' => route('pengawasan.suket.penyedia.pengajuan.show', $suket->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: '.$e->getMessage()], 500);
        }
    }

    public function showPengajuan(string $id)
    {
        abort_unless($this->isPerusahaanPjk3(), 403);
        $suket = EtamPjk3Suket::query()
            ->with(['profilPjk3', 'profilPerusahaan', 'kategori', 'ahliK3Kategori', 'jenis', 'pengawas', 'kasi', 'perangkat.jenis', 'perangkat.uji'])
            ->where('pjk3_penyedia_id', Auth::id())
            ->findOrFail($id);

        return view('backend.pengawasan.pjk3.suket.penyedia-show', [
            'suket' => $suket,
            'dokumenKeys' => EtamPjk3Suket::dokumenKeys(),
            'sebagai' => 'pjk3',
        ]);
    }

    public function terbitIndex(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamPjk3Suket::query()
                ->with(['profilPjk3:id,user_id,name', 'kategori:id,nama,kode'])
                ->where('perusahaan_id', Auth::id())
                ->where('status', '>=', EtamPjk3Suket::STATUS_DRAFT)
                ->latest();

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('pjk3', fn (EtamPjk3Suket $row) => $row->profilPjk3->name ?? '-')
                ->addColumn('nomor', fn (EtamPjk3Suket $row) => $row->nomor_suket ?? '-')
                ->addColumn('hasil', function (EtamPjk3Suket $row) {
                    if ($row->is_layak === null) {
                        return '-';
                    }

                    return (int) $row->is_layak === 1
                        ? '<span class="badge bg-success">Layak</span>'
                        : '<span class="badge bg-danger">Tidak layak</span>';
                })
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusTimeline($row, false))
                ->addColumn('options', function (EtamPjk3Suket $row) {
                    return '<a href="'.route('pengawasan.suket.penyedia.terbit.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['hasil', 'status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.suket.penyedia-terbit-index');
    }

    public function showTerbit(string $id)
    {
        $suket = EtamPjk3Suket::query()
            ->with(['profilPjk3', 'profilPerusahaan', 'kategori', 'ahliK3Kategori', 'jenis', 'pengawas', 'kasi', 'perangkat.jenis', 'perangkat.uji'])
            ->where('perusahaan_id', Auth::id())
            ->where('status', '>=', EtamPjk3Suket::STATUS_DRAFT)
            ->findOrFail($id);

        return view('backend.pengawasan.pjk3.suket.penyedia-show', [
            'suket' => $suket,
            'dokumenKeys' => EtamPjk3Suket::dokumenKeys(),
            'sebagai' => 'uji',
        ]);
    }

    public function file(string $id, string $key)
    {
        $suket = EtamPjk3Suket::query()
            ->where(function ($q) {
                $q->where('pjk3_penyedia_id', Auth::id())
                    ->orWhere('perusahaan_id', Auth::id());
            })
            ->findOrFail($id);

        return $this->streamSuketDokumen($suket, $key);
    }

    public function jenisByKategori(string $kategoriId)
    {
        abort_unless($this->isPerusahaanPjk3(), 403);

        return response()->json(
            EtamPengawasanK3Jenis::query()
                ->where('kategori_id', $kategoriId)
                ->orderBy('nama')
                ->get(['id', 'nama'])
        );
    }

    private function validator(Request $request)
    {
        $rules = [
            'kode' => 'required|string|max:20',
            'kategori_id' => 'required|exists:etam_pengawasan_k3_kategori,id',
            'perangkat' => 'required|array|min:1',
            'perangkat.*.jenis_id' => 'nullable|exists:etam_pengawasan_k3_jenis,id',
            'perangkat.*.nama' => 'nullable|string|max:255',
            'perangkat.*.merk' => 'nullable|string|max:255',
            'perangkat.*.jumlah' => 'nullable|string|max:50',
            'perangkat.*.lokasi' => 'nullable|string|max:255',
        ];
        foreach (array_keys(EtamPjk3Suket::dokumenKeys()) as $key) {
            $rules["tipe_{$key}"] = 'required|in:file,link';
            $rules["file_{$key}"] = 'nullable|required_if:tipe_'.$key.',file|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules["link_{$key}"] = 'nullable|required_if:tipe_'.$key.',link|url|max:500';
        }

        return Validator::make($request->all(), $rules, [
            'kode.required' => 'Masukkan kode perusahaan yang diperiksa, lalu cari.',
            'required_if' => 'Lengkapi file atau tautan sesuai pilihan.',
        ]);
    }

    /**
     * @param  array<int|string, mixed>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function normalisasiPerangkat(array $rows): array
    {
        $hasil = [];
        foreach ($rows as $row) {
            if (! is_array($row) || (int) ($row['jenis_id'] ?? 0) < 1) {
                continue;
            }
            $hasil[] = [
                'jenis_id' => (int) $row['jenis_id'],
                'nama' => trim((string) ($row['nama'] ?? '')),
                'merk' => trim((string) ($row['merk'] ?? '')),
                'jumlah' => trim((string) ($row['jumlah'] ?? '')),
                'lokasi' => trim((string) ($row['lokasi'] ?? '')),
                'uji' => [],
            ];
        }

        return $hasil;
    }

    /**
     * @param  array<int, array<string, mixed>>  $perangkat
     */
    private function ringkasNamaAlat(array $perangkat): string
    {
        $nama = [];
        foreach ($perangkat as $row) {
            $nilai = trim((string) ($row['nama'] ?? ''));
            if ($nilai === '') {
                $nilai = (string) EtamPengawasanK3Jenis::query()->whereKey($row['jenis_id'] ?? 0)->value('nama');
            }
            if ($nilai !== '') {
                $nama[] = $nilai;
            }
        }

        return $nama !== [] ? implode(', ', $nama) : 'Objek K3';
    }

    /**
     * @param  array<int, array<string, mixed>>  $perangkat
     */
    private function ringkasLokasiAlat(array $perangkat): string
    {
        $lokasi = [];
        foreach ($perangkat as $row) {
            $nilai = trim((string) ($row['lokasi'] ?? ''));
            if ($nilai !== '' && ! in_array($nilai, $lokasi, true)) {
                $lokasi[] = $nilai;
            }
        }

        return $lokasi !== [] ? implode('; ', $lokasi) : '-';
    }

    /**
     * @param  array<int, array<string, mixed>>  $perangkat
     */
    private function jumlahUnitDariPerangkat(array $perangkat): int
    {
        $total = 0;
        $adaAngka = false;
        foreach ($perangkat as $row) {
            if (preg_match('/(\d+)/', (string) ($row['jumlah'] ?? ''), $cocok) === 1) {
                $total += (int) $cocok[1];
                $adaAngka = true;
            }
        }

        return $adaAngka ? max(1, $total) : max(1, count($perangkat));
    }

    private function kodePengajuanValid(Request $request): ?EtamPjk3SuketKode
    {
        return EtamPjk3SuketKode::cariAktif($request->input('kode'));
    }

    /**
     * @return array<string, array{tipe: string, value: string}>
     */
    private function collectDokumen(Request $request): array
    {
        $dokumen = [];
        foreach (array_keys(EtamPjk3Suket::dokumenKeys()) as $key) {
            $tipe = $request->input('tipe_'.$key);
            if ($tipe === 'link') {
                $dokumen[$key] = ['tipe' => 'link', 'value' => $request->input('link_'.$key)];

                continue;
            }
            $dokumen[$key] = [
                'tipe' => 'file',
                'value' => $this->storeFile($request->file('file_'.$key)),
            ];
        }

        return $dokumen;
    }

    private function storeFile(UploadedFile $file): string
    {
        $dir = sprintf('suket_k3/%s/%s/%s', now()->format('Y'), now()->format('m'), md5((string) Auth::id()));
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '_', $file->getClientOriginalName()) ?: 'dokumen';

        return $file->storeAs($dir, ltrim($safe, '.') ?: 'dokumen', 'public');
    }

    private function statusTimeline(EtamPjk3Suket $row, bool $admin): string
    {
        return view('backend.pengawasan.pjk3.suket._timeline', [
            'suket' => $row,
            'admin' => $admin,
            'compact' => true,
        ])->render();
    }
}
