<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use App\Models\Pengawasan\Pjk3\EtamPjk3SuketUji;
use App\Services\Pengawasan\SuketDokumenTteService;
use App\Services\Tte\Handlers\SuketK3Handler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdminSuketController extends Controller
{
    use ManagesPjk3Access;

    public function pengajuanIndex(Request $request)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);

        if ($request->ajax()) {
            $query = EtamPjk3Suket::query()
                ->with(['profilPjk3:id,user_id,name', 'profilPerusahaan:id,user_id,name', 'kategori:id,nama,kode']);

            if ($this->currentRoleName() === 'petugas-bidang') {
                $query->where('pengawas_id', Auth::id());
            }

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('pjk3', fn (EtamPjk3Suket $row) => $row->profilPjk3->name ?? '-')
                ->addColumn('perusahaan', fn (EtamPjk3Suket $row) => $row->profilPerusahaan->name ?? '-')
                ->addColumn('kategori_nama', fn (EtamPjk3Suket $row) => $row->kategori->nama ?? '-')
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusTimeline($row, true))
                ->addColumn('options', function (EtamPjk3Suket $row) {
                    return '<a href="'.route('pengawasan.suket.admin.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.suket.admin-pengajuan-index');
    }

    public function terbitIndex(Request $request)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);

        if ($request->ajax()) {
            $query = EtamPjk3Suket::query()
                ->with(['profilPjk3:id,user_id,name', 'profilPerusahaan:id,user_id,name', 'kategori:id,nama,kode'])
                ->where('status', '>=', EtamPjk3Suket::STATUS_DRAFT);

            if ($this->currentRoleName() === 'petugas-bidang') {
                $query->where('pengawas_id', Auth::id());
            }

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('nomor', fn (EtamPjk3Suket $row) => $row->nomor_suket ?? '-')
                ->addColumn('pjk3', fn (EtamPjk3Suket $row) => $row->profilPjk3->name ?? '-')
                ->addColumn('perusahaan', fn (EtamPjk3Suket $row) => $row->profilPerusahaan->name ?? '-')
                ->addColumn('hasil', function (EtamPjk3Suket $row) {
                    if ($row->is_layak === null) {
                        return '-';
                    }

                    return (int) $row->is_layak === 1
                        ? '<span class="badge bg-success">Layak</span>'
                        : '<span class="badge bg-danger">Tidak layak</span>';
                })
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusTimeline($row, true))
                ->addColumn('options', function (EtamPjk3Suket $row) {
                    return '<a href="'.route('pengawasan.suket.admin.show', $row->id).'" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['hasil', 'status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.suket.admin-terbit-index');
    }

    public function show(string $id)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);
        $suket = $this->findVisible($id);

        $suket = $suket->load([
            'profilPjk3', 'profilPerusahaan', 'kategori', 'ahliK3Kategori', 'jenis',
            'pengawas', 'kasi', 'perangkat.jenis', 'perangkat.uji',
        ]);

        return view('backend.pengawasan.pjk3.suket.admin-show', [
            'suket' => $suket,
            'lhppForm' => $this->lhppFormDefaults($suket),
            'kategoriList' => EtamPengawasanK3Kategori::query()->orderBy('nama')->get(['id', 'nama', 'kode']),
            'jenisList' => EtamPengawasanK3Jenis::query()->orderBy('nama')->get(['id', 'kategori_id', 'nama']),
            'dokumenKeys' => EtamPjk3Suket::dokumenKeys(),
            'daftarPengawas' => $this->daftarPengawasBidangDua(),
            'canDispo' => $this->canDisposisiSuket() && (int) $suket->status === EtamPjk3Suket::STATUS_DIAJUKAN,
            'canAnalisis' => $this->isPengawasK3() && in_array((int) $suket->status, [EtamPjk3Suket::STATUS_DISPO, EtamPjk3Suket::STATUS_DRAFT], true)
                && ($this->currentRoleName() !== 'petugas-bidang' || (int) $suket->pengawas_id === (int) Auth::id()),
        ]);
    }

    public function file(string $id, string $key)
    {
        abort_unless($this->canAccessSuketAdmin(), 403);

        return $this->streamSuketDokumen($this->findVisible($id), $key);
    }

    public function disposisi(Request $request, string $id)
    {
        abort_unless($this->canDisposisiSuket(), 403);
        $suket = EtamPjk3Suket::findOrFail($id);
        abort_unless((int) $suket->status === EtamPjk3Suket::STATUS_DIAJUKAN, 422);

        $validator = Validator::make($request->all(), [
            'pengawas_id' => 'required|exists:users,id',
            'catatan_disposisi' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $pengawasId = (int) $request->pengawas_id;
        abort_unless(
            $this->daftarPengawasBidangDua()->contains(fn ($user) => (int) $user->id === $pengawasId),
            422
        );

        $suket->update([
            'pengawas_id' => $request->pengawas_id,
            'kasi_id' => Auth::id(),
            'catatan_disposisi' => $request->catatan_disposisi,
            'status' => EtamPjk3Suket::STATUS_DISPO,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Disposisi ke pengawas berhasil.']);
    }

    public function analisis(Request $request, string $id)
    {
        abort_unless($this->isPengawasK3(), 403);
        $suket = $this->findVisible($id);
        abort_unless(in_array((int) $suket->status, [EtamPjk3Suket::STATUS_DISPO, EtamPjk3Suket::STATUS_DRAFT], true), 422);

        $validator = Validator::make($request->all(), [
            'tanggal_pemeriksaan' => 'required|date',
            'is_layak' => 'required|in:0,1',
            'hasil_analisis' => 'required|string',
            'rekomendasi' => 'nullable|string',
            'ahli_k3_kategori_id' => 'required|exists:etam_pengawasan_k3_kategori,id',
            'tempat_pengujian' => 'nullable|string|max:255',
            'nama_pengurus' => 'required|string|max:255',
            'jenis_usaha' => 'nullable|string|max:255',
            'jumlah_pekerja' => 'nullable|integer|min:0',
            'nomor_wlkp' => 'nullable|string|max:100',
            'nomor_unit' => 'nullable|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'pabrik' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'negara_pembuat' => 'nullable|string|max:100',
            'tahun_pasang' => 'nullable|integer|min:1900|max:2100',
            'nomor_lhpp_pjk3' => 'required|string|max:120',
            'dasar_hukum' => 'nullable|string',
            'narasi_uji' => 'nullable|string',
            'perangkat' => 'required|array|min:1',
            'perangkat.*.jenis_id' => 'nullable|exists:etam_pengawasan_k3_jenis,id',
            'perangkat.*.nama' => 'nullable|string|max:255',
            'perangkat.*.merk' => 'nullable|string|max:255',
            'perangkat.*.jumlah' => 'nullable|string|max:50',
            'perangkat.*.lokasi' => 'nullable|string|max:255',
            'uji' => 'nullable|array',
            'uji.*.perangkat_index' => 'nullable|integer|min:0',
            'uji.*.item' => 'nullable|string|max:255',
            'uji.*.status' => 'nullable|in:'.EtamPjk3SuketUji::STATUS_MEMENUHI.','.EtamPjk3SuketUji::STATUS_TIDAK_MEMENUHI,
            'uji.*.hasil' => 'nullable|string|max:500',
            'perangkat.*.uji' => 'nullable|array',
            'perangkat.*.uji.*.item' => 'nullable|string|max:255',
            'perangkat.*.uji.*.status' => 'nullable|in:'.EtamPjk3SuketUji::STATUS_MEMENUHI.','.EtamPjk3SuketUji::STATUS_TIDAK_MEMENUHI,
            'perangkat.*.uji.*.hasil' => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $perangkat = $this->gabungkanUjiKePerangkat((array) $request->input('perangkat', []), (array) $request->input('uji', []));
        if ($perangkat === []) {
            return response()->json([
                'success' => false,
                'errors' => ['perangkat' => ['Isi minimal satu objek pengujian (jenis perangkat).']],
            ]);
        }

        $nomor = $suket->nomor_suket;
        if (! $nomor) {
            $kode = $suket->kategori()->value('kode') ?: 'K3';
            $nomor = $this->nextNomorSuket((int) $suket->kategori_id, $kode);
        }

        $kategoriAhli = EtamPengawasanK3Kategori::query()->findOrFail((int) $request->ahli_k3_kategori_id);

        DB::transaction(function () use ($request, $suket, $nomor, $kategoriAhli, $perangkat) {
            $suket->update([
                'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
                'is_layak' => $request->is_layak,
                'hasil_analisis' => $request->hasil_analisis,
                'rekomendasi' => $request->rekomendasi,
                'ahli_k3_kategori_id' => $kategoriAhli->id,
                'lhpp' => $this->collectLhpp($request, $kategoriAhli->nama),
                'nomor_suket' => $nomor,
                'status' => EtamPjk3Suket::STATUS_DRAFT,
                'updated_by' => Auth::id(),
            ]);
            $suket->sinkronkanPerangkatDanUji($perangkat);
        });

        app(SuketK3Handler::class)->ajukanPengawas($suket->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Hasil analisis disimpan. LHPP dan Suket masuk antrean TTE Anda.',
        ]);
    }

    public function cetakLhpp(string $id)
    {
        return $this->cetakDokumen($id, EtamPjk3Suket::TTE_DOK_LHPP);
    }

    public function cetakSuket(string $id)
    {
        return $this->cetakDokumen($id, EtamPjk3Suket::TTE_DOK_SUKET);
    }

    private function cetakDokumen(string $id, string $kodeDokumen)
    {
        $suket = $this->cetakContext($id);
        $admin = $this->canAccessSuketAdmin();
        $pakaiAsli = $admin || ! $suket->isDraft();
        if ($pakaiAsli) {
            $path = app(SuketDokumenTteService::class)->pathBerkasTte($suket, $kodeDokumen);
            if ($path) {
                $nama = $kodeDokumen === EtamPjk3Suket::TTE_DOK_SUKET
                    ? 'suket-k3-'.$suket->id.'.pdf'
                    : 'lhpp-k3-'.$suket->id.'.pdf';

                return response(Storage::disk('local')->get($path), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.$nama.'"',
                ]);
            }
        }

        $view = $kodeDokumen === EtamPjk3Suket::TTE_DOK_SUKET
            ? 'backend.pengawasan.pjk3.suket.cetak-suket'
            : 'backend.pengawasan.pjk3.suket.cetak-lhpp';

        return view($view, [
            'suket' => $suket,
            'untukDs' => false,
            'tampilDraft' => ! $admin && $suket->isDraft(),
            'logoBsre' => asset('assets/etam_be/images/logo/logo_bsre.png'),
            'bsreTeks' => SuketDokumenTteService::BSRE_TEKS,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function lhppFormDefaults(EtamPjk3Suket $suket): array
    {
        $profil = $suket->profilPerusahaan;
        $jenisUsaha = $suket->nilaiLhpp('jenis_usaha');
        if ($jenisUsaha === '' && $profil?->jenis_perusahaan) {
            $jenisUsaha = function_exists('getFullCompanyType')
                ? (string) getFullCompanyType($profil->jenis_perusahaan)
                : (string) $profil->jenis_perusahaan;
        }

        return [
            'ahli_k3_kategori_id' => $suket->ahli_k3_kategori_id ?: $suket->kategori_id,
            'dasar_hukum' => $suket->nilaiLhpp('dasar_hukum'),
            'tempat_pengujian' => $suket->nilaiLhpp('tempat_pengujian') ?: (string) $suket->lokasi_alat,
            'nama_pengurus' => $suket->nilaiLhpp('nama_pengurus'),
            'jenis_usaha' => $jenisUsaha,
            'jumlah_pekerja' => $suket->nilaiLhpp('jumlah_pekerja'),
            'nomor_wlkp' => $suket->nilaiLhpp('nomor_wlkp'),
            'nomor_unit' => $suket->nilaiLhpp('nomor_unit'),
            'nomor_seri' => $suket->nilaiLhpp('nomor_seri'),
            'pabrik' => $suket->nilaiLhpp('pabrik'),
            'model' => $suket->nilaiLhpp('model'),
            'negara_pembuat' => $suket->nilaiLhpp('negara_pembuat'),
            'tahun_pasang' => $suket->nilaiLhpp('tahun_pasang'),
            'nomor_lhpp_pjk3' => $suket->nilaiLhpp('nomor_lhpp_pjk3'),
            'narasi_uji' => $suket->nilaiLhpp('narasi_uji'),
            'perangkat' => $this->perangkatFormDefaults($suket),
            'uji' => $this->ujiFormDefaults($suket),
        ];
    }

    /**
     * @param  array<int|string, mixed>  $perangkat
     * @param  array<int, mixed>  $uji
     * @return array<int, array<string, mixed>>
     */
    private function gabungkanUjiKePerangkat(array $perangkat, array $uji): array
    {
        foreach ($uji as $row) {
            if (! is_array($row)) {
                continue;
            }
            $index = $row['perangkat_index'] ?? null;
            if ($index === null || $index === '' || ! array_key_exists((int) $index, $perangkat)) {
                continue;
            }
            $perangkat[(int) $index]['uji'][] = $row;
        }

        return array_values(array_filter($perangkat, function ($row) {
            return is_array($row) && (int) ($row['jenis_id'] ?? 0) > 0;
        }));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function perangkatFormDefaults(EtamPjk3Suket $suket): array
    {
        if ($suket->perangkat->isNotEmpty()) {
            return $suket->perangkat->map(function ($row) {
                $uji = $row->uji->map(fn ($item) => [
                    'item' => $item->item,
                    'status' => $item->status,
                    'hasil' => $item->hasil,
                ])->all();

                return [
                    'jenis_id' => $row->jenis_id,
                    'nama' => $row->nama,
                    'merk' => $row->merk,
                    'jumlah' => $row->jumlah,
                    'lokasi' => $row->lokasi,
                    'uji' => $uji !== [] ? $uji : [['item' => '', 'status' => '', 'hasil' => '']],
                ];
            })->all();
        }

        $legacy = $suket->nilaiLhpp('data_teknis', []);
        if (is_array($legacy) && $legacy !== []) {
            $ujiLegacy = $suket->nilaiLhpp('item_uji', []);

            return collect($legacy)->values()->map(function (array $row, int $i) use ($suket, $ujiLegacy) {
                $uji = $i === 0 && is_array($ujiLegacy) && $ujiLegacy !== []
                    ? $ujiLegacy
                    : [['item' => '', 'status' => '', 'hasil' => '']];

                return [
                    'jenis_id' => $suket->jenis_id,
                    'nama' => $row['nama'] ?? '',
                    'merk' => $row['merk'] ?? '',
                    'jumlah' => $row['jumlah'] ?? '',
                    'lokasi' => $row['lokasi'] ?? '',
                    'uji' => $uji,
                ];
            })->all();
        }

        return [[
            'jenis_id' => $suket->jenis_id,
            'nama' => $suket->jenis?->nama ?: (string) $suket->nama_alat,
            'merk' => '',
            'jumlah' => $suket->jumlah_unit ? (string) $suket->jumlah_unit : '',
            'lokasi' => (string) $suket->lokasi_alat,
            'uji' => [],
        ]];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function ujiFormDefaults(EtamPjk3Suket $suket): array
    {
        $uji = [];
        foreach ($suket->perangkat as $i => $alat) {
            foreach ($alat->uji as $item) {
                $uji[] = [
                    'perangkat_index' => $i,
                    'item' => $item->item,
                    'status' => $item->status,
                    'hasil' => $item->hasil,
                ];
            }
        }

        $legacy = $suket->nilaiLhpp('item_uji', []);
        if ($uji === [] && is_array($legacy) && $legacy !== []) {
            foreach ($legacy as $row) {
                $uji[] = [
                    'perangkat_index' => 0,
                    'item' => $row['item'] ?? '',
                    'status' => $row['status'] ?? '',
                    'hasil' => $row['hasil'] ?? '',
                ];
            }
        }

        return $uji !== [] ? $uji : [['perangkat_index' => 0, 'item' => '', 'status' => '', 'hasil' => '']];
    }

    /**
     * @return array<string, mixed>
     */
    private function collectLhpp(Request $request, string $namaBidangAhli): array
    {
        return [
            'ahli_k3_bidang' => $namaBidangAhli,
            'dasar_hukum' => trim((string) $request->input('dasar_hukum')),
            'tempat_pengujian' => trim((string) $request->input('tempat_pengujian')),
            'nama_pengurus' => trim((string) $request->input('nama_pengurus')),
            'jenis_usaha' => trim((string) $request->input('jenis_usaha')),
            'jumlah_pekerja' => $request->filled('jumlah_pekerja') ? (int) $request->input('jumlah_pekerja') : null,
            'nomor_wlkp' => trim((string) $request->input('nomor_wlkp')),
            'nomor_unit' => trim((string) $request->input('nomor_unit')),
            'nomor_seri' => trim((string) $request->input('nomor_seri')),
            'pabrik' => trim((string) $request->input('pabrik')),
            'model' => trim((string) $request->input('model')),
            'negara_pembuat' => trim((string) $request->input('negara_pembuat')),
            'tahun_pasang' => $request->filled('tahun_pasang') ? (int) $request->input('tahun_pasang') : null,
            'nomor_lhpp_pjk3' => trim((string) $request->input('nomor_lhpp_pjk3')),
            'narasi_uji' => trim((string) $request->input('narasi_uji')),
        ];
    }

    private function cetakContext(string $id): EtamPjk3Suket
    {
        $suket = EtamPjk3Suket::with([
            'profilPjk3', 'profilPerusahaan', 'kategori', 'ahliK3Kategori', 'jenis',
            'pengawas', 'perangkat.jenis', 'perangkat.uji',
        ])->findOrFail($id);
        abort_unless((int) $suket->status >= EtamPjk3Suket::STATUS_DRAFT, 404);

        $userId = (int) Auth::id();
        $ok = $this->canAccessSuketAdmin()
            || $userId === (int) $suket->pjk3_penyedia_id
            || $userId === (int) $suket->perusahaan_id;
        abort_unless($ok, 403);

        return $suket;
    }

    private function findVisible(string $id): EtamPjk3Suket
    {
        $query = EtamPjk3Suket::query();
        if ($this->currentRoleName() === 'petugas-bidang') {
            $query->where('pengawas_id', Auth::id());
        }

        return $query->findOrFail($id);
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
