<?php

namespace App\Services\Tte;

use App\Models\EtamTte;
use App\Models\EtamTteLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TteService
{
    public function __construct(private DsClient $ds) {}

    public function canAccess(?User $user): bool
    {
        return (bool) $user?->bisaAksesTte();
    }

    public function isKepalaDinas(?User $user = null): bool
    {
        return (bool) ($user ?? Auth::user())?->adalahKepalaDinas();
    }

    /**
     * @return array<int, string>
     */
    public function daftarModul(): array
    {
        return EtamTte::query()
            ->select('nama_modul')
            ->whereNotNull('nama_modul')
            ->where('nama_modul', '!=', '')
            ->distinct()
            ->orderBy('nama_modul')
            ->pluck('nama_modul')
            ->all();
    }

    public function visibleQuery(?User $user): Builder
    {
        $query = EtamTte::query();
        $role = $this->roleName($user);

        if ($role === 'super-admin') {
            return $query;
        }

        if ($user?->adalahKepalaDinas()) {
            return $query->whereNull('id_struktur');
        }

        if ($role === 'kepala-balai') {
            $kode = $user?->kode_struktur;
            if (! $kode) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('id_struktur', (string) $kode);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * @param  array<int, int>  $ids
     * @return array{
     *     count: int,
     *     berhasil: int,
     *     gagal: int,
     *     kode: string|null,
     *     message: string,
     *     items: array<int, array{id: int, judul: string, ok: bool, kode: string, pesan: string}>
     * }
     */
    public function tandatangani(array $ids, bool $pilihSemua = false, ?string $filterModul = null, string $passphrase = ''): array
    {
        if (trim($passphrase) === '') {
            return [
                'count' => 0,
                'berhasil' => 0,
                'gagal' => 0,
                'kode' => DsClient::PASSPHRASE_SALAH,
                'message' => 'Passphrase wajib diisi',
                'items' => [],
            ];
        }

        $nik = Auth::user()?->nikSiapTte();
        if ($nik === null) {
            return [
                'count' => 0,
                'berhasil' => 0,
                'gagal' => 0,
                'kode' => DsClient::NIK_KOSONG,
                'message' => 'NIK penandatangan belum diisi. Lengkapi NIK di kelola admin sebelum TTE.',
                'items' => [],
            ];
        }

        $query = $this->visibleQuery(Auth::user())
            ->where('status', EtamTte::STATUS_MENUNGGU);
        $this->applyFilterModul($query, $filterModul);

        if (! $pilihSemua) {
            $query->whereIn('id', $ids);
        }

        $tteIds = $query->pluck('id')->all();
        $items = [];
        $berhasil = 0;
        $gagal = 0;
        $kodeBatch = null;

        foreach ($tteIds as $id) {
            $hasil = $this->tandatanganiSatu((int) $id, $passphrase, $nik);
            $items[] = $hasil;
            if ($hasil['ok']) {
                $berhasil++;
            } else {
                $gagal++;
                $kodeBatch = $kodeBatch ?? $hasil['kode'];
            }

            if (in_array($hasil['kode'], [DsClient::PASSPHRASE_SALAH, DsClient::KONEKSI_PUTUS], true)) {
                break;
            }
        }

        $count = $berhasil + $gagal;
        $pesan = $this->pesanRingkasan($berhasil, $gagal, $kodeBatch);

        return [
            'count' => $count,
            'berhasil' => $berhasil,
            'gagal' => $gagal,
            'kode' => $kodeBatch,
            'message' => $pesan,
            'items' => $items,
        ];
    }

    public function handlerFor(string $modul): ?TteModuleHandler
    {
        $class = config('tte.modules.'.$modul);
        if (! is_string($class) || ! class_exists($class)) {
            return null;
        }

        $handler = app($class);

        return $handler instanceof TteModuleHandler ? $handler : null;
    }

    public function judulUntuk(EtamTte $tte): string
    {
        if ($tte->judul) {
            return $tte->judul;
        }

        $handler = $this->handlerFor($tte->nama_modul);

        return $handler?->judul($tte) ?? ($tte->nama_modul.' #'.$tte->id_refrensi);
    }

    /**
     * @return array{id: int, judul: string, ok: bool, kode: string, pesan: string}
     */
    private function tandatanganiSatu(int $id, string $passphrase, string $nik): array
    {
        $tte = $this->visibleQuery(Auth::user())
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->where('id', $id)
            ->first();

        $judul = $tte ? $this->judulUntuk($tte) : '#'.$id;
        if (! $tte) {
            return $this->item($id, $judul, false, DsClient::GAGAL, 'Dokumen tidak ada dalam antrean Anda');
        }

        $ds = $this->ds->tandatangani(
            $tte->path_file_before,
            $passphrase,
            $nik,
            $judul,
            $tte->kode_ds
        );

        if (! empty($ds['kode_dokumen']) && $tte->kode_ds !== $ds['kode_dokumen']) {
            $tte->kode_ds = $ds['kode_dokumen'];
            $tte->save();
        }

        if (! $ds['ok']) {
            $this->catatLog($tte, false, $ds['kode'], $ds['pesan']);
            if ($ds['kode'] === DsClient::GAGAL) {
                $this->tandaiGagal($tte);
            }

            return $this->item($tte->id, $judul, false, $ds['kode'], $ds['pesan']);
        }

        try {
            DB::transaction(function () use ($tte, $ds) {
                $tte = $this->visibleQuery(Auth::user())
                    ->where('status', EtamTte::STATUS_MENUNGGU)
                    ->where('id', $tte->id)
                    ->lockForUpdate()
                    ->first();

                if (! $tte) {
                    throw new \RuntimeException('Dokumen TTE tidak lagi menunggu');
                }

                $pathAfter = 'tte/after/'.$tte->id.'.pdf';
                Storage::disk('local')->put($pathAfter, (string) $ds['pdf']);

                $tte->path_file_after = $pathAfter;
                $tte->status = EtamTte::STATUS_BERHASIL;
                $tte->user_id_tte = Auth::id();
                $tte->updated_by = Auth::id();
                $tte->save();

                $handler = $this->handlerFor($tte->nama_modul);
                if (! $handler) {
                    throw new \RuntimeException('Modul TTE tidak terdaftar');
                }

                $modelClass = $handler->modelClass();
                if (! is_subclass_of($modelClass, Model::class)) {
                    throw new \RuntimeException('Model modul TTE tidak valid');
                }

                /** @var Model|null $model */
                $model = $modelClass::query()->find($tte->id_refrensi);
                $kolom = $tte->kolom_status_refrensi;
                if (
                    ! $model
                    || $model->getTable() !== $tte->nama_table
                    || ! $kolom
                    || ! Schema::hasColumn($model->getTable(), $kolom)
                ) {
                    throw new \RuntimeException('Data sumber TTE tidak valid');
                }

                $model->{$kolom} = $handler->nilaiStatusSukses($tte);
                if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                    $model->setAttribute('updated_by', Auth::id());
                }
                $model->save();

                $handler->setelahBerhasil($tte->fresh());
            });
        } catch (\Throwable $e) {
            Log::error('TTE gagal setelah DS', [
                'tte_id' => $id,
                'message' => $e->getMessage(),
            ]);
            $this->tandaiGagal($tte);
            $this->catatLog($tte, false, DsClient::GAGAL, $e->getMessage());

            return $this->item($tte->id, $judul, false, DsClient::GAGAL, 'TTE DS berhasil, tetapi aksi modul gagal');
        }

        $this->catatLog($tte->fresh(), true, DsClient::OK, 'TTE berhasil');

        return $this->item($tte->id, $judul, true, DsClient::OK, 'Berhasil ditandatangani');
    }

    private function tandaiGagal(EtamTte $tte): void
    {
        $tte->status = EtamTte::STATUS_GAGAL;
        $tte->updated_by = Auth::id();
        $tte->save();
        $this->handlerFor($tte->nama_modul)?->setelahGagal($tte);
    }

    private function catatLog(EtamTte $tte, bool $ok, string $kode, string $pesan): void
    {
        EtamTteLog::create([
            'etam_tte_id' => $tte->id,
            'user_id' => Auth::id(),
            'aksi' => 'tandatangani',
            'kode' => $kode,
            'status' => $ok ? EtamTteLog::STATUS_BERHASIL : EtamTteLog::STATUS_GAGAL,
            'judul_dokumen' => $this->judulUntuk($tte),
            'nama_modul' => $tte->nama_modul,
            'pesan' => $pesan,
        ]);
    }

    /**
     * @return array{id: int, judul: string, ok: bool, kode: string, pesan: string}
     */
    private function item(int $id, string $judul, bool $ok, string $kode, string $pesan): array
    {
        return [
            'id' => $id,
            'judul' => $judul,
            'ok' => $ok,
            'kode' => $kode,
            'pesan' => $pesan,
        ];
    }

    private function pesanRingkasan(int $berhasil, int $gagal, ?string $kode): string
    {
        if ($kode === DsClient::PASSPHRASE_SALAH) {
            return 'Passphrase salah. Tidak ada dokumen yang ditandatangani.';
        }
        if ($kode === DsClient::KONEKSI_PUTUS) {
            return 'Koneksi ke server DS terputus. Proses TTE dihentikan.';
        }
        if ($kode === DsClient::NIK_KOSONG) {
            return 'NIK penandatangan belum diisi. Lengkapi NIK di kelola admin sebelum TTE.';
        }
        if ($berhasil === 0 && $gagal === 0) {
            return 'Dokumen terpilih tidak ada dalam antrean TTE Anda';
        }

        $pesan = $berhasil.' dokumen berhasil ditandatangani';
        if ($gagal > 0) {
            $pesan .= ', '.$gagal.' gagal';
        }

        return $pesan;
    }

    public function applyFilterModul(Builder $query, ?string $filterModul): void
    {
        $modul = is_string($filterModul) ? trim($filterModul) : '';
        if ($modul === '') {
            return;
        }

        $query->where('nama_modul', $modul);
    }

    public function urlBerkas(EtamTte $tte): ?string
    {
        if (! $tte->token_verifikasi) {
            return null;
        }

        return route('tte.berkas', $tte->token_verifikasi);
    }

    private function roleName(?User $user): ?string
    {
        return $user?->roles[0]['name'] ?? null;
    }
}
