<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi TTE{{ $judul ? ' — '.$judul : '' }}</title>
    <style>
        :root {
            --hijau: #0f5c2e;
            --hijau-tua: #0a3d1e;
            --ok: #1b7a3d;
            --wait: #c05612;
            --fail: #b42318;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: #eef2f1;
            color: #1a1a1a;
            min-height: 100vh;
        }
        .top {
            background: linear-gradient(180deg, var(--hijau) 0%, var(--hijau-tua) 100%);
            color: #fff;
            padding: 14px 20px 16px;
        }
        .top-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .top img { height: 52px; width: auto; background: #fff; border-radius: 8px; padding: 4px; }
        .top h1 { font-size: 18px; margin: 0 0 2px; letter-spacing: .3px; }
        .top p { margin: 0; font-size: 12px; opacity: .9; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 16px 16px 32px; }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(15, 40, 24, .08);
            overflow: hidden;
        }
        .meta { padding: 16px 20px; border-bottom: 1px solid #e6ece8; }
        .meta table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .meta th {
            text-align: left;
            width: 160px;
            color: #5b6b62;
            font-weight: 600;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        .meta td { padding: 5px 0; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.ok { background: var(--ok); }
        .badge.wait { background: var(--wait); }
        .badge.fail { background: var(--fail); }
        .aksi { margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap; }
        .btn {
            display: inline-block;
            background: var(--hijau);
            color: #fff;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }
        .btn.outline { background: #fff; color: var(--hijau); border: 1px solid var(--hijau); }
        .viewer {
            background: #d9e0dc;
            min-height: 70vh;
        }
        .viewer iframe {
            display: block;
            width: 100%;
            height: 78vh;
            border: 0;
            background: #525659;
        }
        .kosong {
            min-height: 52vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            text-align: center;
        }
        .kosong-box {
            max-width: 460px;
            border: 2px dashed #c5d2cb;
            border-radius: 12px;
            padding: 28px 22px;
            background: #f7faf8;
        }
        .kosong h2 { margin: 0 0 8px; font-size: 18px; color: var(--hijau-tua); }
        .kosong p { margin: 0; color: #5b6b62; line-height: 1.5; }
        .ikon {
            width: 64px;
            height: 64px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: #e4eee8;
            color: var(--hijau);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
        }
        @media (max-width: 720px) {
            .top-inner { align-items: flex-start; }
            .meta th { display: block; width: auto; padding-bottom: 0; }
            .meta td { display: block; padding-top: 2px; padding-bottom: 10px; }
            .viewer iframe { height: 62vh; }
        }
    </style>
</head>
<body>
    <header class="top">
        <div class="top-inner">
            <img src="{{ asset('assets/etam_fe/images/logo/logo_kaltim.png') }}" alt="Lambang Kalimantan Timur">
            <div>
                <h1>ETAM KERJA — Verifikasi TTE</h1>
                <p>Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur</p>
            </div>
        </div>
    </header>

    <main class="wrap">
        <div class="card">
            <div class="meta">
                <table>
                    <tr>
                        <th>Modul</th>
                        <td>{{ $labelModul }}</td>
                    </tr>
                    <tr>
                        <th>Perihal</th>
                        <td>{{ $judul ?: 'Dokumen Tanda Tangan Elektronik' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $tanggal }}</td>
                    </tr>
                    <tr>
                        <th>Status TTE</th>
                        <td><span class="badge {{ $status['kelas'] }}">{{ $status['label'] }}</span></td>
                    </tr>
                </table>
                @if ($urlBerkas)
                    <div class="aksi">
                        <a class="btn" href="{{ $urlBerkas }}" target="_blank" rel="noopener">Lihat File</a>
                        @if ($urlUnduh)
                            <a class="btn outline" href="{{ $urlUnduh }}">Unduh PDF</a>
                        @endif
                    </div>
                @endif
            </div>

            @if ($urlBerkas)
                <div class="viewer">
                    <iframe src="{{ $urlBerkas }}" title="Berkas asli TTE"></iframe>
                </div>
            @else
                <div class="kosong">
                    <div class="kosong-box">
                        <div class="ikon">!</div>
                        <h2>{{ $tte ? 'Berkas asli belum tersedia' : 'Dokumen tidak ditemukan' }}</h2>
                        <p>{{ $pesanKosong }}</p>
                    </div>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
