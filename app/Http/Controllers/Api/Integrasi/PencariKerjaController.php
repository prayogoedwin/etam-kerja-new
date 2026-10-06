<?php

namespace App\Http\Controllers\Api\Integrasi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPencari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class PencariKerjaController extends Controller
{
    public function register(Request $request)
    {
        $idIntegration = $request->attributes->get('id_integration');

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16',
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama_id' => 'required|integer',
            'kabkota_id' => 'required|integer',
            'kecamatan_id' => 'required|integer',
            'alamat' => 'required|string|max:200',
            'kodepos' => 'required|string|max:5',
            'pendidikan_id' => 'required|integer',
            'jurusan_id' => 'required|integer',
            'tahun_lulus' => 'nullable|integer',
            'id_status_perkawinan' => 'required|string|max:1',
            'jabatan_harapan_id' => 'nullable|integer',
            'disabilitas' => 'nullable|in:0,1,ya,tidak,Ya,Tidak',
            'jenis_disabilitas' => 'nullable|string|max:255',
            'keterangan_disabilitas' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'whatsapp' => 'required|string|max:15',
            'medsos' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
                'data' => null,
            ], 422);
        }

        if (UserPencari::where('ktp', $request->nik)->whereNull('deleted_at')->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'NIK sudah terdaftar',
                'data' => null,
            ], 409);
        }

        if (User::where('email', $request->email)->whereNull('deleted_at')->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Email sudah terdaftar',
                'data' => null,
            ], 409);
        }

        if (User::where('whatsapp', $request->whatsapp)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'WhatsApp sudah terdaftar',
                'data' => null,
            ], 409);
        }

        $disabilitas = $this->normalizeDisabilitas($request->disabilitas);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'password' => $request->password,
                'is_finished' => 1,
            ]);

            $role = Role::where('name', 'pencari-kerja')->first();
            if ($role) {
                $user->assignRole($role);
            }

            $pencari = UserPencari::create([
                'user_id' => $user->id,
                'ktp' => $request->nik,
                'name' => $request->nama,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'gender' => $request->jenis_kelamin,
                'id_provinsi' => 64,
                'id_kota' => $request->kabkota_id,
                'id_kecamatan' => $request->kecamatan_id,
                'alamat' => $request->alamat,
                'kodepos' => $request->kodepos,
                'id_pendidikan' => $request->pendidikan_id,
                'id_jurusan' => $request->jurusan_id,
                'tahun_lulus' => $request->tahun_lulus ?: (int) date('Y'),
                'id_status_perkawinan' => $request->id_status_perkawinan,
                'id_agama' => $request->agama_id,
                'id_jabatan_harapan' => $request->jabatan_harapan_id,
                'foto' => null,
                'status_id' => 1,
                'is_alumni_bkk' => 0,
                'bkk_id' => null,
                'toket' => null,
                'disabilitas' => $disabilitas,
                'jenis_disabilitas' => $request->jenis_disabilitas,
                'keterangan_disabilitas' => $request->keterangan_disabilitas,
                'posted_by' => $user->id,
                'id_integration' => $idIntegration,
                'is_diterima' => 0,
                'medsos' => $request->medsos,
                'ex_tambang' => '0',
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil register pencari kerja',
                'data' => [
                    'id' => $pencari->id,
                    'user_id' => $user->id,
                    'nik' => $pencari->ktp,
                    'nama' => $pencari->name,
                    'email' => $user->email,
                    'whatsapp' => $user->whatsapp,
                    'id_integration' => $idIntegration,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API register pencari: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal register pencari kerja',
                'data' => null,
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'email' => 'nullable|email',
            'nik' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
                'data' => null,
            ], 422);
        }

        if (! $request->filled('id') && ! $request->filled('email') && ! $request->filled('nik')) {
            return response()->json([
                'status' => false,
                'message' => 'Minimal salah satu parameter: id, email, atau nik',
                'data' => null,
            ], 422);
        }

        $query = UserPencari::with([
            'user:id,name,email,whatsapp',
            'provinsi:id,name',
            'kabkota:id,name',
            'kecamatan:id,name',
            'pendidikan:id,name',
            'jurusan:id,nama',
            'agama:id,name',
        ])->whereNull('deleted_at');

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('nik')) {
            $query->where('ktp', $request->nik);
        }

        if ($request->filled('email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', $request->email);
            });
        }

        $pencari = $query->first();

        if (! $pencari) {
            return response()->json([
                'status' => false,
                'message' => 'Pencari kerja tidak ditemukan',
                'data' => null,
            ], 404);
        }

        $marital = DB::table('etam_marital')->where('id', $pencari->id_status_perkawinan)->value('name');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mendapatkan data pencari kerja',
            'id_integration' => $request->attributes->get('id_integration'),
            'data' => [
                'id' => $pencari->id,
                'user_id' => $pencari->user_id,
                'nik' => $pencari->ktp,
                'nama' => $pencari->name,
                'email' => optional($pencari->user)->email,
                'whatsapp' => optional($pencari->user)->whatsapp,
                'tempat_lahir' => $pencari->tempat_lahir,
                'tanggal_lahir' => $pencari->tanggal_lahir,
                'jenis_kelamin' => $pencari->gender,
                'agama_id' => $pencari->id_agama,
                'agama' => optional($pencari->agama)->name,
                'kabkota_id' => $pencari->id_kota,
                'kabkota' => optional($pencari->kabkota)->name,
                'kecamatan_id' => $pencari->id_kecamatan,
                'kecamatan' => optional($pencari->kecamatan)->name,
                'alamat' => $pencari->alamat,
                'kodepos' => $pencari->kodepos,
                'pendidikan_id' => $pencari->id_pendidikan,
                'pendidikan' => optional($pencari->pendidikan)->name,
                'jurusan_id' => $pencari->id_jurusan,
                'jurusan' => optional($pencari->jurusan)->nama,
                'id_status_perkawinan' => $pencari->id_status_perkawinan,
                'status_perkawinan' => $marital,
                'jabatan_harapan_id' => $pencari->id_jabatan_harapan,
                'disabilitas' => $pencari->disabilitas,
                'jenis_disabilitas' => $pencari->jenis_disabilitas,
                'id_integration' => $pencari->id_integration,
            ],
        ]);
    }

    private function normalizeDisabilitas($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $v = strtolower((string) $value);
        if (in_array($v, ['1', 'ya'], true)) {
            return '1';
        }

        return '0';
    }
}
