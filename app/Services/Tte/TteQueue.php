<?php

namespace App\Services\Tte;

use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\EtamTte;
use App\Services\Blk\BlkSertifikatKelulusanService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use InvalidArgumentException;

class TteQueue
{
    public function __construct(private BlkSertifikatKelulusanService $sertifikat) {}

    /**
     * @param  array{
     *     nama_modul: string,
     *     nama_table: string,
     *     id_refrensi: int,
     *     kolom_status_refrensi: string,
     *     id_struktur?: string|null,
     *     kode_dokumen?: string|null,
     *     path_file_before?: string|null,
     *     token_verifikasi?: string|null,
     *     judul?: string|null
     * }  $data
     */
    public function ajukan(array $data): EtamTte
    {
        foreach (['nama_modul', 'nama_table', 'id_refrensi', 'kolom_status_refrensi'] as $wajib) {
            if (! isset($data[$wajib]) || $data[$wajib] === '' || $data[$wajib] === null) {
                throw new InvalidArgumentException('Field TTE wajib: '.$wajib);
            }
        }

        $idStruktur = $data['id_struktur'] ?? null;
        if ($idStruktur === '') {
            $idStruktur = null;
        }
        $kodeDokumen = $data['kode_dokumen'] ?? null;
        if ($kodeDokumen === '') {
            $kodeDokumen = null;
        }

        $existing = EtamTte::query()
            ->where('nama_modul', $data['nama_modul'])
            ->where('nama_table', $data['nama_table'])
            ->where('id_refrensi', (int) $data['id_refrensi'])
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->when(
                $idStruktur === null,
                fn ($q) => $q->whereNull('id_struktur'),
                fn ($q) => $q->where('id_struktur', (string) $idStruktur)
            )
            ->when(
                $kodeDokumen === null,
                fn ($q) => $q->where(fn ($inner) => $inner->whereNull('kode_dokumen')->orWhere('kode_dokumen', '')),
                fn ($q) => $q->where('kode_dokumen', $kodeDokumen)
            )
            ->first();

        if ($existing) {
            return $existing;
        }

        return EtamTte::create([
            'nama_modul' => $data['nama_modul'],
            'nama_table' => $data['nama_table'],
            'id_refrensi' => (int) $data['id_refrensi'],
            'kolom_status_refrensi' => $data['kolom_status_refrensi'],
            'id_struktur' => $idStruktur,
            'kode_dokumen' => $kodeDokumen,
            'judul' => $data['judul'] ?? null,
            'path_file_before' => $data['path_file_before'] ?? null,
            'token_verifikasi' => $data['token_verifikasi'] ?? Str::lower(Str::random(40)),
            'status' => EtamTte::STATUS_MENUNGGU,
            'created_by' => Auth::id(),
        ]);
    }

    public function ajukanBlkUptd(EtamBlkPelatihanPeserta $peserta): EtamTte
    {
        $peserta->loadMissing('pelatihan.blk');

        $kodeStruktur = $peserta->pelatihan?->blk?->kode_struktur;
        if (! $kodeStruktur) {
            throw new InvalidArgumentException('BLK peserta tidak memiliki kode_struktur untuk antrean TTE Kepala Balai');
        }

        $tte = $this->ajukan([
            'nama_modul' => 'blk',
            'nama_table' => $peserta->getTable(),
            'id_refrensi' => $peserta->id,
            'kolom_status_refrensi' => 'status_ttd',
            'id_struktur' => (string) $kodeStruktur,
            'kode_dokumen' => EtamBlkPelatihanPeserta::TTE_DOK_KOMPETENSI,
            'judul' => 'Daftar Unit Kompetensi — '.$peserta->name,
        ]);
        if (! $tte->token_verifikasi) {
            $tte->token_verifikasi = Str::lower(Str::random(40));
        }
        $tte->path_file_before = $this->sertifikat->simpanDokumenTte(
            $peserta,
            EtamBlkPelatihanPeserta::TTE_DOK_KOMPETENSI,
            false
        );
        $tte->save();

        return $tte;
    }

    public function batalkanMenunggu(string $modul, string $table, int $idRefrensi): void
    {
        EtamTte::query()
            ->where('nama_modul', $modul)
            ->where('nama_table', $table)
            ->where('id_refrensi', $idRefrensi)
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->get()
            ->each(function (EtamTte $row): void {
                $row->deleted_by = Auth::id();
                $row->save();
                $row->delete();
            });
    }
}
