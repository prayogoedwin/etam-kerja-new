<?php

namespace Tests\Feature;

use App\Models\EtamStruktur;
use App\Models\EtamTte;
use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use App\Models\Pengawasan\Pjk3\EtamPjk3SuketKode;
use App\Models\Pengawasan\Pjk3\EtamPjk3SuketPerangkat;
use App\Models\Pengawasan\Pjk3\EtamPjk3SuketUji;
use App\Models\User;
use App\Models\UserPenyedia;
use App\Services\Pengawasan\SuketDokumenTteService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Pjk3SuketTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        foreach ([
            'super-admin', 'admin-provinsi', 'admin-bidang', 'petugas-bidang',
            'kepala-bidang', 'kepala-seksi', 'penyedia-kerja', 'pencari-kerja',
            'eksekutif-provinsi',
        ] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_non_pjk3_cannot_open_pengajuan_suket(): void
    {
        $penyedia = $this->makePenyediaUser('PT Bukan PJK3');

        $this->actingAs($penyedia)
            ->get(route('pengawasan.suket.penyedia.pengajuan.index'))
            ->assertForbidden();

        $this->actingAs($penyedia)
            ->get(route('pengawasan.suket.penyedia.terbit.index'))
            ->assertOk()
            ->assertSee('Suket Layak K3')
            ->assertSee('Kode Suket K3')
            ->assertDontSee('Pengajuan Suket Layak K3 oleh PJK3');

        $this->actingAs($penyedia)
            ->get(route('pengawasan.suket.penyedia.kode.index'))
            ->assertOk()
            ->assertSee('Generate kode');
    }

    public function test_pjk3_can_submit_and_inspected_company_sees_draft_after_analisis(): void
    {
        $pjk3 = $this->makePjk3User();
        $uji = $this->makePenyediaUser('PT Perusahaan Diuji');
        $kategori = $this->kategori();
        $kode = $this->buatKode($uji);

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.pengajuan.create'))
            ->assertOk()
            ->assertSee('Kode perusahaan yang diperiksa')
            ->assertSee('Tambah baris alat')
            ->assertDontSee('PT Perusahaan Diuji')
            ->assertDontSee('Pilih perusahaan yang sudah punya akun');

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($kode->kode, $kategori->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $suket = EtamPjk3Suket::query()->where('pjk3_penyedia_id', $pjk3->id)->first();
        $this->assertNotNull($suket);
        $this->assertSame(EtamPjk3Suket::STATUS_DIAJUKAN, (int) $suket->status);
        $this->assertSame('Diajukan', $suket->statusLabel());
        $this->assertSame(1, $suket->perangkat()->count());
        $this->assertSame('Lift Gedung A', $suket->perangkat()->first()?->nama);

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.pengajuan.show', $suket->id))
            ->assertOk()
            ->assertSee('Diajukan')
            ->assertSee('Diproses')
            ->assertSee('Selesai')
            ->assertDontSee('Menunggu disposisi Kasi')
            ->assertDontSee('Sedang diperiksa dan ditandatangani')
            ->assertDontSee('Pengajuan masuk');

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.pengajuan.index'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('suket-tl', false)
            ->assertSee('Diajukan', false);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.terbit.show', $suket->id))
            ->assertNotFound();

        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $kasi = $this->makeUser('kepala-seksi', ['kode_struktur' => '21']);

        $this->actingAs($kasi)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suket->id), [
                'pengawas_id' => $pengawas->id,
                'catatan_disposisi' => 'Mohon cek dokumen',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_DISPO, (int) $suket->status);
        $this->assertSame('Diproses', $suket->statusLabel());
        $this->assertSame($pengawas->id, (int) $suket->pengawas_id);

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.pengajuan.show', $suket->id))
            ->assertOk()
            ->assertSee('Diproses');

        $this->actingAs($kasi)
            ->get(route('pengawasan.suket.admin.show', $suket->id))
            ->assertOk()
            ->assertSee('Disposisi Kasi')
            ->assertSee('Pemeriksaan')
            ->assertSee('TTD Pengawas')
            ->assertSee('TTD Kadis')
            ->assertSee('Diteruskan ke pengawas');

        $this->actingAs($kasi)
            ->get(route('pengawasan.suket.admin.pengajuan'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('TTD Pws', false)
            ->assertSee('Disposisi', false);

        $this->actingAs($pengawas)
            ->postJson(route('pengawasan.suket.admin.analisis', $suket->id), $this->payloadAnalisis([
                'is_layak' => 1,
                'hasil_analisis' => 'Alat memenuhi syarat K3.',
                'rekomendasi' => 'Dapat dioperasikan.',
            ]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_DRAFT, (int) $suket->status);
        $this->assertSame(1, (int) $suket->is_layak);
        $this->assertSame((int) $kategori->id, (int) $suket->ahli_k3_kategori_id);
        $this->assertSame($kategori->nama, $suket->namaBidangAhliK3());
        $this->assertSame('Budi Santoso', $suket->nilaiLhpp('nama_pengurus'));
        $this->assertSame('LHPP-PJK3-001', $suket->nilaiLhpp('nomor_lhpp_pjk3'));
        $alat = EtamPjk3SuketPerangkat::query()->where('suket_id', $suket->id)->first();
        $this->assertNotNull($alat);
        $this->assertSame('MCFA', $alat->nama);
        $this->assertDatabaseHas('etam_pjk3_suket_uji', [
            'suket_id' => $suket->id,
            'perangkat_id' => $alat->id,
            'item' => 'Fungsi alarm',
            'status' => EtamPjk3SuketUji::STATUS_MEMENUHI,
        ]);
        $this->assertMatchesRegularExpression(
            '/^'.preg_quote((string) $kategori->kode, '/').'\.\d{3}\/SUKET-K3\/'.now()->year.'$/',
            (string) $suket->nomor_suket
        );

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.terbit.show', $suket->id))
            ->assertOk()
            ->assertSee('draft')
            ->assertSee($suket->nomor_suket);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.cetak-lhpp', $suket->id))
            ->assertOk()
            ->assertSee('DRAFT')
            ->assertSee('Laporan Hasil Pemeriksaan')
            ->assertSee('LAYAK')
            ->assertSee('Nama pengurus')
            ->assertSee('Budi Santoso')
            ->assertSee('LHPP-PJK3-001')
            ->assertSee('MCFA')
            ->assertSee('Fungsi alarm')
            ->assertDontSee('Logo BSrE');

        $htmlDs = view('backend.pengawasan.pjk3.suket.cetak-lhpp', [
            'suket' => $suket->fresh()->load(['profilPjk3', 'profilPerusahaan', 'kategori', 'ahliK3Kategori', 'jenis', 'pengawas', 'perangkat.jenis', 'perangkat.uji']),
            'untukDs' => true,
            'tampilDraft' => false,
            'logoBsre' => 'data:image/png;base64,xx',
            'bsreTeks' => SuketDokumenTteService::BSRE_TEKS,
        ])->render();
        $this->assertStringNotContainsString('DRAFT', $htmlDs);
        $this->assertStringContainsString('ttd-bsre', $htmlDs);
        $this->assertStringContainsString('Balai Sertifikasi Elektronik (BSrE)', $htmlDs);
        $this->assertStringContainsString('size: A4', $htmlDs);

        $htmlSuketDs = view('backend.pengawasan.pjk3.suket.cetak-suket', [
            'suket' => $suket->fresh()->load(['profilPjk3', 'profilPerusahaan', 'kategori', 'jenis', 'pengawas', 'perangkat.jenis']),
            'untukDs' => true,
            'tampilDraft' => false,
            'logoBsre' => 'data:image/png;base64,xx',
            'bsreTeks' => SuketDokumenTteService::BSRE_TEKS,
        ])->render();
        $this->assertStringNotContainsString('DRAFT', $htmlSuketDs);
        $this->assertStringContainsString('ttd-bsre', $htmlSuketDs);
        $this->assertStringContainsString('Balai Sertifikasi Elektronik (BSrE)', $htmlSuketDs);

        $this->assertDatabaseHas('etam_tte', [
            'nama_modul' => 'suket_k3',
            'id_refrensi' => $suket->id,
            'kode_dokumen' => EtamPjk3Suket::TTE_DOK_LHPP,
            'user_id_tujuan' => $pengawas->id,
            'status' => EtamTte::STATUS_MENUNGGU,
        ]);
        $this->assertDatabaseHas('etam_tte', [
            'nama_modul' => 'suket_k3',
            'id_refrensi' => $suket->id,
            'kode_dokumen' => EtamPjk3Suket::TTE_DOK_SUKET,
            'user_id_tujuan' => $pengawas->id,
            'status' => EtamTte::STATUS_MENUNGGU,
        ]);

        $this->actingAs($pengawas)
            ->get(route('tte.menunggu'))
            ->assertOk()
            ->assertSee('TTE');

        $judulPengawas = collect($this->actingAs($pengawas)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->json('data'))->pluck('judul');
        $this->assertTrue($judulPengawas->contains(fn ($judul) => str_contains((string) $judul, 'LHPP K3')));
        $this->assertTrue($judulPengawas->contains(fn ($judul) => str_contains((string) $judul, 'Suket Layak K3')));

        $adminProv = $this->makeUser('admin-provinsi', ['kode_struktur' => '21']);
        $judulAdmin = collect($this->actingAs($adminProv)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->json('data'))->pluck('judul');
        $this->assertFalse($judulAdmin->contains(fn ($judul) => str_contains((string) $judul, 'LHPP K3')));

        $idsPengawas = EtamTte::query()
            ->where('nama_modul', 'suket_k3')
            ->where('id_refrensi', $suket->id)
            ->where('user_id_tujuan', $pengawas->id)
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->pluck('id')
            ->all();
        $this->assertCount(2, $idsPengawas);

        $this->actingAs($pengawas)
            ->post(route('tte.tandatangani'), ['tte_ids' => $idsPengawas, 'passphrase' => 'rahasia-tte'])
            ->assertOk()
            ->assertJsonPath('berhasil', 2);

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_TTD_PENGAWAS, (int) $suket->status);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.cetak-lhpp', $suket->id))
            ->assertOk()
            ->assertSee('DRAFT');

        $this->actingAs($pengawas)
            ->get(route('pengawasan.suket.admin.cetak-lhpp', $suket->id))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $kadis = $this->makeUser('eksekutif-provinsi');
        $this->assertDatabaseHas('etam_tte', [
            'nama_modul' => 'suket_k3',
            'id_refrensi' => $suket->id,
            'kode_dokumen' => EtamPjk3Suket::TTE_DOK_SUKET,
            'id_struktur' => EtamStruktur::KODE_DINAS,
            'status' => EtamTte::STATUS_MENUNGGU,
        ]);

        $tteKadis = EtamTte::query()
            ->where('nama_modul', 'suket_k3')
            ->where('id_refrensi', $suket->id)
            ->where('id_struktur', EtamStruktur::KODE_DINAS)
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->first();
        $this->assertNotNull($tteKadis);

        $judulKadis = collect($this->actingAs($kadis)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->json('data'))->pluck('judul');
        $this->assertTrue($judulKadis->contains(fn ($judul) => str_contains((string) $judul, 'Suket Layak K3')));
        $this->assertFalse($judulKadis->contains(fn ($judul) => str_contains((string) $judul, 'LHPP K3')));

        $this->actingAs($kadis)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tteKadis->id], 'passphrase' => 'rahasia-tte'])
            ->assertOk()
            ->assertJsonPath('berhasil', 1);

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_TTD_KADIS, (int) $suket->status);
        $this->assertSame('Selesai', $suket->statusLabel());

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.cetak-suket', $suket->id))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pengajuan_bisa_kirim_lebih_dari_satu_alat_dan_pengawas_melihatnya(): void
    {
        $pjk3 = $this->makePjk3User();
        $uji = $this->makePenyediaUser('PT Banyak Alat');
        $kategori = $this->kategori();
        $jenis = $this->jenisUntuk($kategori);
        $kode = $this->buatKode($uji);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($kode->kode, $kategori->id, [
                'perangkat' => [
                    [
                        'jenis_id' => $jenis->id,
                        'nama' => 'Panel Control',
                        'merk' => 'ABB',
                        'jumlah' => '2 Unit',
                        'lokasi' => 'Control Room',
                    ],
                    [
                        'jenis_id' => $jenis->id,
                        'nama' => 'Gas Detector',
                        'merk' => 'MSA',
                        'jumlah' => '1 Unit',
                        'lokasi' => 'Area produksi',
                    ],
                ],
            ]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $suket = EtamPjk3Suket::query()->where('pjk3_penyedia_id', $pjk3->id)->first();
        $this->assertNotNull($suket);
        $this->assertSame(2, $suket->perangkat()->count());
        $this->assertSame(3, (int) $suket->jumlah_unit);
        $this->assertSame('Panel Control, Gas Detector', $suket->nama_alat);

        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $kasi = $this->makeUser('kepala-seksi', ['kode_struktur' => '21']);
        $this->actingAs($kasi)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suket->id), ['pengawas_id' => $pengawas->id])
            ->assertOk();

        $this->actingAs($pengawas)
            ->get(route('pengawasan.suket.admin.show', $suket->id))
            ->assertOk()
            ->assertSee('2 alat')
            ->assertSee('Panel Control')
            ->assertSee('Gas Detector')
            ->assertSee('ABB')
            ->assertSee('MSA')
            ->assertSee('Tambah baris objek');
    }

    public function test_tidak_layak_also_creates_suket_draft(): void
    {
        $pjk3 = $this->makePjk3User();
        $uji = $this->makePenyediaUser('PT Tidak Layak');
        $kategori = $this->kategori();
        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $kasi = $this->makeUser('kepala-seksi', ['kode_struktur' => '21']);

        $kode = $this->buatKode($uji);
        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($kode->kode, $kategori->id))
            ->assertOk();

        $suket = EtamPjk3Suket::query()->where('pjk3_penyedia_id', $pjk3->id)->first();

        $this->actingAs($kasi)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suket->id), ['pengawas_id' => $pengawas->id])
            ->assertOk();

        $this->actingAs($pengawas)
            ->postJson(route('pengawasan.suket.admin.analisis', $suket->id), $this->payloadAnalisis([
                'is_layak' => 0,
                'hasil_analisis' => 'Tidak memenuhi syarat.',
            ]))
            ->assertOk();

        $suket->refresh();
        $this->assertSame(0, (int) $suket->is_layak);
        $this->assertSame(EtamPjk3Suket::STATUS_DRAFT, (int) $suket->status);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.cetak-suket', $suket->id))
            ->assertOk()
            ->assertSee('DRAFT')
            ->assertSee('SURAT KETERANGAN TIDAK LAYAK K3');
    }

    public function test_admin_bidang_lain_cannot_access_suket(): void
    {
        $admin = $this->makeUser('admin-bidang', ['kode_struktur' => '41']);

        $this->actingAs($admin)
            ->get(route('pengawasan.suket.admin.pengajuan'))
            ->assertForbidden();
    }

    public function test_pjk3_inspects_account_only_company_and_not_self(): void
    {
        $pjk3 = $this->makePjk3User('PT PJK3 Pemeriksa');
        $akunSaja = $this->makePenyediaUser('PT Hanya Punya Akun');
        $pjk3Lain = $this->makePjk3User('PT PJK3 Lain');
        $kategori = $this->kategori();
        $kodeAkun = $this->buatKode($akunSaja);
        $kodeLain = $this->buatKode($pjk3Lain);
        $kodeSendiri = $this->buatKode($pjk3);

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.pengajuan.create'))
            ->assertOk()
            ->assertSee('Kode perusahaan yang diperiksa')
            ->assertDontSee('PT Hanya Punya Akun')
            ->assertDontSee('PT PJK3 Lain')
            ->assertDontSee('PT PJK3 Pemeriksa');

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($kodeSendiri->kode, $kategori->id))
            ->assertOk()
            ->assertJsonPath('success', false);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($kodeAkun->kode, $kategori->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($kodeLain->kode, $kategori->id))
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_perusahaan_bisa_generate_lebih_dari_satu_kode_lalu_pjk3_mencari(): void
    {
        $uji = $this->makePenyediaUser('PT Kode Ganda');
        $pjk3 = $this->makePjk3User();

        $this->actingAs($uji)
            ->postJson(route('pengawasan.suket.penyedia.kode.store'))
            ->assertOk()
            ->assertJsonPath('success', true);
        $this->actingAs($uji)
            ->postJson(route('pengawasan.suket.penyedia.kode.store'))
            ->assertOk()
            ->assertJsonPath('success', true);

        $kodeAktif = EtamPjk3SuketKode::query()->where('perusahaan_id', $uji->id)->get();
        $this->assertCount(2, $kodeAktif);
        $this->assertTrue($kodeAktif->every(fn (EtamPjk3SuketKode $row) => $row->masihBerlaku()));

        $satu = $kodeAktif->first();
        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.cari'), ['kode' => $satu->kode])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('perusahaan.id', $uji->id)
            ->assertJsonPath('perusahaan.name', 'PT Kode Ganda');

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.cari'), ['kode' => 'SK3-XXXXXX'])
            ->assertOk()
            ->assertJsonPath('success', false);
    }

    public function test_kode_kedaluwarsa_dan_kode_sendiri_tidak_bisa_dicari(): void
    {
        $uji = $this->makePenyediaUser('PT Kedaluwarsa');
        $pjk3 = $this->makePjk3User();
        $kode = $this->buatKode($uji);
        $kodeSendiri = $this->buatKode($pjk3);

        $kode->update(['berlaku_hingga' => now()->subDay()]);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.cari'), ['kode' => $kode->kode])
            ->assertOk()
            ->assertJsonPath('success', false);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.cari'), ['kode' => $kodeSendiri->kode])
            ->assertOk()
            ->assertJsonPath('success', false);
    }

    public function test_pjk3_sees_pengajuan_menu(): void
    {
        $pjk3 = $this->makePjk3User();

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.terbit.index'))
            ->assertOk()
            ->assertSee('Pengajuan Suket Layak K3 oleh PJK3')
            ->assertSee('Pendaftaran PJK3')
            ->assertSee('Kode Suket K3');
    }

    public function test_disposisi_boleh_admin_bidang_pengawasan_kasi_21_22_dan_admin_prov(): void
    {
        $pjk3 = $this->makePjk3User();
        $uji = $this->makePenyediaUser('PT Disposisi');
        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $adminBidang = $this->makeUser('admin-bidang', ['kode_struktur' => '2']);
        $kasi22 = $this->makeUser('kepala-seksi', ['kode_struktur' => '22']);
        $adminProv = $this->makeUser('admin-provinsi');
        $pengawasUser = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $kasiLain = $this->makeUser('kepala-seksi', ['kode_struktur' => '41']);

        $suketBidang = $this->buatSuketDiajukan($pjk3, $uji);
        $this->actingAs($adminBidang)
            ->get(route('pengawasan.suket.admin.show', $suketBidang->id))
            ->assertOk()
            ->assertSee('Disposisi ke Pengawas');
        $this->actingAs($adminBidang)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suketBidang->id), [
                'pengawas_id' => $pengawas->id,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertSame($adminBidang->id, (int) $suketBidang->fresh()->kasi_id);

        $suketKasi22 = $this->buatSuketDiajukan($pjk3, $uji);
        $this->actingAs($kasi22)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suketKasi22->id), [
                'pengawas_id' => $pengawas->id,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $suketProv = $this->buatSuketDiajukan($pjk3, $uji);
        $this->actingAs($adminProv)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suketProv->id), [
                'pengawas_id' => $pengawas->id,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $suketTolak = $this->buatSuketDiajukan($pjk3, $uji);
        $this->actingAs($pengawasUser)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suketTolak->id), [
                'pengawas_id' => $pengawas->id,
            ])
            ->assertForbidden();
        $this->actingAs($kasiLain)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suketTolak->id), [
                'pengawas_id' => $pengawas->id,
            ])
            ->assertForbidden();
    }

    private function buatSuketDiajukan(User $pjk3, User $uji): EtamPjk3Suket
    {
        return EtamPjk3Suket::create([
            'pjk3_penyedia_id' => $pjk3->id,
            'perusahaan_id' => $uji->id,
            'kategori_id' => $this->kategori()->id,
            'status' => EtamPjk3Suket::STATUS_DIAJUKAN,
            'created_by' => $pjk3->id,
        ]);
    }

    private function kategori(): EtamPengawasanK3Kategori
    {
        $kategori = EtamPengawasanK3Kategori::query()->first();
        if (! $kategori) {
            return EtamPengawasanK3Kategori::create([
                'nama' => 'Listrik',
                'kode' => 'LIS',
            ]);
        }

        if (! $kategori->kode) {
            $kategori->update(['kode' => 'LIS']);
        }

        return $kategori->fresh();
    }

    private function buatKode(User $perusahaan): EtamPjk3SuketKode
    {
        return EtamPjk3SuketKode::buatUntuk($perusahaan->id);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function jenisUntuk(EtamPengawasanK3Kategori $kategori): EtamPengawasanK3Jenis
    {
        $jenis = EtamPengawasanK3Jenis::query()->where('kategori_id', $kategori->id)->first();
        if ($jenis) {
            return $jenis;
        }

        return EtamPengawasanK3Jenis::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Elevator (lift)',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payloadAnalisis(array $overrides = []): array
    {
        $kategori = $this->kategori();
        $jenis = $this->jenisUntuk($kategori);

        return array_merge([
            'tanggal_pemeriksaan' => now()->toDateString(),
            'is_layak' => 1,
            'hasil_analisis' => 'Alat memenuhi syarat K3.',
            'rekomendasi' => 'Dapat dioperasikan.',
            'ahli_k3_kategori_id' => $kategori->id,
            'nama_pengurus' => 'Budi Santoso',
            'nomor_lhpp_pjk3' => 'LHPP-PJK3-001',
            'jenis_usaha' => 'Perseroan Terbatas',
            'jumlah_pekerja' => 40,
            'nomor_wlkp' => 'WLKP-9',
            'perangkat' => [
                [
                    'jenis_id' => $jenis->id,
                    'nama' => 'MCFA',
                    'merk' => 'Honeywell',
                    'jumlah' => '1',
                    'lokasi' => 'Control room',
                    'uji' => [
                        ['item' => 'Fungsi alarm', 'status' => EtamPjk3SuketUji::STATUS_MEMENUHI, 'hasil' => 'Normal'],
                    ],
                ],
            ],
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(string $kode, int $kategoriId, array $overrides = []): array
    {
        $kategori = EtamPengawasanK3Kategori::query()->find($kategoriId) ?? $this->kategori();
        $jenis = $this->jenisUntuk($kategori);

        return array_merge([
            'kode' => $kode,
            'kategori_id' => $kategoriId,
            'perangkat' => [
                [
                    'jenis_id' => $jenis->id,
                    'nama' => 'Lift Gedung A',
                    'merk' => '',
                    'jumlah' => '1',
                    'lokasi' => 'Samarinda',
                ],
            ],
            'tipe_surat_permohonan' => 'link',
            'link_surat_permohonan' => 'https://example.com/permohonan',
            'tipe_lhpp' => 'link',
            'link_lhpp' => 'https://example.com/lhpp',
            'tipe_skp_pjk3' => 'link',
            'link_skp_pjk3' => 'https://example.com/skp-pjk3',
            'tipe_ahli_k3' => 'link',
            'link_ahli_k3' => 'https://example.com/ahli-k3',
        ], $overrides);
    }

    private function makePjk3User(string $name = 'PT PJK3 Aktif'): User
    {
        $user = $this->makePenyediaUser($name);
        EtamPjk3Ajuan::create([
            'penyedia_id' => $user->id,
            'tanggal_ajuan' => now()->toDateString(),
            'status_verifikasi' => EtamPjk3Ajuan::STATUS_ACC,
            'masa_berlaku' => now()->addYear()->toDateString(),
            'dokumen' => [
                'sk_pjk3' => ['tipe' => 'link', 'value' => 'https://example.com/sk'],
                'sk_ahli' => ['tipe' => 'link', 'value' => 'https://example.com/ahli'],
                'wlkp' => ['tipe' => 'link', 'value' => 'https://example.com/wlkp'],
                'bpjs' => ['tipe' => 'link', 'value' => 'https://example.com/bpjs'],
            ],
            'created_by' => $user->id,
        ]);

        return $user;
    }

    private function makeUser(string $role, array $overrides = []): User
    {
        $defaults = [
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'is_finished' => 1,
        ];
        if (in_array($role, ['petugas-bidang', 'eksekutif-provinsi'], true)
            && ! array_key_exists('nik', $overrides)) {
            $defaults['nik'] = fake()->unique()->numerify('################');
        }
        if ($role === 'eksekutif-provinsi' && ! array_key_exists('kode_struktur', $overrides)) {
            $defaults['kode_struktur'] = EtamStruktur::KODE_DINAS;
            $defaults['lokasi_kerja'] = $overrides['lokasi_kerja'] ?? '64';
        }

        $user = User::factory()->create(array_merge($defaults, $overrides));
        $user->assignRole($role);

        return $user;
    }

    private function makePenyediaUser(string $name = 'PT Demo'): User
    {
        $user = $this->makeUser('penyedia-kerja');
        UserPenyedia::create([
            'user_id' => $user->id,
            'name' => $name,
            'nib' => fake()->unique()->numerify('##########'),
            'id_sektor' => 1,
            'id_provinsi' => 64,
            'id_kota' => 6472,
            'id_kecamatan' => 6472010,
            'status_id' => 1,
            'posted_by' => $user->id,
        ]);

        return $user;
    }
}
