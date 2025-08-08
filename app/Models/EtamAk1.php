<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamAk1 extends Model
{
    //
    use HasFactory, SoftDeletes;

    // Tentukan nama tabel jika berbeda dari plural model
    protected $table = 'etam_ak1';

    // Tentukan atribut yang dapat diisi (mass assignable)
    protected $fillable = [
        'id_user',
        'tanggal_cetak',
        'berlaku_hingga',
        'status_cetak',
        'dicetak_oleh',
        'qr',
        'unik_kode',
    ];

    // Tentukan format tanggal jika ada
    protected $dates = [
        'tanggal_cetak',
        'berlaku_hingga',
        'deleted_at', // Untuk soft delete
    ];

    // Relasi dengan tabel users (User yang mencetak AK1)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi dengan tabel users (Admin yang mencetak AK1 jika ada)
    public function dicetakOleh()
    {
        return $this->belongsTo(User::class, 'dicetak_oleh');
    }
}
