<?php

namespace App\Services\Blk;

use App\Models\BLK\EtamBlkForm;
use App\Models\BLK\EtamBlkFormPertanyaan;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanJawaban;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\BLK\EtamBlkPelatihanPesertaPerusahaan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BlkFormAnswerService
{
    public function formFor(EtamBlkPelatihan $pelatihan, string $jenis): ?EtamBlkForm
    {
        $formId = $jenis === EtamBlkForm::JENIS_PRETEST
            ? $pelatihan->pretest_form_id
            : $pelatihan->wawancara_form_id;

        if (! $formId) {
            return null;
        }

        return EtamBlkForm::with('pertanyaan')->find($formId);
    }

    /**
     * @return array{blk_peserta_id: int|null, perusahaan_peserta_id: int|null, pencari_id: int|null}
     */
    public function participantKeys(EtamBlkPelatihan $pelatihan, int $pesertaId): array
    {
        if ((int) $pelatihan->pelatihan_untuk === EtamBlkPelatihan::UNTUK_PENYEDIA) {
            EtamBlkPelatihanPesertaPerusahaan::where('blk_pelatihan_id', $pelatihan->id)->findOrFail($pesertaId);

            return [
                'blk_peserta_id' => null,
                'perusahaan_peserta_id' => $pesertaId,
                'pencari_id' => null,
            ];
        }

        $peserta = EtamBlkPelatihanPeserta::where('blk_pelatihan_id', $pelatihan->id)->findOrFail($pesertaId);

        return [
            'blk_peserta_id' => $pesertaId,
            'perusahaan_peserta_id' => null,
            'pencari_id' => $peserta->pencari_id ? (int) $peserta->pencari_id : null,
        ];
    }

    public function answersQuery(EtamBlkPelatihan $pelatihan, string $jenis, array $keys)
    {
        $query = EtamBlkPelatihanJawaban::query()
            ->where('blk_pelatihan_id', $pelatihan->id)
            ->where('jenis', $jenis);

        if ($keys['perusahaan_peserta_id']) {
            $query->where('perusahaan_peserta_id', $keys['perusahaan_peserta_id']);
        } else {
            $query->where('blk_peserta_id', $keys['blk_peserta_id']);
        }

        return $query;
    }

    public function isSubmitted(EtamBlkPelatihan $pelatihan, string $jenis, array $keys): bool
    {
        return $this->answersQuery($pelatihan, $jenis, $keys)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    /**
     * Salin pertanyaan template ke jawaban peserta saat pertama kali dibuka,
     * supaya edit template kemudian tidak mengubah riwayat peserta.
     *
     * @return Collection<int, EtamBlkPelatihanJawaban>
     */
    public function ensureSnapshot(EtamBlkPelatihan $pelatihan, string $jenis, array $keys): Collection
    {
        $existing = $this->answersQuery($pelatihan, $jenis, $keys)->orderBy('urutan')->get();
        if ($existing->isNotEmpty()) {
            return $existing;
        }

        $form = $this->formFor($pelatihan, $jenis);
        if (! $form) {
            throw ValidationException::withMessages([
                'form' => 'Template belum dipilih untuk pelatihan ini.',
            ]);
        }

        foreach ($form->pertanyaan as $index => $pertanyaan) {
            EtamBlkPelatihanJawaban::create([
                'blk_pelatihan_id' => $pelatihan->id,
                'jenis' => $jenis,
                'form_id' => $form->id,
                'form_pertanyaan_id' => $pertanyaan->id,
                'blk_pertanyaan_id' => null,
                'blk_peserta_id' => $keys['blk_peserta_id'],
                'perusahaan_peserta_id' => $keys['perusahaan_peserta_id'],
                'pencari_id' => $keys['pencari_id'],
                'jenis_pertanyaan' => $pertanyaan->jenis_pertanyaan,
                'urutan' => $pertanyaan->urutan ?: ($index + 1),
                'wajib' => (int) $pertanyaan->wajib,
                'pertanyaan' => $pertanyaan->pertanyaan,
                'pilihan' => $pertanyaan->pilihan,
                'jawaban' => null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return $this->answersQuery($pelatihan, $jenis, $keys)->orderBy('urutan')->get();
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function saveAnswers(EtamBlkPelatihan $pelatihan, string $jenis, array $keys, array $input, bool $submit): Collection
    {
        $rows = $this->ensureSnapshot($pelatihan, $jenis, $keys);

        if ($this->isSubmitted($pelatihan, $jenis, $keys)) {
            throw ValidationException::withMessages([
                'form' => 'Form sudah dikirim dan tidak dapat diubah.',
            ]);
        }

        $errors = [];
        $normalizedMap = [];

        foreach ($rows as $row) {
            $raw = $input[(string) $row->id] ?? $input[$row->id] ?? null;
            $normalized = $this->normalizeAnswer((int) $row->jenis_pertanyaan, $raw);

            if ($submit && (int) $row->wajib === 1 && $normalized === null) {
                $errors['jawaban.'.$row->id] = 'Pertanyaan wajib belum diisi.';
            }

            $normalizedMap[$row->id] = $normalized;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        foreach ($rows as $row) {
            $row->jawaban = $normalizedMap[$row->id];
            $row->updated_by = Auth::id();
            if ($submit) {
                $row->submitted_at = now();
            }
            $row->save();
        }

        return $this->answersQuery($pelatihan, $jenis, $keys)->orderBy('urutan')->get();
    }

    /**
     * @return array<int, string>
     */
    public function pilihanFromSnapshot(EtamBlkPelatihanJawaban $row): array
    {
        $dummy = new EtamBlkFormPertanyaan;
        $dummy->pilihan = $row->pilihan;

        return $dummy->daftarPilihan();
    }

    /**
     * @return array<int, string>
     */
    public function decodedCheckbox(EtamBlkPelatihanJawaban $row): array
    {
        $raw = trim((string) $row->jawaban);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? array_values(array_map('strval', $decoded)) : [$raw];
    }

    private function normalizeAnswer(int $jenisPertanyaan, mixed $raw): ?string
    {
        if ($jenisPertanyaan === EtamBlkFormPertanyaan::JENIS_CHECKBOX) {
            $values = is_array($raw) ? $raw : (filled($raw) ? [$raw] : []);
            $values = array_values(array_filter(array_map('strval', $values), fn ($item) => trim($item) !== ''));

            return $values === [] ? null : json_encode($values);
        }

        if (is_array($raw)) {
            $raw = implode(', ', $raw);
        }

        $value = trim((string) $raw);

        return $value === '' ? null : $value;
    }
}
