<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamNotification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'etam_notification';

    /**
     * Disable updated_at karena tidak ada di tabel
     */
    const UPDATED_AT = null;

    /**
     * Kolom yang bisa diisi massal
     */
    protected $fillable = [
        'from_user',
        'to_user',
        'table_target',
        'id_target',
        'url_redirection',
        'is_open',
        'is_email',
        'is_whatsapp',
        'info',
        'created_at',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'is_open' => 'boolean',
        'is_email' => 'boolean',
        'is_whatsapp' => 'boolean',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke user pengirim
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'from_user');
    }

    /**
     * Relasi ke user penerima
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'to_user');
    }

    /**
     * Scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_open', false);
    }

    /**
     * Scope untuk notifikasi user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('to_user', $userId);
    }

    /**
     * Mark notifikasi sebagai dibaca
     */
    public function markAsRead()
    {
        $this->is_open = true;
        $this->save();
    }
}