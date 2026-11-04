<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi berkas TTE</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 24px; color: #222; }
        .card { max-width: 640px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        h1 { font-size: 20px; margin: 0 0 12px; }
        .muted { color: #666; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; color: #fff; font-size: 12px; }
        .ok { background: #2e7d32; }
        .wait { background: #ef6c00; }
        .fail { background: #c62828; }
        a.btn { display: inline-block; margin-top: 16px; background: #1b5e20; color: #fff; text-decoration: none; padding: 8px 14px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Verifikasi berkas TTE</h1>
        <p>{{ $judul }}</p>
        <p class="muted">Modul: {{ $tte->nama_modul }}</p>
        <p>
            @if ((int) $tte->status === \App\Models\EtamTte::STATUS_BERHASIL)
                <span class="badge ok">Sudah ditandatangani</span>
            @elseif ((int) $tte->status === \App\Models\EtamTte::STATUS_GAGAL)
                <span class="badge fail">Gagal TTE</span>
            @else
                <span class="badge wait">Menunggu TTE</span>
            @endif
        </p>
        @if ($urlUnduh)
            <a class="btn" href="{{ $urlUnduh }}">Unduh berkas asli (PDF)</a>
        @elseif (! $adaBerkas)
            <p class="muted">Berkas belum tersedia.</p>
        @else
            <p class="muted">Berkas asli akan dapat diunduh setelah TTE selesai.</p>
        @endif
    </div>
</body>
</html>
