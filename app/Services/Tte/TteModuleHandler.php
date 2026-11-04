<?php

namespace App\Services\Tte;

use App\Models\EtamTte;

/**
 * Kontrak TTE untuk SEMUA modul, bukan khusus BLK.
 *
 * Engine TTE tidak tahu BLK/HI/Suket. Setelah pejabat meneken, engine hanya:
 * - update kolom_status_refrensi = nilaiStatusSukses()
 * - panggil setelahBerhasil($tte) pada handler yang terdaftar di config/tte.php
 *
 * Isi setelahBerhasil() milik modul itu sendiri (BLK generate kompetensi/sertifikat
 * lalu enqueue Kadis; HI nanti unggah SK; dst). Modul baru = class handler baru,
 * engine tidak diubah.
 */
interface TteModuleHandler
{
    public function modul(): string;

    public function modelClass(): string;

    public function judul(EtamTte $tte): string;

    public function nilaiStatusSukses(EtamTte $tte): mixed;

    public function setelahBerhasil(EtamTte $tte): void;

    public function setelahGagal(EtamTte $tte): void;
}
