<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\BLK\EtamBlkPelatihanPesertaPerusahaan;
use App\Models\UserPencari;
use App\Models\UserPenyedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PesertaController extends Controller
{
    use ManagesBlkAccess;

    public function index(Request $request, string $pelatihanId)
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($pelatihanId);
        if (! $this->canAccessBlk((int) $pelatihan->blk_id)) {
            abort(403);
        }

        if ($request->ajax()) {
            if ((int) $pelatihan->pelatihan_untuk === EtamBlkPelatihan::UNTUK_PENYEDIA) {
                $datas = EtamBlkPelatihanPesertaPerusahaan::query()
                    ->where('blk_pelatihan_id', $pelatihan->id)
                    ->select('id', 'name', 'nib', 'email', 'hp', 'alamat', 'status_pendaftaran', 'alasan_status');

                return DataTables::of($datas)
                    ->addIndexColumn()
                    ->addColumn('identitas', function (EtamBlkPelatihanPesertaPerusahaan $data) {
                        return $data->nib ?? '-';
                    })
                    ->addColumn('status_label', function (EtamBlkPelatihanPesertaPerusahaan $data) {
                        return $this->statusBadge((int) $data->status_pendaftaran);
                    })
                    ->addColumn('options', function (EtamBlkPelatihanPesertaPerusahaan $data) {
                        return '<button class="btn btn-warning btn-sm" onclick="showStatusModal('.$data->id.', '.$data->status_pendaftaran.', `'.e($data->alasan_status ?? '').'`)">Status</button>';
                    })
                    ->rawColumns(['status_label', 'options'])
                    ->make(true);
            }

            $datas = EtamBlkPelatihanPeserta::query()
                ->where('blk_pelatihan_id', $pelatihan->id)
                ->select('id', 'name', 'ktp', 'email', 'hp', 'alamat', 'status_pendaftaran', 'alasan_status');

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('identitas', function (EtamBlkPelatihanPeserta $data) {
                    return $data->ktp ?? '-';
                })
                ->addColumn('status_label', function (EtamBlkPelatihanPeserta $data) {
                    return $this->statusBadge((int) $data->status_pendaftaran);
                })
                ->addColumn('options', function (EtamBlkPelatihanPeserta $data) {
                    return '<button class="btn btn-warning btn-sm" onclick="showStatusModal('.$data->id.', '.$data->status_pendaftaran.', `'.e($data->alasan_status ?? '').'`)">Status</button>';
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        $statusLabels = EtamBlkPelatihanPeserta::statusLabels();

        return view('backend.blk.pelatihan.peserta', compact('pelatihan', 'statusLabels'));
    }

    public function updateStatus(Request $request, string $pelatihanId, string $pesertaId)
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($pelatihanId);
        if (! $this->canAccessBlk((int) $pelatihan->blk_id)) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'status_pendaftaran' => 'required|in:0,1,2,3,4,5',
            'alasan_status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        if ((int) $pelatihan->pelatihan_untuk === EtamBlkPelatihan::UNTUK_PENYEDIA) {
            $peserta = EtamBlkPelatihanPesertaPerusahaan::where('blk_pelatihan_id', $pelatihan->id)->findOrFail($pesertaId);
        } else {
            $peserta = EtamBlkPelatihanPeserta::where('blk_pelatihan_id', $pelatihan->id)->findOrFail($pesertaId);
        }

        $peserta->update([
            'status_pendaftaran' => $request->status_pendaftaran,
            'alasan_status' => $request->alasan_status,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status peserta berhasil diupdate']);
    }

    public function formDaftar(string $id)
    {
        $pelatihan = EtamBlkPelatihan::with(['blk', 'syarat', 'fasilitas'])->findOrFail($id);
        $role = $this->currentRoleName();

        if ($role === 'pencari-kerja' && (int) $pelatihan->pelatihan_untuk !== EtamBlkPelatihan::UNTUK_PENCARI) {
            abort(403);
        }
        if ($role === 'penyedia-kerja' && (int) $pelatihan->pelatihan_untuk !== EtamBlkPelatihan::UNTUK_PENYEDIA) {
            abort(403);
        }

        $already = $this->existingRegistration($pelatihan);

        return view('backend.blk.pelatihan.daftar', compact('pelatihan', 'already', 'role'));
    }

    public function daftar(Request $request, string $id)
    {
        $pelatihan = EtamBlkPelatihan::findOrFail($id);
        $role = $this->currentRoleName();

        if (! $pelatihan->isOpenForRegistration()) {
            return redirect()->back()->with('error', 'Pendaftaran pelatihan ini sedang ditutup');
        }

        if ($this->existingRegistration($pelatihan)) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar pada pelatihan ini');
        }

        if ($role === 'pencari-kerja') {
            return $this->daftarPencari($pelatihan);
        }

        if ($role === 'penyedia-kerja') {
            return $this->daftarPenyedia($pelatihan);
        }

        abort(403);
    }

    private function daftarPencari(EtamBlkPelatihan $pelatihan)
    {
        if ((int) $pelatihan->pelatihan_untuk !== EtamBlkPelatihan::UNTUK_PENCARI) {
            abort(403);
        }

        $pencari = UserPencari::where('user_id', Auth::id())->first();
        if (! $pencari) {
            return redirect()->back()->with('error', 'Profil pencari kerja belum lengkap');
        }

        $user = Auth::user();

        EtamBlkPelatihanPeserta::create([
            'blk_pelatihan_id' => $pelatihan->id,
            'pencari_id' => $pencari->id,
            'status_pendaftaran' => EtamBlkPelatihanPeserta::STATUS_MENUNGGU,
            'name' => $pencari->name ?? $user->name,
            'ktp' => $pencari->ktp,
            'email' => $user->email,
            'hp' => $user->whatsapp,
            'tempat_lahir' => $pencari->tempat_lahir,
            'tanggal_lahir' => $pencari->tanggal_lahir,
            'gender' => $pencari->gender,
            'id_provinsi' => $pencari->id_provinsi,
            'id_kota' => $pencari->id_kota,
            'id_kecamatan' => $pencari->id_kecamatan,
            'alamat' => $pencari->alamat,
            'id_pendidikan' => $pencari->id_pendidikan,
            'id_jurusan' => $pencari->id_jurusan,
            'id_status_perkawinan' => $pencari->id_status_perkawinan,
            'sumber_informasi' => 100,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('blk.pelatihan.index')->with('success', 'Pendaftaran pelatihan berhasil dikirim');
    }

    private function daftarPenyedia(EtamBlkPelatihan $pelatihan)
    {
        if ((int) $pelatihan->pelatihan_untuk !== EtamBlkPelatihan::UNTUK_PENYEDIA) {
            abort(403);
        }

        $penyedia = UserPenyedia::where('user_id', Auth::id())->first();
        if (! $penyedia) {
            return redirect()->back()->with('error', 'Profil pemberi kerja belum lengkap');
        }

        $user = Auth::user();

        EtamBlkPelatihanPesertaPerusahaan::create([
            'blk_pelatihan_id' => $pelatihan->id,
            'perusahaan_id' => $penyedia->id,
            'status_pendaftaran' => EtamBlkPelatihanPeserta::STATUS_MENUNGGU,
            'name' => $penyedia->name ?? $user->name,
            'nib' => $penyedia->nib,
            'email' => $user->email,
            'hp' => $user->whatsapp,
            'id_provinsi' => $penyedia->id_provinsi,
            'id_kota' => $penyedia->id_kota,
            'id_kecamatan' => $penyedia->id_kecamatan,
            'alamat' => $penyedia->alamat,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('blk.pelatihan.index')->with('success', 'Pendaftaran pelatihan berhasil dikirim');
    }

    private function existingRegistration(EtamBlkPelatihan $pelatihan): mixed
    {
        $role = $this->currentRoleName();

        if ($role === 'pencari-kerja') {
            $pencari = UserPencari::where('user_id', Auth::id())->first();
            if (! $pencari) {
                return null;
            }

            return EtamBlkPelatihanPeserta::where('blk_pelatihan_id', $pelatihan->id)
                ->where('pencari_id', $pencari->id)
                ->first();
        }

        if ($role === 'penyedia-kerja') {
            $penyedia = UserPenyedia::where('user_id', Auth::id())->first();
            if (! $penyedia) {
                return null;
            }

            return EtamBlkPelatihanPesertaPerusahaan::where('blk_pelatihan_id', $pelatihan->id)
                ->where('perusahaan_id', $penyedia->id)
                ->first();
        }

        return null;
    }

    private function statusBadge(int $status): string
    {
        $map = [
            0 => 'warning',
            1 => 'info',
            2 => 'success',
            3 => 'danger',
            4 => 'secondary',
            5 => 'primary',
        ];
        $label = EtamBlkPelatihanPeserta::statusLabels()[$status] ?? '-';
        $color = $map[$status] ?? 'secondary';

        return '<span class="badge bg-'.$color.'">'.$label.'</span>';
    }
}
