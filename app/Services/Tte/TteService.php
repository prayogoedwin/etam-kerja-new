<?php

namespace App\Services\Tte;

use App\Models\EtamStruktur;
use App\Models\EtamTte;
use App\Models\EtamTteLog;
use App\Models\User;
use App\Services\Blk\BlkSertifikatKelulusanService;
use App\Services\Pengawasan\SuketDokumenTteService;
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

    public function isSuperAdmin(?User $user = null): bool
    {
        return (bool) ($user ?? Auth::user())?->hasRole('super-admin');
    }

    /**
     * @return array<int, string>
     */
    public function daftarModul(?User $user = null): array
    {
        return $this->visibleQuery($user ?? Auth::user())
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

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        $kode = $user->kode_struktur ? (string) $user->kode_struktur : null;

        return $query->where(function (Builder $q) use ($user, $kode) {
            $q->where('user_id_tujuan', $user->id);

            if ($user->hasRole('kepala-balai') && $kode) {
                $q->orWhere(function (Builder $inner) use ($kode) {
                    $inner->whereNull('user_id_tujuan')
                        ->where('id_struktur', $kode);
                });
            }

            if ($user->adalahKepalaDinas()) {
                $q->orWhere('id_struktur', EtamStruktur::KODE_DINAS);
                $q->orWhere(function (Builder $inner) {
                    $inner->whereNull('user_id_tujuan')
                        ->whereNull('id_struktur');
                });
            }
        });
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

        $this->handlerFor($tte->nama_modul)?->siapkanBerkasDs($tte);
        $tte->refresh();

        if (! config('tte.ds.demo', true) && ! $tte->kode_ds) {
            $kirim = $this->kirimDokumenAwal($tte, $nik, $judul);
            $this->simpanKodeDs($tte, $kirim['kode_dokumen'] ?? null);
            if (! $kirim['ok']) {
                $this->catatLog($tte, false, $kirim['kode'], $kirim['pesan']);
                if ($kirim['kode'] === DsClient::GAGAL && ! $tte->kode_ds) {
                    $this->tandaiGagal($tte);
                }

                return $this->item($tte->id, $judul, false, $kirim['kode'], $kirim['pesan']);
            }
        }

        $ds = $this->ds->tandatangani(
            $tte->path_file_before,
            $passphrase,
            $nik,
            $judul,
            $tte->kode_ds
        );

        $this->simpanKodeDs($tte, $ds['kode_dokumen'] ?? null);

        if (! $ds['ok']) {
            $this->catatLog($tte, false, $ds['kode'], $ds['pesan']);
            if ($ds['kode'] === DsClient::GAGAL && ! $tte->kode_ds) {
                $this->tandaiGagal($tte);
            }

            return $this->item($tte->id, $judul, false, $ds['kode'], $ds['pesan']);
        }

        $pdf = is_string($ds['pdf'] ?? null) && $ds['pdf'] !== '' ? $ds['pdf'] : null;
        if ($pdf === null) {
            $selesai = $this->tungguCallback($tte);
            if ($selesai) {
                $this->catatLog($selesai, true, DsClient::OK, 'TTE berhasil (callback DS)');

                return $this->item($tte->id, $judul, true, DsClient::OK, 'Berhasil ditandatangani');
            }

            if ($tte->kode_ds) {
                $unduh = $this->ds->unduhDokumen((string) $tte->kode_ds);
                if ($unduh['ok'] && is_string($unduh['pdf'] ?? null) && $unduh['pdf'] !== '') {
                    $pdf = $unduh['pdf'];
                }
            }

            if ($pdf === null) {
                $this->catatLog($tte, true, DsClient::OK, 'TTD DS berhasil, berkas belum terunduh. Kode DS: '.(string) $tte->kode_ds);

                return $this->item($tte->id, $judul, true, DsClient::OK, 'TTD di DS berhasil. Berkas belum terunduh; super admin dapat tarik berkas.');
            }
        }

        try {
            $this->selesaikanDenganPdf($tte, $pdf, Auth::id());
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

    /**
     * @return array{ok: bool, pesan: string}
     */
    public function terimaBerkasDs(string $kodeDs, string $pdf): array
    {
        $tte = EtamTte::query()->where('kode_ds', $kodeDs)->latest('id')->first();
        if (! $tte) {
            return ['ok' => false, 'pesan' => 'Kode dokumen tidak dikenali'];
        }

        try {
            $this->selesaikanDenganPdf($tte, $pdf, $tte->user_id_tte ?: $tte->user_id_tujuan);
        } catch (\Throwable $e) {
            Log::error('DS callback terima gagal', [
                'kode_ds' => $kodeDs,
                'tte_id' => $tte->id,
                'message' => $e->getMessage(),
            ]);

            return ['ok' => false, 'pesan' => 'Gagal menyimpan berkas TTE'];
        }

        $this->catatLog($tte->fresh(), true, DsClient::OK, 'Berkas TTE diterima dari callback DS', 'callback_terima');

        return ['ok' => true, 'pesan' => 'Berhasil'];
    }

    /**
     * @return array{ok: bool, pesan: string}
     */
    public function tolakBerkasDs(string $kodeDs, string $alasan = ''): array
    {
        $tte = EtamTte::query()->where('kode_ds', $kodeDs)->latest('id')->first();
        if (! $tte) {
            return ['ok' => false, 'pesan' => 'Kode dokumen tidak dikenali'];
        }
        if ((int) $tte->status === EtamTte::STATUS_BERHASIL) {
            return ['ok' => false, 'pesan' => 'Dokumen sudah ditandatangani'];
        }

        $pesan = $alasan !== '' ? $alasan : 'Ditolak oleh Digital Signature';
        $this->tandaiGagal($tte);
        $this->catatLog($tte, false, DsClient::GAGAL, $pesan, 'callback_tolak');

        return ['ok' => true, 'pesan' => 'Berhasil'];
    }

    public function selesaikanDenganPdf(EtamTte $tte, string $pdf, ?int $userId = null): void
    {
        DB::transaction(function () use ($tte, $pdf, $userId) {
            $tte = EtamTte::query()->where('id', $tte->id)->lockForUpdate()->first();
            if (! $tte) {
                throw new \RuntimeException('Dokumen TTE tidak ditemukan');
            }

            $pathAfter = $this->pathFileAfter($tte);
            Storage::disk('local')->put($pathAfter, $pdf);

            $sudahBerhasil = (int) $tte->status === EtamTte::STATUS_BERHASIL;
            $tte->path_file_after = $pathAfter;
            $tte->status = EtamTte::STATUS_BERHASIL;
            if ($userId && ! $tte->user_id_tte) {
                $tte->user_id_tte = $userId;
            }
            if ($userId) {
                $tte->updated_by = $userId;
            }
            $tte->save();

            if ($sudahBerhasil) {
                return;
            }

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
            if (Schema::hasColumn($model->getTable(), 'updated_by') && $userId) {
                $model->setAttribute('updated_by', $userId);
            }
            $model->save();

            $handler->setelahBerhasil($tte->fresh());
        });
    }

    /**
     * @return array{ok: bool, kode: string, pesan: string}
     */
    private function kirimDokumenAwal(EtamTte $tte, string $nik, string $judul): array
    {
        $isi = $tte->path_file_before ? Storage::disk('local')->get($tte->path_file_before) : null;
        if (! is_string($isi) || $isi === '') {
            return [
                'ok' => false,
                'kode' => DsClient::GAGAL,
                'pesan' => 'Berkas PDF sumber tidak ditemukan',
            ];
        }

        $kirim = $this->ds->kirimDokumen($isi, $nik, $judul);
        $this->simpanKodeDs($tte, $kirim['kode_dokumen'] ?? null);

        return $kirim;
    }

    /**
     * @return array{ok: bool, kode: string, message: string}
     */
    public function tarikBerkas(int $id): array
    {
        $tte = $this->visibleQuery(Auth::user())->where('id', $id)->first();
        if (! $tte) {
            return ['ok' => false, 'kode' => DsClient::GAGAL, 'message' => 'Dokumen TTE tidak ditemukan'];
        }

        $judul = $this->judulUntuk($tte);
        if (! $tte->kode_ds) {
            return ['ok' => false, 'kode' => DsClient::GAGAL, 'message' => 'Dokumen belum punya kode DS. Kirim ulang TTD dulu.'];
        }

        if (
            (int) $tte->status === EtamTte::STATUS_BERHASIL
            && $tte->path_file_after
            && Storage::disk('local')->exists($tte->path_file_after)
        ) {
            return ['ok' => true, 'kode' => DsClient::OK, 'message' => 'Berkas sudah ada'];
        }

        $unduh = $this->ds->unduhDokumen((string) $tte->kode_ds);
        $pdf = is_string($unduh['pdf'] ?? null) && $unduh['pdf'] !== '' ? $unduh['pdf'] : null;
        if (! $unduh['ok'] || $pdf === null) {
            $this->catatLog($tte, false, $unduh['kode'] ?? DsClient::GAGAL, $unduh['pesan'] ?? 'Gagal unduh berkas DS', 'tarik');

            return [
                'ok' => false,
                'kode' => $unduh['kode'] ?? DsClient::GAGAL,
                'message' => $unduh['pesan'] ?? 'Gagal tarik berkas dari DS',
            ];
        }

        try {
            $this->selesaikanDenganPdf($tte, $pdf, Auth::id());
        } catch (\Throwable $e) {
            Log::error('TTE tarik berkas gagal', [
                'tte_id' => $tte->id,
                'kode_ds' => $tte->kode_ds,
                'message' => $e->getMessage(),
            ]);
            $this->catatLog($tte, false, DsClient::GAGAL, $e->getMessage(), 'tarik');

            return ['ok' => false, 'kode' => DsClient::GAGAL, 'message' => 'Berkas DS didapat, tetapi gagal disimpan'];
        }

        $this->catatLog($tte->fresh() ?? $tte, true, DsClient::OK, 'Berkas ditarik dari DS ('.$judul.')', 'tarik');

        return ['ok' => true, 'kode' => DsClient::OK, 'message' => 'Berkas berhasil ditarik dari DS'];
    }

    private function simpanKodeDs(EtamTte $tte, mixed $kode): void
    {
        if (! is_string($kode) && ! is_int($kode) && ! is_float($kode)) {
            return;
        }

        $kode = trim((string) $kode);
        if ($kode === '' || $tte->kode_ds === $kode) {
            return;
        }

        $tte->kode_ds = $kode;
        $tte->save();
    }

    private function tungguCallback(EtamTte $tte): ?EtamTte
    {
        $detik = max(0, (int) config('tte.ds.callback_wait', 8));
        $deadline = microtime(true) + $detik;

        do {
            $fresh = $tte->fresh();
            if ($fresh && (int) $fresh->status === EtamTte::STATUS_BERHASIL && $fresh->path_file_after) {
                return $fresh;
            }
            if ($detik === 0) {
                break;
            }
            usleep(250000);
        } while (microtime(true) < $deadline);

        return null;
    }

    private function tandaiGagal(EtamTte $tte): void
    {
        $tte->status = EtamTte::STATUS_GAGAL;
        $tte->updated_by = Auth::id();
        $tte->save();
        $this->handlerFor($tte->nama_modul)?->setelahGagal($tte);
    }

    private function catatLog(EtamTte $tte, bool $ok, string $kode, string $pesan, string $aksi = 'tandatangani'): void
    {
        EtamTteLog::create([
            'etam_tte_id' => $tte->id,
            'user_id' => Auth::id(),
            'aksi' => $aksi,
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

    private function pathFileAfter(EtamTte $tte): string
    {
        if ($tte->nama_modul === 'blk') {
            return app(BlkSertifikatKelulusanService::class)->pathDokumenTte(
                (int) $tte->id_refrensi,
                (string) ($tte->kode_dokumen ?: ''),
                true
            );
        }

        if ($tte->nama_modul === 'suket_k3') {
            return app(SuketDokumenTteService::class)->pathDokumenTte(
                (int) $tte->id_refrensi,
                (string) ($tte->kode_dokumen ?: ''),
                true
            );
        }

        return 'tte/after/'.$tte->id.'.pdf';
    }

    private function roleName(?User $user): ?string
    {
        return $user?->roles[0]['name'] ?? null;
    }
}
