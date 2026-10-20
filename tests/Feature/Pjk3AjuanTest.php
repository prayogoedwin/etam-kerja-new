<?php

namespace Tests\Feature;

use App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan;
use App\Models\User;
use App\Models\UserPenyedia;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Pjk3AjuanTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin-provinsi', 'admin-bidang', 'petugas-bidang', 'kepala-bidang', 'kepala-seksi', 'penyedia-kerja', 'pencari-kerja'] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_penyedia_can_submit_pjk3_with_links(): void
    {
        $penyedia = $this->makePenyediaUser();

        $response = $this->actingAs($penyedia)->postJson(route('pengawasan.pjk3.penyedia.store'), $this->linkPayload());

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('etam_pjk3_ajuan', [
            'penyedia_id' => $penyedia->id,
            'status_verifikasi' => EtamPjk3Ajuan::STATUS_MENUNGGU,
        ]);
    }

    public function test_penyedia_cannot_submit_while_pending(): void
    {
        $penyedia = $this->makePenyediaUser();
        $this->makeAjuan($penyedia->id, EtamPjk3Ajuan::STATUS_MENUNGGU);

        $this->actingAs($penyedia)
            ->get(route('pengawasan.pjk3.penyedia.create'))
            ->assertRedirect(route('pengawasan.pjk3.penyedia.index'));

        $this->actingAs($penyedia)
            ->postJson(route('pengawasan.pjk3.penyedia.store'), $this->linkPayload())
            ->assertStatus(422);
    }

    public function test_penyedia_can_replace_documents_when_revisi(): void
    {
        Storage::fake('public');
        $penyedia = $this->makePenyediaUser();
        $ajuan = $this->makeAjuan($penyedia->id, EtamPjk3Ajuan::STATUS_REVISI, null, [
            'sk_pjk3' => ['tipe' => 'link', 'value' => 'https://example.com/lama-sk'],
            'sk_ahli' => ['tipe' => 'link', 'value' => 'https://example.com/lama-ahli'],
            'wlkp' => ['tipe' => 'link', 'value' => 'https://example.com/lama-wlkp'],
            'bpjs' => ['tipe' => 'link', 'value' => 'https://example.com/lama-bpjs'],
        ]);

        $this->actingAs($penyedia)
            ->get(route('pengawasan.pjk3.penyedia.show', $ajuan->id))
            ->assertOk()
            ->assertSee('Upload ulang dokumen');

        $this->actingAs($penyedia)
            ->get(route('pengawasan.pjk3.penyedia.revisi', $ajuan->id))
            ->assertOk()
            ->assertSee('Keterangan')
            ->assertSee('Google Drive');

        $file = UploadedFile::fake()->create('sk-baru.pdf', 120, 'application/pdf');

        $this->actingAs($penyedia)
            ->postJson(route('pengawasan.pjk3.penyedia.update', $ajuan->id), [
                'tipe_sk_pjk3' => 'file',
                'file_sk_pjk3' => $file,
                'tipe_sk_ahli' => 'link',
                'link_sk_ahli' => 'https://example.com/baru-ahli',
                'tipe_wlkp' => 'link',
                'link_wlkp' => 'https://example.com/baru-wlkp',
                'tipe_bpjs' => 'link',
                'link_bpjs' => 'https://example.com/baru-bpjs',
                'keterangan' => 'Dokumen SK sudah diperbarui',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $ajuan->refresh();
        $this->assertSame(EtamPjk3Ajuan::STATUS_MENUNGGU, (int) $ajuan->status_verifikasi);
        $this->assertSame('file', $ajuan->dokumen['sk_pjk3']['tipe']);
        $this->assertNotSame('https://example.com/lama-sk', $ajuan->dokumen['sk_pjk3']['value']);
        $this->assertSame('https://example.com/baru-ahli', $ajuan->dokumen['sk_ahli']['value']);
        Storage::disk('public')->assertExists($ajuan->dokumen['sk_pjk3']['value']);
        $this->assertSame(
            'ajuan_pjk3/'.now()->format('Y').'/'.now()->format('m').'/'.md5((string) $penyedia->id).'/sk-baru.pdf',
            $ajuan->dokumen['sk_pjk3']['value']
        );
        $this->assertSame('Dokumen SK sudah diperbarui', $ajuan->alasan);
        $this->assertSame('ajuan_ulang', $ajuan->log_verifikasi[0]['status']);
        $this->assertSame('Dokumen SK sudah diperbarui', $ajuan->log_verifikasi[0]['alasan']);
        $this->assertSame($penyedia->id, $ajuan->log_verifikasi[0]['user_id']);
    }

    public function test_penyedia_and_admin_can_view_uploaded_file(): void
    {
        Storage::fake('public');
        $penyedia = $this->makePenyediaUser();
        $other = $this->makePenyediaUser();
        $path = 'ajuan_pjk3/2026/10/'.md5((string) $penyedia->id).'/sk.pdf';
        Storage::disk('public')->put($path, 'pdf-content');
        $ajuan = $this->makeAjuan($penyedia->id, EtamPjk3Ajuan::STATUS_MENUNGGU, null, [
            'sk_pjk3' => ['tipe' => 'file', 'value' => $path],
            'sk_ahli' => ['tipe' => 'link', 'value' => 'https://example.com/ahli'],
            'wlkp' => ['tipe' => 'link', 'value' => 'https://example.com/wlkp'],
            'bpjs' => ['tipe' => 'link', 'value' => 'https://example.com/bpjs'],
        ]);

        $this->actingAs($penyedia)
            ->get(route('pengawasan.pjk3.penyedia.file', [$ajuan->id, 'sk_pjk3']))
            ->assertOk();

        $this->actingAs($other)
            ->get(route('pengawasan.pjk3.penyedia.file', [$ajuan->id, 'sk_pjk3']))
            ->assertNotFound();

        $admin = $this->makeUser('super-admin');
        $this->actingAs($admin)
            ->get(route('pengawasan.pjk3.admin.file', [$ajuan->id, 'sk_pjk3']))
            ->assertOk();
    }

    public function test_penyedia_can_submit_again_after_expired_acc(): void
    {
        $penyedia = $this->makePenyediaUser();
        $this->makeAjuan($penyedia->id, EtamPjk3Ajuan::STATUS_ACC, now()->subDay()->toDateString());

        $this->actingAs($penyedia)
            ->postJson(route('pengawasan.pjk3.penyedia.store'), $this->linkPayload())
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(2, EtamPjk3Ajuan::where('penyedia_id', $penyedia->id)->count());
    }

    public function test_petugas_seksi_k3_can_verify_acc(): void
    {
        $penyedia = $this->makePenyediaUser();
        $ajuan = $this->makeAjuan($penyedia->id, EtamPjk3Ajuan::STATUS_MENUNGGU);
        $petugas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);

        $this->actingAs($petugas)
            ->postJson(route('pengawasan.pjk3.admin.verifikasi', $ajuan->id), [
                'status' => 'acc',
                'masa_berlaku' => now()->addYear()->toDateString(),
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $ajuan->refresh();
        $this->assertSame(EtamPjk3Ajuan::STATUS_ACC, (int) $ajuan->status_verifikasi);
        $this->assertNotEmpty($ajuan->log_verifikasi);
        $this->assertSame('acc', $ajuan->log_verifikasi[0]['status']);
        $this->assertSame($petugas->id, $ajuan->log_verifikasi[0]['user_id']);
    }

    public function test_kepala_bidang_pembinaan_can_verify_tolak(): void
    {
        $penyedia = $this->makePenyediaUser();
        $ajuan = $this->makeAjuan($penyedia->id, EtamPjk3Ajuan::STATUS_MENUNGGU);
        $kabid = $this->makeUser('kepala-bidang', ['kode_struktur' => '2']);

        $this->actingAs($kabid)
            ->postJson(route('pengawasan.pjk3.admin.verifikasi', $ajuan->id), [
                'status' => 'tolak',
                'alasan' => 'Dokumen tidak lengkap',
            ])
            ->assertOk();

        $ajuan->refresh();
        $this->assertSame(EtamPjk3Ajuan::STATUS_TOLAK, (int) $ajuan->status_verifikasi);
        $this->assertSame('tolak', $ajuan->log_verifikasi[0]['status']);
    }

    public function test_admin_bidang_lain_cannot_access_pjk3(): void
    {
        $admin = $this->makeUser('admin-bidang', ['kode_struktur' => '41']);

        $this->actingAs($admin)
            ->get(route('pengawasan.pjk3.admin.index'))
            ->assertStatus(403);
    }

    public function test_super_admin_can_verify_acc(): void
    {
        $penyedia = $this->makePenyediaUser();
        $ajuan = $this->makeAjuan($penyedia->id, EtamPjk3Ajuan::STATUS_MENUNGGU);
        $admin = $this->makeUser('super-admin');

        $this->actingAs($admin)
            ->get(route('pengawasan.pjk3.admin.show', $ajuan->id))
            ->assertOk()
            ->assertSee('Verifikasi');

        $this->actingAs($admin)
            ->postJson(route('pengawasan.pjk3.admin.verifikasi', $ajuan->id), [
                'status' => 'acc',
                'masa_berlaku' => now()->addYear()->toDateString(),
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $ajuan->refresh();
        $this->assertSame(EtamPjk3Ajuan::STATUS_ACC, (int) $ajuan->status_verifikasi);
        $this->assertSame('acc', $ajuan->log_verifikasi[0]['status']);
    }

    private function makeUser(string $role, array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'is_finished' => 1,
        ], $overrides));
        $user->assignRole($role);

        return $user;
    }

    private function makePenyediaUser(): User
    {
        $user = $this->makeUser('penyedia-kerja');
        UserPenyedia::create([
            'user_id' => $user->id,
            'name' => 'PT Demo PJK3',
            'nib' => '999888777',
            'id_sektor' => 1,
            'id_provinsi' => 64,
            'id_kota' => 6472,
            'id_kecamatan' => 6472010,
            'status_id' => 1,
            'posted_by' => $user->id,
        ]);

        return $user;
    }

    private function makeAjuan(int $penyediaId, int $status, ?string $masaBerlaku = null, ?array $dokumen = null): EtamPjk3Ajuan
    {
        return EtamPjk3Ajuan::create([
            'penyedia_id' => $penyediaId,
            'tanggal_ajuan' => now()->toDateString(),
            'status_verifikasi' => $status,
            'masa_berlaku' => $masaBerlaku,
            'dokumen' => $dokumen ?? [
                'sk_pjk3' => ['tipe' => 'link', 'value' => 'https://example.com/sk-pjk3'],
                'sk_ahli' => ['tipe' => 'link', 'value' => 'https://example.com/sk-ahli'],
                'wlkp' => ['tipe' => 'link', 'value' => 'https://example.com/wlkp'],
                'bpjs' => ['tipe' => 'link', 'value' => 'https://example.com/bpjs'],
            ],
            'created_by' => $penyediaId,
        ]);
    }

    private function linkPayload(): array
    {
        return [
            'tipe_sk_pjk3' => 'link',
            'link_sk_pjk3' => 'https://example.com/sk-pjk3',
            'tipe_sk_ahli' => 'link',
            'link_sk_ahli' => 'https://example.com/sk-ahli',
            'tipe_wlkp' => 'link',
            'link_wlkp' => 'https://example.com/wlkp',
            'tipe_bpjs' => 'link',
            'link_bpjs' => 'https://example.com/bpjs',
        ];
    }
}
