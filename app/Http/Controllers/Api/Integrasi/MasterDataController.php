<?php

namespace App\Http\Controllers\Api\Integrasi;

use App\Http\Controllers\Controller;
use App\Models\Agama;
use App\Models\Jabatan;
use App\Models\Jurusan;
use App\Models\Kabkota;
use App\Models\Kecamatan;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterDataController extends Controller
{
    public function jenisKelamin()
    {
        return $this->ok('Berhasil get data jenis kelamin', [
            ['id' => 'L', 'name' => 'Laki-Laki'],
            ['id' => 'P', 'name' => 'Perempuan'],
        ]);
    }

    public function agama()
    {
        $data = Agama::select('id', 'name')->orderBy('id')->get();

        return $this->ok('Berhasil get data agama', $data);
    }

    public function kabkota()
    {
        $data = Kabkota::select('id', 'name', 'province_id')
            ->where('province_id', 64)
            ->orderBy('id')
            ->get();

        return $this->ok('Berhasil get data kabupaten/kota', $data);
    }

    public function kecamatan(Request $request)
    {
        $query = Kecamatan::select('id', 'regency_id', 'name')->orderBy('name');

        if ($request->filled('kabkota_id')) {
            $query->where('regency_id', $request->kabkota_id);
        }

        return $this->ok('Berhasil get data kecamatan', $query->get());
    }

    public function pendidikan()
    {
        $data = Pendidikan::select('id', 'kode', 'name')->orderBy('id')->get();

        return $this->ok('Berhasil get data pendidikan', $data);
    }

    public function jurusan(Request $request)
    {
        $query = Jurusan::select('id', 'nama', 'id_pendidikans')->orderBy('nama');

        if ($request->filled('id_pendidikan') || $request->filled('pendidikan_id')) {
            $id = $request->input('id_pendidikan', $request->input('pendidikan_id'));
            $pendidikan = Pendidikan::where('id', $id)->orWhere('kode', $id)->first();
            if ($pendidikan) {
                $kode = (string) $pendidikan->kode;
                $query->where(function ($q) use ($pendidikan, $kode) {
                    $q->where('id_pendidikans', $pendidikan->id)
                        ->orWhere('id_pendidikans', $kode)
                        ->orWhere('id_pendidikans', substr($kode, 0, max(strlen($kode) - 3, 1)));
                });
            }
        }

        return $this->ok('Berhasil get data jurusan', $query->get());
    }

    public function statusPerkawinan()
    {
        $data = DB::table('etam_marital')->select('id', 'name')->orderBy('id')->get();

        return $this->ok('Berhasil get data status perkawinan', $data);
    }

    public function disabilitas()
    {
        return $this->ok('Berhasil get data status disabilitas', [
            ['id' => '0', 'name' => 'Tidak'],
            ['id' => '1', 'name' => 'Ya'],
        ]);
    }

    public function jenisDisabilitas()
    {
        $data = DB::table('etam_jenis_disabilitas')
            ->select('id', 'nama_disabilitas as name')
            ->whereNull('deleted_at')
            ->orderBy('nama_disabilitas')
            ->get();

        return $this->ok('Berhasil get data jenis disabilitas', $data);
    }

    public function jabatanHarapan()
    {
        $data = Jabatan::active()->ordered()->select('id', 'nama')->get();

        return $this->ok('Berhasil get data jabatan harapan', $data);
    }

    private function ok(string $message, $data)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
