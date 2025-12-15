<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class RecaptchaV3 implements ValidationRule
{
    protected float $minScore;

    public function __construct(float $minScore = 0.5)
    {
        $this->minScore = $minScore;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Debug: cek token diterima atau tidak
        \Log::info('reCAPTCHA token received: ' . substr($value, 0, 50) . '...');
        
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $result = $response->json();
        
        // Debug: lihat full response dari Google
        \Log::info('reCAPTCHA response: ', $result);

        if (!($result['success'] ?? false)) {
            \Log::error('reCAPTCHA failed - success false', $result);
            $fail('Verifikasi reCAPTCHA gagal: ' . json_encode($result['error-codes'] ?? []));
            return;
        }
        
        if (($result['score'] ?? 0) < $this->minScore) {
            \Log::error('reCAPTCHA failed - score too low: ' . $result['score']);
            $fail('Skor reCAPTCHA terlalu rendah: ' . $result['score']);
            return;
        }
    }
}