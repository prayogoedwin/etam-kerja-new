<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlkForm;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanJawaban;
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

    public function cbt(string $pelatihanId)
    {
        [$pelatihan, $peserta, $keys] = $this->pesertaContext($pelatihanId, EtamBlkForm::JENIS_CBT);
        $submitted = $this->answers->isSubmitted($pelatihan, EtamBlkForm::JENIS_CBT, $keys);
        $form = $this->answers->formFor($pelatihan, EtamBlkForm::JENIS_CBT);
        $unlocked = $submitted || session()->get($this->cbtSessionKey($pelatihan->id), false);
        $rows = collect();
        $score = null;

        if ($submitted || ($unlocked && $pelatihan->isCbtOpen())) {
            $rows = $this->answers->ensureSnapshot($pelatihan, EtamBlkForm::JENIS_CBT, $keys);
        }

        if ($submitted) {
            $score = $this->answers->score($rows);
        }

        $jenis = EtamBlkForm::JENIS_CBT;
        $readonly = $submitted;
        $action = route('blk.pelatihan.cbt.store', $pelatihan->id);
        $needKode = ! $submitted && ! $unlocked;
        $closed = ! $submitted && ! $pelatihan->isCbtOpen();

        return view('backend.blk.cbt.isi', compact(
            'pelatihan',
            'peserta',
            'rows',
            'submitted',
            'form',
            'jenis',
            'readonly',
            'action',
            'score',
            'needKode',
            'closed'
        ));
    }

    public function unlockCbt(Request $request, string $pelatihanId)
    {
        [$pelatihan] = $this->pesertaContext($pelatihanId, EtamBlkForm::JENIS_CBT);

        if (! $pelatihan->isCbtOpen()) {
            return redirect()->back()->withErrors(['cbt_kode' => 'CBT belum dibuka.']);
        }

        $kode = trim((string) $request->input('cbt_kode'));
        if ($kode !== (string) $pelatihan->cbt_kode) {
            return redirect()->back()->withErrors(['cbt_kode' => 'Kode tes tidak sesuai.'])->withInput();
        }

        session()->put($this->cbtSessionKey($pelatihan->id), true);

        return redirect()->route('blk.pelatihan.cbt', $pelatihan->id);
    }

    public function storeCbt(Request $request, string $pelatihanId)
    {
        [$pelatihan, , $keys] = $this->pesertaContext($pelatihanId, EtamBlkForm::JENIS_CBT);

        if (! $pelatihan->isCbtOpen()) {
            return redirect()->back()->withErrors(['form' => 'CBT sudah ditutup.']);
        }

        if (! session()->get($this->cbtSessionKey($pelatihan->id), false)
            && ! $this->answers->isSubmitted($pelatihan, EtamBlkForm::JENIS_CBT, $keys)) {
            return redirect()->route('blk.pelatihan.cbt', $pelatihan->id)
                ->withErrors(['cbt_kode' => 'Masukkan kode tes terlebih dahulu.']);
        }

        try {
            $this->answers->saveAnswers(
                $pelatihan,
                EtamBlkForm::JENIS_CBT,
                $keys,
                (array) $request->input('jawaban', []),
                true
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        session()->forget($this->cbtSessionKey($pelatihan->id));

        return redirect()
            ->route('blk.pelatihan.cbt', $pelatihan->id)
            ->with('success', 'CBT berhasil dikirim. Nilai langsung tampil di bawah.');
    }

    public function hasilCbt(string $pelatihanId)
    {
        if (! $this->canCreatePelatihan()) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::with(['cbtForm:id,nama', 'blk:id,nama_lembaga'])->findOrFail($pelatihanId);
        if (! $this->canAccessBlk((int) $pelatihan->blk_id)) {
            abort(403);
        }
        if (! $pelatihan->cbt_form_id) {
            abort(404, 'Pelatihan ini tidak memiliki CBT.');
        }

        $isPerusahaan = (int) $pelatihan->pelatihan_untuk === EtamBlkPelatihan::UNTUK_PENYEDIA;
        $pesertaRows = $isPerusahaan
            ? EtamBlkPelatihanPesertaPerusahaan::query()->where('blk_pelatihan_id', $pelatihan->id)->get()
            : EtamBlkPelatihanPeserta::query()->where('blk_pelatihan_id', $pelatihan->id)->get();

        $hasil = $pesertaRows->map(function ($peserta) use ($pelatihan, $isPerusahaan) {
            $keys = $this->answers->participantKeys($pelatihan, (int) $peserta->id);
            $rows = $this->answers->answersQuery($pelatihan, EtamBlkForm::JENIS_CBT, $keys)->orderBy('urutan')->get();
            $submitted = $rows->contains(fn (EtamBlkPelatihanJawaban $row) => $row->submitted_at !== null);
            $score = $submitted ? $this->answers->score($rows) : null;

            return [
                'peserta' => $peserta,
                'submitted' => $submitted,
                'submitted_at' => $submitted ? $rows->max('submitted_at') : null,
                'score' => $score,
            ];
        })->sort(function ($a, $b) {
            if ($a['submitted'] !== $b['submitted']) {
                return $a['submitted'] ? -1 : 1;
            }
            if ($a['submitted'] && $b['submitted']) {
                $nilai = ($b['score']['nilai'] ?? 0) <=> ($a['score']['nilai'] ?? 0);
                if ($nilai !== 0) {
                    return $nilai;
                }
            }

            return strcmp((string) $a['peserta']->name, (string) $b['peserta']->name);
        })->values();

        return view('backend.blk.cbt.hasil', compact('pelatihan', 'hasil', 'isPerusahaan'));
    }

    public function lihatCbt(string $pelatihanId, string $pesertaId)
    {
        [$pelatihan, $peserta, $keys] = $this->adminContext($pelatihanId, $pesertaId, EtamBlkForm::JENIS_CBT);
        $rows = $this->answers->answersQuery($pelatihan, EtamBlkForm::JENIS_CBT, $keys)->orderBy('urutan')->get();
        $submitted = $this->answers->isSubmitted($pelatihan, EtamBlkForm::JENIS_CBT, $keys);
        $form = $this->answers->formFor($pelatihan, EtamBlkForm::JENIS_CBT);
        $jenis = EtamBlkForm::JENIS_CBT;
        $readonly = true;
        $action = null;
        $score = $submitted ? $this->answers->score($rows) : null;

        return view('backend.blk.form.isi', compact(
            'pelatihan',
            'peserta',
            'rows',
            'submitted',
            'form',
            'jenis',
            'readonly',
            'action',
            'score'
        ));
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

    private function cbtSessionKey(int $pelatihanId): string
    {
        return 'blk_cbt_unlock_'.$pelatihanId;
    }

    /**
     * @return array{0: EtamBlkPelatihan, 1: mixed, 2: array{blk_peserta_id: int|null, perusahaan_peserta_id: int|null, pencari_id: int|null}}
     */
    private function pesertaContext(string $pelatihanId, string $jenis = EtamBlkForm::JENIS_PRETEST): array
    {
        $role = $this->currentRoleName();
        if (! in_array($role, ['pencari-kerja', 'penyedia-kerja'], true)) {
            abort(403);
        }

        $pelatihan = EtamBlkPelatihan::findOrFail($pelatihanId);
        if (! $this->answers->formFor($pelatihan, $jenis)) {
            abort(404, 'Pelatihan ini tidak memiliki '.($jenis === EtamBlkForm::JENIS_CBT ? 'CBT' : 'pretest').'.');
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
