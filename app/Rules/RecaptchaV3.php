<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecaptchaV3 implements ValidationRule
{
    protected float $minScore;

    public function __construct(float $minScore = 0.5)
    {
        $this->minScore = $minScore;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->shouldSkip()) {
            return;
        }

        if (blank($value)) {
            $fail('Verifikasi reCAPTCHA wajib diisi.');

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (ConnectionException $e) {
            Log::warning('reCAPTCHA connection failed: '.$e->getMessage());

            if (app()->environment('local')) {
                return;
            }

            $fail('Tidak dapat menghubungi layanan reCAPTCHA. Coba lagi beberapa saat.');

            return;
        } catch (Throwable $e) {
            Log::error('reCAPTCHA unexpected error: '.$e->getMessage());
            $fail('Verifikasi reCAPTCHA gagal. Coba lagi.');

            return;
        }

        $result = $response->json() ?? [];

        if (! ($result['success'] ?? false)) {
            Log::warning('reCAPTCHA failed - success false', $result);
            $fail('Verifikasi reCAPTCHA gagal.');

            return;
        }

        if (($result['score'] ?? 0) < $this->minScore) {
            Log::warning('reCAPTCHA failed - score too low: '.($result['score'] ?? 0));
            $fail('Verifikasi reCAPTCHA ditolak. Coba login ulang.');
        }
    }

    private function shouldSkip(): bool
    {
        if (config('services.recaptcha.skip')) {
            return true;
        }

        if (! config('services.recaptcha.secret_key')) {
            return app()->environment('local');
        }

        return false;
    }
}
