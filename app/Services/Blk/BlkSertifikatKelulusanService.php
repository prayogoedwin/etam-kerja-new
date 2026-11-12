<?php

namespace App\Services\Blk;

use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\EtamStruktur;
use App\Models\EtamTte;
use App\Models\Kabkota;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BlkSertifikatKelulusanService
{
    public const VERSI_DRAFT = 'draft';

    public const VERSI_TTE = 'tte';

    public const ROLE_KEPALA_DINAS = 'eksekutif-provinsi';

    public const ROLE_KEPALA_UPTD = 'kepala-balai';

    /**
     * @return array<string, mixed>
     */
    public function build(EtamBlkPelatihan $pelatihan, EtamBlkPelatihanPeserta $peserta, string $versi = self::VERSI_DRAFT): array
    {
        $pelatihan->loadMissing(['blk', 'kompetensiInti', 'kompetensiPenunjang']);
        $blk = $pelatihan->blk;
        $strukturBalai = $this->strukturBalai($blk);
        $kotaBalai = $this->kotaBalai($blk, $strukturBalai);
        $tanggalTtdDinas = $pelatihan->tanggal_ttd_dinas ?: $pelatihan->tanggal_pelaksanaan_selesai ?: now();
        $tanggalTtdUptd = $pelatihan->tanggal_ttd_uptd ?: $pelatihan->tanggal_pelaksanaan_selesai ?: now();

        $this->ensureNomorSertifikat($peserta, $pelatihan, $tanggalTtdDinas);

        $statusTtd = (int) $peserta->status_ttd;
        $signedKompetensi = $statusTtd >= EtamBlkPelatihanPeserta::TTD_TTD_UPTD;
        $signedSertifikat = $statusTtd >= EtamBlkPelatihanPeserta::TTD_TTD_DINAS;
        if ($versi === self::VERSI_TTE) {
            $signedKompetensi = true;
            $signedSertifikat = true;
        }

        return [
            'versi' => $versi === self::VERSI_TTE ? self::VERSI_TTE : self::VERSI_DRAFT,
            'is_draft' => ! $signedSertifikat,
            'signed' => [
                'sertifikat' => $signedSertifikat,
                'kompetensi' => $signedKompetensi,
            ],
            'logo_kaltim' => asset('assets/etam_fe/images/logo/logo_kaltim.png'),
            'logo_bsre' => asset('assets/etam_be/images/logo/logo_bsre.png'),
            'bsre_teks' => "Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik\nyang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).",
            'nomor' => $peserta->nomor_sertifikat,
            'peserta' => [
                'nama' => $peserta->name ?: '-',
                'nik' => $peserta->ktp ?: '-',
                'ttl' => $this->formatTtl($peserta),
            ],
            'pelatihan' => [
                'nama' => $pelatihan->nama_pelatihan ?: '-',
                'kejuruan' => trim((string) $pelatihan->kejuruan),
                'angkatan' => trim((string) $pelatihan->angkatan),
                'jam' => $pelatihan->jam_pelatihan ? (int) $pelatihan->jam_pelatihan : null,
                'jam_terbilang' => $pelatihan->jam_pelatihan ? $this->terbilang((int) $pelatihan->jam_pelatihan) : null,
                'tanggal_mulai' => $this->tanggalIndo($pelatihan->tanggal_pelaksanaan),
                'tanggal_selesai' => $this->tanggalIndo($pelatihan->tanggal_pelaksanaan_selesai),
                'uraian' => $this->uraianKelulusan($pelatihan),
            ],
            'kop' => [
                'pemerintah' => 'PEMERINTAH PROVINSI KALIMANTAN TIMUR',
                'dinas' => 'DINAS TENAGA KERJA DAN TRANSMIGRASI',
                'uptd' => Str::upper($strukturBalai?->nama ?: ($blk->nama_lembaga ?? '')),
                'alamat' => $this->barisAlamat($strukturBalai, $blk),
            ],
            'kompetensi_inti' => $pelatihan->kompetensiInti->values(),
            'kompetensi_penunjang' => $pelatihan->kompetensiPenunjang->values(),
            'penandatangan' => [
                'dinas' => [
                    'tempat' => 'Samarinda',
                    'tanggal' => $this->tanggalIndo($tanggalTtdDinas),
                    'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi',
                    'jabatan' => 'Kepala,',
                    'nama' => $this->namaPenandatangan(self::ROLE_KEPALA_DINAS),
                ],
                'balai' => [
                    'tempat' => $kotaBalai,
                    'tanggal' => $this->tanggalIndo($tanggalTtdUptd),
                    'instansi' => $this->labelInstansiBalai($strukturBalai, $blk),
                    'jabatan' => 'Kepala,',
                    'nama' => $this->namaPenandatangan(self::ROLE_KEPALA_UPTD, $blk?->kode_struktur),
                ],
            ],
        ];
    }

    public function pathDokumenTte(int $pesertaId, string $kodeDokumen, bool $sudahTte = false, ?Carbon $waktu = null): string
    {
        $waktu ??= now();
        $nama = $kodeDokumen === EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT
            ? 'sertifikat_tte_'.$pesertaId.'.pdf'
            : 'uji_kompetensi_tte_'.$pesertaId.'.pdf';

        return sprintf(
            'tte/%s/blk/%s/%s/%s',
            $sudahTte ? 'after' : 'before',
            $waktu->format('Y'),
            $waktu->format('m'),
            $nama
        );
    }

    public function simpanDokumenTte(EtamBlkPelatihanPeserta $peserta, string $kodeDokumen, bool $sudahTte = false): string
    {
        $path = $this->pathDokumenTte($peserta->id, $kodeDokumen, $sudahTte);
        Storage::disk('local')->put($path, $this->buatPdfDokumen($peserta, $kodeDokumen));

        return $path;
    }

    public function pdfUntukDs(EtamBlkPelatihanPeserta $peserta, string $kodeDokumen): string
    {
        $pdf = $this->buatPdfDokumen($peserta, $kodeDokumen);

        $tte = EtamTte::query()
            ->where('nama_modul', 'blk')
            ->where('id_refrensi', $peserta->id)
            ->where('kode_dokumen', $kodeDokumen)
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->orderByDesc('id')
            ->first();

        if ($tte) {
            $path = $tte->path_file_before ?: $this->pathDokumenTte($peserta->id, $kodeDokumen, false);
            Storage::disk('local')->put($path, $pdf);
            if ($tte->path_file_before !== $path) {
                $tte->path_file_before = $path;
                $tte->save();
            }
        }

        return $pdf;
    }

    public function buatPdfDokumen(EtamBlkPelatihanPeserta $peserta, string $kodeDokumen): string
    {
        $peserta->loadMissing(['pelatihan.blk', 'pelatihan.kompetensiInti', 'pelatihan.kompetensiPenunjang']);
        $pelatihan = $peserta->pelatihan;
        if (! $pelatihan) {
            throw new \RuntimeException('Peserta BLK tidak memiliki pelatihan');
        }

        $sertifikat = $this->build($pelatihan, $peserta);
        $sertifikat['logo_kaltim'] = $this->dataUri(public_path('assets/etam_fe/images/logo/logo_kaltim.png')) ?: $sertifikat['logo_kaltim'];
        $sertifikat['logo_bsre'] = $this->dataUri(public_path('assets/etam_be/images/logo/logo_bsre.png')) ?: $sertifikat['logo_bsre'];
        $sertifikat['signed']['sertifikat'] = false;
        $sertifikat['signed']['kompetensi'] = false;
        $sertifikat['is_draft'] = false;

        $html = view('backend.blk.pelatihan.sertifikat', [
            'pelatihan' => $pelatihan,
            'peserta' => $peserta,
            'sertifikat' => $sertifikat,
            'halaman' => $kodeDokumen,
            'modeUnduh' => true,
            'untukDs' => true,
            'tampilanAdmin' => false,
            'urlVerifikasi' => null,
            'qrVerifikasi' => null,
        ])->render();

        return $this->htmlKePdf($html);
    }

    public function unduhPdf(EtamBlkPelatihanPeserta $peserta, string $kode): StreamedResponse
    {
        if (! in_array($kode, [
            EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT,
            EtamBlkPelatihanPeserta::TTE_DOK_KOMPETENSI,
        ], true)) {
            abort(404);
        }

        $path = $this->pathBerkasTte($peserta, $kode);
        if ($path === null) {
            abort(404, 'Berkas TTE belum tersedia');
        }

        $nama = $kode === EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT
            ? '1-sertifikat-kelulusan.pdf'
            : '2-daftar-unit-kompetensi.pdf';

        return Storage::disk('local')->download($path, $nama, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function pathBerkasTte(EtamBlkPelatihanPeserta $peserta, string $kode): ?string
    {
        $path = EtamTte::query()
            ->where('nama_modul', 'blk')
            ->where('id_refrensi', $peserta->id)
            ->where('kode_dokumen', $kode)
            ->where('status', EtamTte::STATUS_BERHASIL)
            ->orderByDesc('id')
            ->value('path_file_after');

        if (is_string($path) && $path !== '' && Storage::disk('local')->exists($path)) {
            return $path;
        }

        foreach ([
            'tte/blk/'.$peserta->id.'/'.$kode.'.pdf',
            $this->pathDokumenTte($peserta->id, $kode, true),
        ] as $legacy) {
            if (Storage::disk('local')->exists($legacy)) {
                return $legacy;
            }
        }

        return null;
    }

    private function htmlKePdf(string $html): string
    {
        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOption('defaultMediaType', 'print')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->output();
    }

    private function dataUri(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }

    private function strukturBalai(?EtamBlk $blk): ?EtamStruktur
    {
        if (! $blk?->kode_struktur) {
            return null;
        }

        return EtamStruktur::query()
            ->where('kode_bidang', $blk->kode_struktur)
            ->orderByDesc('id')
            ->first();
    }

    private function kotaBalai(?EtamBlk $blk, ?EtamStruktur $struktur): string
    {
        $namaStruktur = (string) ($struktur?->nama ?? $blk?->nama_lembaga ?? '');
        if (Str::contains(Str::lower($namaStruktur), 'balikpapan')) {
            return 'Balikpapan';
        }
        if (Str::contains(Str::lower($namaStruktur), 'bontang')) {
            return 'Bontang';
        }

        if ($blk?->kabkota_id) {
            $kota = Kabkota::query()->find($blk->kabkota_id);

            return $kota?->name ? trim(str_ireplace(['Kota ', 'Kabupaten '], '', $kota->name)) : 'Samarinda';
        }

        return 'Samarinda';
    }

    private function labelInstansiBalai(?EtamStruktur $struktur, ?EtamBlk $blk): string
    {
        $nama = trim((string) ($struktur?->nama ?: $blk?->nama_lembaga ?: 'UPTD Balai Latihan Kerja Industri'));
        $nama = preg_replace('/^UPTD\.\s*/i', 'UPTD ', $nama) ?: $nama;

        return $nama;
    }

    private function namaPenandatangan(string $role, ?string $kodeStruktur = null): string
    {
        $query = User::role($role)->orderByDesc('users.id');
        if ($role === self::ROLE_KEPALA_DINAS && ! $kodeStruktur) {
            $kodeStruktur = EtamStruktur::KODE_DINAS;
        }
        if ($kodeStruktur) {
            $query->where('users.kode_struktur', $kodeStruktur);
        }

        return trim((string) ($query->first()?->name ?? ''));
    }

    private function ensureNomorSertifikat(EtamBlkPelatihanPeserta $peserta, EtamBlkPelatihan $pelatihan, Carbon $tanggalTerbit): void
    {
        if (filled($peserta->nomor_sertifikat)) {
            return;
        }

        $roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][(int) $tanggalTerbit->format('n') - 1];
        $tahun = $tanggalTerbit->format('y');
        $kode = preg_replace('/\D+/', '', (string) ($pelatihan->blk?->kode_struktur ?: $pelatihan->blk_id)) ?: '0';
        $urut = EtamBlkPelatihanPeserta::query()
            ->whereNotNull('nomor_sertifikat')
            ->where('nomor_sertifikat', 'like', '%/'.$roman.'.'.$tahun.'/%')
            ->count() + 1;

        $peserta->nomor_sertifikat = sprintf('%04d/%s.%s/%s', $urut, $roman, $tahun, $kode);
        $peserta->save();
    }

    private function uraianKelulusan(EtamBlkPelatihan $pelatihan): string
    {
        $bagian = ['telah menyelesaikan Pelatihan Vokasi Program Pelatihan', $pelatihan->nama_pelatihan ?: '-'];
        if (filled($pelatihan->angkatan)) {
            $bagian[] = 'Angkatan '.$pelatihan->angkatan;
        }
        if (filled($pelatihan->kejuruan)) {
            $bagian[] = 'Kejuruan '.$pelatihan->kejuruan;
        }

        $kalimat = implode(' ', $bagian);
        $kalimat .= ' yang dilaksanakan pada tanggal '.$this->tanggalIndo($pelatihan->tanggal_pelaksanaan);
        $kalimat .= ' sampai dengan '.$this->tanggalIndo($pelatihan->tanggal_pelaksanaan_selesai);

        if ($pelatihan->jam_pelatihan) {
            $jam = (int) $pelatihan->jam_pelatihan;
            $kalimat .= ' selama '.$jam.' ('.$this->terbilang($jam).') Jam Pelatihan';
        }

        return $kalimat.' dan dinyatakan LULUS.';
    }

    private function formatTtl(EtamBlkPelatihanPeserta $peserta): string
    {
        $tempat = trim((string) $peserta->tempat_lahir);
        $tanggal = $this->tanggalIndo($peserta->tanggal_lahir);
        if ($tempat === '' && $tanggal === '-') {
            return '-';
        }
        if ($tempat === '') {
            return $tanggal;
        }

        return $tempat.', '.$tanggal;
    }

    private function barisAlamat(?EtamStruktur $struktur, ?EtamBlk $blk): string
    {
        if (filled($struktur?->alamat)) {
            return trim((string) $struktur->alamat);
        }

        $parts = array_filter([
            $blk?->alamat_lengkap,
            $blk?->telepon ? 'Telepon/Faksimile '.$blk->telepon : null,
            $blk?->email ? 'Pos-el '.$blk->email : null,
        ]);

        return implode('; ', $parts);
    }

    private function tanggalIndo(mixed $date): string
    {
        if (! $date) {
            return '-';
        }

        return tgl_indo($date);
    }

    private function terbilang(int $angka): string
    {
        $angka = abs($angka);
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($angka < 12) {
            return $huruf[$angka];
        }
        if ($angka < 20) {
            return $this->terbilang($angka - 10).' belas';
        }
        if ($angka < 100) {
            return trim($this->terbilang(intdiv($angka, 10)).' puluh '.$this->terbilang($angka % 10));
        }
        if ($angka < 200) {
            return trim('seratus '.$this->terbilang($angka - 100));
        }
        if ($angka < 1000) {
            return trim($this->terbilang(intdiv($angka, 100)).' ratus '.$this->terbilang($angka % 100));
        }
        if ($angka < 2000) {
            return trim('seribu '.$this->terbilang($angka - 1000));
        }
        if ($angka < 1000000) {
            return trim($this->terbilang(intdiv($angka, 1000)).' ribu '.$this->terbilang($angka % 1000));
        }

        return (string) $angka;
    }
}
