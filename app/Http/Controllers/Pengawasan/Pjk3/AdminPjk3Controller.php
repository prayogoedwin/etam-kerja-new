<?php

namespace App\Http\Controllers\Pengawasan\Pjk3;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdminPjk3Controller extends Controller
{
    use ManagesPjk3Access;

    public function index(Request $request)
    {
        $this->denyIfCannotAccessAdmin();

        if ($request->ajax()) {
            $datas = EtamPjk3Ajuan::query()
                ->with(['penyedia:id,name,email', 'profilPenyedia:id,user_id,name'])
                ->select('id', 'penyedia_id', 'tanggal_ajuan', 'status_verifikasi', 'masa_berlaku', 'created_at')
                ->latest();

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('perusahaan', function (EtamPjk3Ajuan $data) {
                    return $data->profilPenyedia->name ?? ($data->penyedia->name ?? '-');
                })
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
                    return '<a href="'.route('pengawasan.pjk3.admin.show', $data->id).'" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        return view('backend.pengawasan.pjk3.admin.index');
    }

    public function show(string $id)
    {
        $this->denyIfCannotAccessAdmin();

        $ajuan = EtamPjk3Ajuan::with(['penyedia:id,name,email', 'profilPenyedia'])->findOrFail($id);

        return view('backend.pengawasan.pjk3.admin.show', [
            'ajuan' => $ajuan,
            'dokumenKeys' => EtamPjk3Ajuan::dokumenKeys(),
            'canVerify' => $this->canVerifyPjk3(),
        ]);
    }

    public function file(string $id, string $key)
    {
        $this->denyIfCannotAccessAdmin();

        return $this->streamPjk3Dokumen(EtamPjk3Ajuan::findOrFail($id), $key);
    }

    public function verifikasi(Request $request, string $id)
    {
        $this->denyIfCannotVerify();

        $ajuan = EtamPjk3Ajuan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:acc,revisi,tolak',
            'alasan' => 'required_if:status,revisi,tolak|nullable|string|max:1000',
            'masa_berlaku' => 'required_if:status,acc|nullable|date|after_or_equal:today',
        ], [
            'alasan.required_if' => 'Alasan wajib diisi untuk revisi atau tolak.',
            'masa_berlaku.required_if' => 'Masa berlaku wajib diisi jika ACC.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $statusMap = [
            'acc' => EtamPjk3Ajuan::STATUS_ACC,
            'revisi' => EtamPjk3Ajuan::STATUS_REVISI,
            'tolak' => EtamPjk3Ajuan::STATUS_TOLAK,
        ];
        $statusKode = $statusMap[$request->status];

        $log = $ajuan->log_verifikasi ?? [];
        $log[] = [
            'status' => $request->status,
            'waktu' => now()->toDateTimeString(),
            'user_id' => Auth::id(),
            'nama' => Auth::user()->name,
            'alasan' => $request->alasan,
        ];

        $ajuan->update([
            'status_verifikasi' => $statusKode,
            'alasan' => $request->alasan,
            'masa_berlaku' => $request->status === 'acc' ? $request->masa_berlaku : null,
            'log_verifikasi' => $log,
            'updated_by' => Auth::id(),
        ]);

        $pesan = [
            'acc' => 'Pengajuan PJK3 disetujui.',
            'revisi' => 'Pengajuan dikembalikan untuk revisi.',
            'tolak' => 'Pengajuan PJK3 ditolak.',
        ];

        return response()->json([
            'success' => true,
            'message' => $pesan[$request->status],
            'redirect' => route('pengawasan.pjk3.admin.show', $ajuan->id),
        ]);
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
