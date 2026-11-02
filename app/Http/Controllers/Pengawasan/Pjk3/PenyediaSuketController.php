<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusBadge($row))
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
            'perusahaanList' => $this->daftarPerusahaanYangDiperiksa(),
            'kategoriList' => EtamPengawasanK3Kategori::orderBy('nama')->get(),
            'dokumenKeys' => EtamPjk3Suket::dokumenKeys(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->isPerusahaanPjk3(), 403);

        $validator = $this->validator($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $suket = EtamPjk3Suket::create([
                'pjk3_penyedia_id' => Auth::id(),
                'perusahaan_id' => $request->perusahaan_id,
                'kategori_id' => $request->kategori_id,
                'jenis_id' => $request->jenis_id,
                'nama_alat' => $request->nama_alat,
                'lokasi_alat' => $request->lokasi_alat,
                'kapasitas_alat' => $request->kapasitas_alat,
                'jumlah_unit' => $request->jumlah_unit ?? 1,
                'dokumen' => $this->collectDokumen($request),
                'status' => EtamPjk3Suket::STATUS_DIAJUKAN,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
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
            ->with(['profilPjk3', 'profilPerusahaan', 'kategori', 'jenis', 'pengawas', 'kasi'])
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
                ->addColumn('status_label', fn (EtamPjk3Suket $row) => $this->statusBadge($row))
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
            ->with(['profilPjk3', 'profilPerusahaan', 'kategori', 'jenis', 'pengawas', 'kasi'])
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
            'perusahaan_id' => ['required', 'exists:users_penyedia,user_id', Rule::notIn([(string) Auth::id()])],
            'kategori_id' => 'required|exists:etam_pengawasan_k3_kategori,id',
            'jenis_id' => 'nullable|exists:etam_pengawasan_k3_jenis,id',
            'nama_alat' => 'required|string|max:255',
            'lokasi_alat' => 'required|string|max:255',
            'kapasitas_alat' => 'nullable|string|max:255',
            'jumlah_unit' => 'nullable|integer|min:1',
        ];
        foreach (array_keys(EtamPjk3Suket::dokumenKeys()) as $key) {
            $rules["tipe_{$key}"] = 'required|in:file,link';
            $rules["file_{$key}"] = 'nullable|required_if:tipe_'.$key.',file|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules["link_{$key}"] = 'nullable|required_if:tipe_'.$key.',link|url|max:500';
        }

        return Validator::make($request->all(), $rules, [
            'perusahaan_id.not_in' => 'Pilih perusahaan yang diperiksa (yang punya akun), bukan akun PJK3 sendiri.',
            'perusahaan_id.exists' => 'Perusahaan yang diperiksa harus sudah punya akun di ETAM Kerja.',
            'required_if' => 'Lengkapi file atau tautan sesuai pilihan.',
        ]);
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

    private function storeFile(\Illuminate\Http\UploadedFile $file): string
    {
        $dir = sprintf('suket_k3/%s/%s/%s', now()->format('Y'), now()->format('m'), md5((string) Auth::id()));
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '_', $file->getClientOriginalName()) ?: 'dokumen';

        return $file->storeAs($dir, ltrim($safe, '.') ?: 'dokumen', 'public');
    }

    private function statusBadge(EtamPjk3Suket $row): string
    {
        $map = [
            EtamPjk3Suket::STATUS_DIAJUKAN => 'warning',
            EtamPjk3Suket::STATUS_DISPO => 'info',
            EtamPjk3Suket::STATUS_DRAFT => 'secondary',
            EtamPjk3Suket::STATUS_TTD_PENGAWAS => 'primary',
            EtamPjk3Suket::STATUS_TTD_KADIS => 'success',
        ];
        $color = $map[(int) $row->status] ?? 'secondary';

        return '<span class="badge bg-'.$color.'">'.$row->statusLabel().'</span>';
    }
}
