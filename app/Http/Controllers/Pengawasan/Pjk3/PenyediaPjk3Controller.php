<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenyediaPjk3Controller extends Controller
{
    use ManagesPjk3Access;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamPjk3Ajuan::query()
                ->where('penyedia_id', Auth::id())
                ->select('id', 'tanggal_ajuan', 'status_verifikasi', 'masa_berlaku', 'alasan', 'created_at')
                ->latest();

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('tanggal_fmt', function (EtamPjk3Ajuan $data) {
                    return optional($data->tanggal_ajuan)->format('d-m-Y') ?? '-';
                })
                ->addColumn('status_label', function (EtamPjk3Ajuan $data) {
                    return $this->statusBadge($data);
                })
                ->addColumn('masa_berlaku_fmt', function (EtamPjk3Ajuan $data) {
                    return optional($data->masa_berlaku)->format('d-m-Y') ?? '-';
                })
                ->addColumn('options', function (EtamPjk3Ajuan $data) {
                    $html = '<a href="'.route('pengawasan.pjk3.penyedia.show', $data->id).'" class="btn btn-info btn-sm">Detail</a>';
                    if ((int) $data->status_verifikasi === EtamPjk3Ajuan::STATUS_REVISI) {
                        $html .= ' <a href="'.route('pengawasan.pjk3.penyedia.revisi', $data->id).'" class="btn btn-warning btn-sm">Revisi</a>';
                    }

                    return $html;
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.penyedia.index', [
            'canAjukan' => $this->canAjukanBaru(),
        ]);
    }

    public function create()
    {
        if (! $this->canAjukanBaru()) {
            return redirect()
                ->route('pengawasan.pjk3.penyedia.index')
                ->with('error', 'Anda masih memiliki pengajuan yang aktif atau menunggu verifikasi.');
        }

        return view('backend.pengawasan.pjk3.penyedia.form', [
            'ajuan' => null,
            'dokumenKeys' => EtamPjk3Ajuan::dokumenKeys(),
        ]);
    }

    public function store(Request $request)
    {
        if (! $this->canAjukanBaru()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki pengajuan yang aktif atau menunggu verifikasi.',
            ], 422);
        }

        $validator = $this->dokumenValidator($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $ajuan = EtamPjk3Ajuan::create([
                'penyedia_id' => Auth::id(),
                'tanggal_ajuan' => now()->toDateString(),
                'status_verifikasi' => EtamPjk3Ajuan::STATUS_MENUNGGU,
                'dokumen' => $this->collectDokumen($request, penyediaId: Auth::id()),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan PJK3 berhasil dikirim.',
                'redirect' => route('pengawasan.pjk3.penyedia.show', $ajuan->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: '.$e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $ajuan = $this->ownAjuan($id);
        $ajuan->load(['penyedia:id,name,email', 'profilPenyedia']);

        return view('backend.pengawasan.pjk3.penyedia.show', [
            'ajuan' => $ajuan,
            'dokumenKeys' => EtamPjk3Ajuan::dokumenKeys(),
        ]);
    }

    public function revisi(string $id)
    {
        $ajuan = $this->ownAjuan($id);
        if ((int) $ajuan->status_verifikasi !== EtamPjk3Ajuan::STATUS_REVISI) {
            return redirect()
                ->route('pengawasan.pjk3.penyedia.index')
                ->with('error', 'Pengajuan ini tidak dalam status revisi.');
        }

        return view('backend.pengawasan.pjk3.penyedia.form', [
            'ajuan' => $ajuan,
            'dokumenKeys' => EtamPjk3Ajuan::dokumenKeys(),
        ]);
    }

    public function file(string $id, string $key)
    {
        return $this->streamPjk3Dokumen($this->ownAjuan($id), $key);
    }

    public function update(Request $request, string $id)
    {
        $ajuan = $this->ownAjuan($id);
        if ((int) $ajuan->status_verifikasi !== EtamPjk3Ajuan::STATUS_REVISI) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini tidak dalam status revisi.',
            ], 422);
        }

        $validator = $this->dokumenValidator($request, $ajuan);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $log = $ajuan->log_verifikasi ?? [];
        $log[] = [
            'status' => 'ajuan_ulang',
            'waktu' => now()->toDateTimeString(),
            'user_id' => Auth::id(),
            'nama' => Auth::user()->name,
            'alasan' => $request->input('keterangan'),
        ];

        $ajuan->update([
            'tanggal_ajuan' => now()->toDateString(),
            'status_verifikasi' => EtamPjk3Ajuan::STATUS_MENUNGGU,
            'alasan' => $request->input('keterangan'),
            'dokumen' => $this->collectDokumen($request, $ajuan->dokumen ?? [], true, $ajuan->penyedia_id),
            'log_verifikasi' => $log,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Revisi pengajuan PJK3 berhasil dikirim.',
            'redirect' => route('pengawasan.pjk3.penyedia.show', $ajuan->id),
        ]);
    }

    private function canAjukanBaru(): bool
    {
        $latest = EtamPjk3Ajuan::query()
            ->where('penyedia_id', Auth::id())
            ->latest('id')
            ->first();

        if (! $latest) {
            return true;
        }

        $status = (int) $latest->status_verifikasi;
        if ($status === EtamPjk3Ajuan::STATUS_TOLAK) {
            return true;
        }

        if ($status === EtamPjk3Ajuan::STATUS_ACC && $latest->isExpired()) {
            return true;
        }

        return false;
    }

    private function ownAjuan(string $id): EtamPjk3Ajuan
    {
        return EtamPjk3Ajuan::query()
            ->where('penyedia_id', Auth::id())
            ->findOrFail($id);
    }

    private function dokumenValidator(Request $request, ?EtamPjk3Ajuan $existing = null)
    {
        $rules = [];
        foreach (array_keys(EtamPjk3Ajuan::dokumenKeys()) as $key) {
            $rules["tipe_{$key}"] = 'required|in:file,link';
            $hasExistingFile = ($existing?->dokumen[$key]['tipe'] ?? null) === 'file'
                && ! empty($existing->dokumen[$key]['value'] ?? null);

            $rules["file_{$key}"] = [
                'nullable',
                'required_if:tipe_'.$key.',file',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ];
            if ($hasExistingFile) {
                $rules["file_{$key}"] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'];
            }

            $rules["link_{$key}"] = 'nullable|required_if:tipe_'.$key.',link|url|max:500';
        }

        if ($existing) {
            $rules['keterangan'] = 'required|string|max:1000';
        }

        return Validator::make($request->all(), $rules, [
            'required_if' => 'Lengkapi file atau tautan sesuai pilihan.',
            'url' => 'Tautan tidak valid.',
            'mimes' => 'File harus PDF, JPG, atau PNG.',
            'max' => 'Ukuran file maksimal 2 MB.',
            'keterangan.required' => 'Keterangan wajib diisi saat mengajukan ulang.',
        ]);
    }

    /**
     * @param  array<string, array{tipe: string, value: string}>  $existing
     * @return array<string, array{tipe: string, value: string}>
     */
    private function collectDokumen(Request $request, array $existing = [], bool $replaceOld = false, ?int $penyediaId = null): array
    {
        $penyediaId ??= Auth::id();
        $dokumen = [];
        foreach (array_keys(EtamPjk3Ajuan::dokumenKeys()) as $key) {
            $tipe = $request->input('tipe_'.$key);
            $previous = $existing[$key] ?? null;

            if ($tipe === 'link') {
                $dokumen[$key] = [
                    'tipe' => 'link',
                    'value' => $request->input('link_'.$key),
                ];
                if ($replaceOld) {
                    $this->deleteStoredFile($previous);
                }

                continue;
            }

            if ($request->hasFile('file_'.$key)) {
                $dokumen[$key] = [
                    'tipe' => 'file',
                    'value' => $this->storeUploadedFile($request->file('file_'.$key), $penyediaId),
                ];
                if ($replaceOld) {
                    $this->deleteStoredFile($previous);
                }

                continue;
            }

            $dokumen[$key] = $previous ?? ['tipe' => 'file', 'value' => ''];
        }

        return $dokumen;
    }

    private function storeUploadedFile(\Illuminate\Http\UploadedFile $file, int $penyediaId): string
    {
        $dir = sprintf(
            'ajuan_pjk3/%s/%s/%s',
            now()->format('Y'),
            now()->format('m'),
            md5((string) $penyediaId)
        );

        $original = $file->getClientOriginalName();
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '_', $original) ?: 'dokumen';
        $safe = ltrim($safe, '.') ?: 'dokumen';

        $name = $safe;
        $i = 1;
        while (Storage::disk('public')->exists($dir.'/'.$name)) {
            $ext = pathinfo($safe, PATHINFO_EXTENSION);
            $base = pathinfo($safe, PATHINFO_FILENAME);
            $name = $ext !== '' ? $base.'_'.$i.'.'.$ext : $base.'_'.$i;
            $i++;
        }

        return $file->storeAs($dir, $name, 'public');
    }

    /**
     * @param  array{tipe?: string, value?: string}|null  $item
     */
    private function deleteStoredFile(?array $item): void
    {
        if (($item['tipe'] ?? null) !== 'file' || empty($item['value'])) {
            return;
        }

        Storage::disk('public')->delete($item['value']);
    }

    private function statusBadge(EtamPjk3Ajuan $data): string
    {
        $map = [
            EtamPjk3Ajuan::STATUS_MENUNGGU => 'warning',
            EtamPjk3Ajuan::STATUS_ACC => 'success',
            EtamPjk3Ajuan::STATUS_REVISI => 'info',
            EtamPjk3Ajuan::STATUS_TOLAK => 'danger',
        ];
        $color = $data->isExpired() ? 'secondary' : ($map[(int) $data->status_verifikasi] ?? 'secondary');

        return '<span class="badge bg-'.$color.'">'.$data->statusLabel().'</span>';
    }
}
