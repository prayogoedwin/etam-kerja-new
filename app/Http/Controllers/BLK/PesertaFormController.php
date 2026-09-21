<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlkForm;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\BLK\EtamBlkPelatihanPesertaPerusahaan;
use App\Models\UserPencari;
use App\Models\UserPenyedia;
use App\Services\Blk\BlkFormAnswerService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PesertaFormController extends Controller
{
    use ManagesBlkAccess;

    public function __construct(private BlkFormAnswerService $answers) {}

    public function wawancara(string $pelatihanId, string $pesertaId)
    {
        [$pelatihan, $peserta, $keys] = $this->adminContext($pelatihanId, $pesertaId, EtamBlkForm::JENIS_WAWANCARA);
        $rows = $this->answers->ensureSnapshot($pelatihan, EtamBlkForm::JENIS_WAWANCARA, $keys);
        $submitted = $this->answers->isSubmitted($pelatihan, EtamBlkForm::JENIS_WAWANCARA, $keys);
        $form = $this->answers->formFor($pelatihan, EtamBlkForm::JENIS_WAWANCARA);
        $jenis = EtamBlkForm::JENIS_WAWANCARA;
        $readonly = $submitted;
        $action = route('blk.pelatihan.peserta.wawancara.store', [$pelatihan->id, $pesertaId]);

        return view('backend.blk.form.isi', compact('pelatihan', 'peserta', 'rows', 'submitted', 'form', 'jenis', 'readonly', 'action'));
    }

    public function storeWawancara(Request $request, string $pelatihanId, string $pesertaId)
    {
        [$pelatihan, , $keys] = $this->adminContext($pelatihanId, $pesertaId, EtamBlkForm::JENIS_WAWANCARA);

        try {
            $this->answers->saveAnswers(
                $pelatihan,
                EtamBlkForm::JENIS_WAWANCARA,
                $keys,
                (array) $request->input('jawaban', []),
                $request->boolean('submit')
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $message = $request->boolean('submit')
            ? 'Hasil wawancara berhasil dikirim'
            : 'Draft wawancara tersimpan';

        return redirect()
            ->route('blk.pelatihan.peserta.wawancara', [$pelatihan->id, $pesertaId])
            ->with('success', $message);
    }

    public function lihatPretest(string $pelatihanId, string $pesertaId)
    {
        [$pelatihan, $peserta, $keys] = $this->adminContext($pelatihanId, $pesertaId, EtamBlkForm::JENIS_PRETEST);
        $rows = $this->answers->answersQuery($pelatihan, EtamBlkForm::JENIS_PRETEST, $keys)->orderBy('urutan')->get();
        $submitted = $this->answers->isSubmitted($pelatihan, EtamBlkForm::JENIS_PRETEST, $keys);
        $form = $this->answers->formFor($pelatihan, EtamBlkForm::JENIS_PRETEST);
        $jenis = EtamBlkForm::JENIS_PRETEST;
        $readonly = true;
        $action = null;

        return view('backend.blk.form.isi', compact('pelatihan', 'peserta', 'rows', 'submitted', 'form', 'jenis', 'readonly', 'action'));
    }

    public function pretest(string $pelatihanId)
    {
        [$pelatihan, $peserta, $keys] = $this->pesertaContext($pelatihanId);
        $rows = $this->answers->ensureSnapshot($pelatihan, EtamBlkForm::JENIS_PRETEST, $keys);
        $submitted = $this->answers->isSubmitted($pelatihan, EtamBlkForm::JENIS_PRETEST, $keys);
        $form = $this->answers->formFor($pelatihan, EtamBlkForm::JENIS_PRETEST);
        $jenis = EtamBlkForm::JENIS_PRETEST;
        $readonly = $submitted;
        $action = route('blk.pelatihan.pretest.store', $pelatihan->id);

        return view('backend.blk.form.isi', compact('pelatihan', 'peserta', 'rows', 'submitted', 'form', 'jenis', 'readonly', 'action'));
    }

    public function storePretest(Request $request, string $pelatihanId)
    {
        [$pelatihan, , $keys] = $this->pesertaContext($pelatihanId);

        try {
            $this->answers->saveAnswers(
                $pelatihan,
                EtamBlkForm::JENIS_PRETEST,
                $keys,
                (array) $request->input('jawaban', []),
                $request->boolean('submit')
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $message = $request->boolean('submit')
            ? 'Pretest berhasil dikirim'
            : 'Draft pretest tersimpan';

        return redirect()
            ->route('blk.pelatihan.pretest', $pelatihan->id)
            ->with('success', $message);
    }

    /**
     * @return array{0: EtamBlkPelatihan, 1: mixed, 2: array{blk_peserta_id: int|null, perusahaan_peserta_id: int|null, pencari_id: int|null}}
     */
    private function adminContext(string $pelatihanId, string $pesertaId, string $jenis): array
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($pelatihanId);
        if (! $this->canAccessBlk((int) $pelatihan->blk_id)) {
            abort(403);
        }

        $form = $this->answers->formFor($pelatihan, $jenis);
        if (! $form) {
            abort(404, 'Template belum dipilih untuk pelatihan ini.');
        }

        $peserta = $this->findPeserta($pelatihan, (int) $pesertaId);
        $keys = $this->answers->participantKeys($pelatihan, (int) $pesertaId);

        return [$pelatihan, $peserta, $keys];
    }

    /**
     * @return array{0: EtamBlkPelatihan, 1: mixed, 2: array{blk_peserta_id: int|null, perusahaan_peserta_id: int|null, pencari_id: int|null}}
     */
    private function pesertaContext(string $pelatihanId): array
    {
        $role = $this->currentRoleName();
        if (! in_array($role, ['pencari-kerja', 'penyedia-kerja'], true)) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($pelatihanId);
        if (! $this->answers->formFor($pelatihan, EtamBlkForm::JENIS_PRETEST)) {
            abort(404, 'Pelatihan ini tidak memiliki pretest.');
        }

        $peserta = $this->existingRegistration($pelatihan);
        if (! $peserta) {
            abort(403, 'Anda belum terdaftar pada pelatihan ini.');
        }

        if ($role === 'pencari-kerja' && (int) $pelatihan->pelatihan_untuk !== EtamBlkPelatihan::UNTUK_PENCARI) {
            abort(403);
        }
        if ($role === 'penyedia-kerja' && (int) $pelatihan->pelatihan_untuk !== EtamBlkPelatihan::UNTUK_PENYEDIA) {
            abort(403);
        }

        $keys = $this->answers->participantKeys($pelatihan, (int) $peserta->id);

        return [$pelatihan, $peserta, $keys];
    }

    private function findPeserta(EtamBlkPelatihan $pelatihan, int $pesertaId): mixed
    {
        if ((int) $pelatihan->pelatihan_untuk === EtamBlkPelatihan::UNTUK_PENYEDIA) {
            return EtamBlkPelatihanPesertaPerusahaan::where('blk_pelatihan_id', $pelatihan->id)->findOrFail($pesertaId);
        }

        return EtamBlkPelatihanPeserta::where('blk_pelatihan_id', $pelatihan->id)->findOrFail($pesertaId);
    }

    private function existingRegistration(EtamBlkPelatihan $pelatihan): mixed
    {
        $role = $this->currentRoleName();

        if ($role === 'pencari-kerja') {
            $pencari = UserPencari::where('user_id', auth()->id())->first();
            if (! $pencari) {
                return null;
            }

            return EtamBlkPelatihanPeserta::where('blk_pelatihan_id', $pelatihan->id)
                ->where('pencari_id', $pencari->id)
                ->first();
        }

        if ($role === 'penyedia-kerja') {
            $penyedia = UserPenyedia::where('user_id', auth()->id())->first();
            if (! $penyedia) {
                return null;
            }

            return EtamBlkPelatihanPesertaPerusahaan::where('blk_pelatihan_id', $pelatihan->id)
                ->where('perusahaan_id', $penyedia->id)
                ->first();
        }

        return null;
    }
}
