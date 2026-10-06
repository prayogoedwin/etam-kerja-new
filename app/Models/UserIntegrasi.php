<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserIntegrasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_integrasi';

    protected $fillable = [
        'username',
        'password',
        'client_id',
        'api_key',
        'access_token',
        'refresh_token',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'access_token_expires_at' => 'datetime',
        'refresh_token_expires_at' => 'datetime',
        'status' => 'integer',
    ];

    public static function generateClientId(): string
    {
        return (string) Str::uuid();
    }

    public static function generateApiKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(40));
    }

    public function issueTokens(int $accessTtlSeconds = 86400, int $refreshTtlSeconds = 2592000): array
    {
        $accessToken = self::generateToken();
        $refreshToken = self::generateToken();

        $this->forceFill([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'access_token_expires_at' => now()->addSeconds($accessTtlSeconds),
            'refresh_token_expires_at' => now()->addSeconds($refreshTtlSeconds),
        ])->save();

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $accessTtlSeconds,
            'refresh_expires_in' => $refreshTtlSeconds,
        ];
    }

    public function isAccessTokenValid(?string $token): bool
    {
        if (! $token || $this->access_token !== $token) {
            return false;
        }

        if ($this->status !== 1) {
            return false;
        }

        return $this->access_token_expires_at && $this->access_token_expires_at->isFuture();
    }

    public function isRefreshTokenValid(?string $token): bool
    {
        if (! $token || $this->refresh_token !== $token) {
            return false;
        }

        if ($this->status !== 1) {
            return false;
        }

        return $this->refresh_token_expires_at && $this->refresh_token_expires_at->isFuture();
    }
}
