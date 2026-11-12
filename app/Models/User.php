<?php

namespace App\Models;

use App\Models\BLK\UserBlk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;  // Add HasRoles here

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'whatsapp',
        'nik',
        'lokasi_kerja',
        'kode_struktur',
        'password',
        'otp',
        'is_finished',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $dates = ['deleted_at'];

    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'posted_by');
    }

    public function pencari()
    {
        return $this->hasOne(UserPencari::class, 'user_id', 'id');
        // 'user_id' adalah foreign key di tabel 'pencaris' yang merujuk ke 'id' pada tabel 'users'
    }

    public function penyedia()
    {
        return $this->hasOne(UserPenyedia::class, 'user_id', 'id');
        // 'user_id' adalah foreign key di tabel 'penyedias' yang merujuk ke 'id' pada tabel 'users'
    }

    public function admin()
    {
        return $this->hasOne(UserAdmin::class, 'user_id', 'id');
    }

    public function blk()
    {
        return $this->hasOne(UserBlk::class, 'user_id', 'id');
    }

    public static function nikDariInput(mixed $nilai): ?string
    {
        $nik = preg_replace('/\D/', '', (string) $nilai);

        return $nik === '' ? null : $nik;
    }

    /**
     * @return array<int, mixed>
     */
    public static function aturanNik(?int $ignoreUserId = null): array
    {
        $unique = Rule::unique('users', 'nik')->whereNull('deleted_at');
        if ($ignoreUserId) {
            $unique->ignore($ignoreUserId);
        }

        return ['nullable', 'digits:16', $unique];
    }

    public function nikSiapTte(): ?string
    {
        $nik = self::nikDariInput($this->nik);

        return $nik !== null && strlen($nik) === 16 ? $nik : null;
    }

    public function adalahKepalaDinas(): bool
    {
        return $this->hasRole('eksekutif-provinsi')
            && (string) $this->kode_struktur === EtamStruktur::KODE_DINAS;
    }

    public function bisaAksesTte(): bool
    {
        return $this->hasRole('super-admin')
            || $this->hasRole('kepala-balai')
            || $this->adalahKepalaDinas();
    }
}
