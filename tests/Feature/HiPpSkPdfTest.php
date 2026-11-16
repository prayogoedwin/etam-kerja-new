<?php

namespace Tests\Feature;

use App\Models\EtamTte;
use App\Models\HI\PP\EtamHiPpAjuan;
use App\Models\User;
use App\Http\Controllers\HI\PP\AdmBidangPpController;
use App\Services\Tte\TteVerifikasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HiPpSkPdfTest extends TestCase
{
    public function test_data_cetak_memilih_kepala_dinas_terbaru_di_lokasi_64(): void
    {
        $this->actingAs(new User(['name' => 'Petugas Penguji']));
        config(['database.connections.hi_pp_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::setDefaultConnection('hi_pp_test');
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('lokasi_kerja');
            $table->string('kode_struktur');
            $table->softDeletes();
        });
        DB::table('users')->insert([
            ['name' => 'Kepala Dinas Lama', 'lokasi_kerja' => '64', 'kode_struktur' => '99', 'deleted_at' => null],
            ['name' => 'Kepala Dinas Terbaru', 'lokasi_kerja' => '64', 'kode_struktur' => '99', 'deleted_at' => null],
            ['name' => 'Lokasi Lain', 'lokasi_kerja' => '65', 'kode_struktur' => '99', 'deleted_at' => null],
            ['name' => 'Struktur Lain', 'lokasi_kerja' => '64', 'kode_struktur' => '41', 'deleted_at' => null],
            ['name' => 'Tidak Aktif', 'lokasi_kerja' => '64', 'kode_struktur' => '99', 'deleted_at' => now()],
        ]);

        $ajuan = new EtamHiPpAjuan(['tanggal_berlaku_pp_baru' => '2026-11-16']);
        $tte = new EtamTte(['nama_modul' => 'hi_pp', 'token_verifikasi' => 'token-sk-ujian']);
        $method = new \ReflectionMethod(AdmBidangPpController::class, 'dataCetakSk');
        $data = $method->invoke(app(AdmBidangPpController::class), $ajuan, $tte);

        $this->assertSame('Kepala Dinas Terbaru', $data['namaKepalaDinas']);
    }

    #[DataProvider('companyExamples')]
    public function test_sk_pdf_contains_two_pages_and_bsre_footer(string $name, string $address): void
    {
        $this->actingAs(new User(['name' => 'Petugas Penguji']));
        $query = Mockery::mock();
        $query->shouldReceive('where')->once()->with('id', 1)->andReturnSelf();
        $query->shouldReceive('first')->once()->andReturn((object) ['name' => 'Industri']);
        DB::shouldReceive('table')->once()->with('etam_sektor')->andReturn($query);

        $tanggalMulai = Carbon::parse('2026-11-16');
        $ajuan = (object) [
            'jenis_ajuan' => 2,
            'nomor_sk' => '560/123/2026',
            'nomor' => '123/PP/2026',
            'tanggal' => Carbon::parse('2026-11-01'),
            'perusahaan' => (object) [
                'penyedia' => (object) [
                    'name' => $name,
                    'alamat' => $address,
                    'id_sektor' => 1,
                ],
            ],
        ];
        $logo = static fn (string $path): string => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path($path)));
        $verifikasi = app(TteVerifikasi::class);
        $tte = new EtamTte(['nama_modul' => 'hi_pp', 'token_verifikasi' => 'token-sk-ujian']);
        $urlVerifikasi = $verifikasi->urlUntuk($tte);
        $qrVerifikasi = $verifikasi->qrDataUri($urlVerifikasi);
        $pdf = Pdf::loadView('backend.hi.pp.admbidang.cetak_sk', [
            'ajuan' => $ajuan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalBerakhir' => $tanggalMulai->copy()->addYears(2)->subDay(),
            'rangeTahun' => '2026-2028',
            'logoDataUri' => $logo('assets/etam_fe/images/logo/logo_kaltim.png'),
            'bsreLogoDataUri' => $logo('assets/etam_be/images/logo/logo_bsre.png'),
            'urlVerifikasi' => $urlVerifikasi,
            'qrVerifikasi' => $qrVerifikasi,
            'namaKepalaDinas' => 'Kepala Dinas Penguji',
            'bsreFooterText' => "Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik\nyang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).",
        ])->setPaper('a4', 'portrait')
            ->setOption('defaultMediaType', 'print')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true);

        $bytes = $pdf->output(['compress' => 0]);
        $this->assertStringStartsWith('%PDF-', $bytes);
        $this->assertSame(2, $pdf->getDomPDF()->getCanvas()->get_page_count());
        $this->assertSame(2, substr_count($bytes, 'Dokumen ini telah ditandatangani'));
        $this->assertStringContainsString('Salinan Keputusan', $bytes);
        $this->assertStringContainsString('Kepala Dinas Penguji', $bytes);
        $this->assertStringNotContainsString('Petugas Penguji', $bytes);
        $this->assertStringContainsString($urlVerifikasi, $bytes);
        $this->assertStringStartsWith('data:image/', $qrVerifikasi);
        $this->assertGreaterThanOrEqual(3, substr_count($bytes, '/Subtype /Image'));
        $this->assertSame(2, preg_match_all('/BT\s+[\d.]+\s+([\d.]+)\s+Td[^\n]*\[\(Dokumen ini telah ditandatangani/', $bytes, $footers));
        $this->assertSame(1, preg_match('/BT\s+[\d.]+\s+([\d.]+)\s+Td[^\n]*\[\(Jenis Usaha\)/', $bytes, $lastContent));
        $this->assertGreaterThan(12 * 72 / 25.4, (float) $footers[1][0]);
        $this->assertGreaterThan((float) $footers[1][0] + 30, (float) $lastContent[1]);
        $pageStyle = $pdf->getDomPDF()->getCss()->get_page_styles()['base'];
        $this->assertEqualsWithDelta(12 * 72 / 25.4, $pageStyle->margin_top, 0.1);
        $this->assertEqualsWithDelta(15 * 72 / 25.4, $pageStyle->margin_right, 0.1);
        $this->assertEqualsWithDelta(12 * 72 / 25.4, $pageStyle->margin_bottom, 0.1);
        $this->assertEqualsWithDelta(15 * 72 / 25.4, $pageStyle->margin_left, 0.1);
    }

    public static function companyExamples(): array
    {
        return [
            'standard' => ['PT Contoh Perusahaan Kalimantan Timur', 'Jalan Kemakmuran Nomor 123, Samarinda, Kalimantan Timur'],
            'long company details' => [
                'PT Contoh Perusahaan Pengelolaan dan Pengembangan Sumber Daya Kalimantan Timur',
                'Jalan Kemakmuran Nomor 123, Kelurahan Sungai Pinang, Kecamatan Samarinda Utara, Kota Samarinda, Kalimantan Timur',
            ],
        ];
    }
}
