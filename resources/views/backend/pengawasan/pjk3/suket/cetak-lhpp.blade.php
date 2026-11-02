@php
    $perusahaan = $suket->profilPerusahaan;
    $pjk3 = $suket->profilPjk3;
    $isDraft = $suket->isDraft();
    $hasil = (int) $suket->is_layak === 1 ? 'LAYAK' : 'TIDAK LAYAK';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>LHPP {{ $suket->nomor_suket }}</title>
    <style>
        body { font-family: "Times New Roman", serif; font-size: 12pt; color: #000; margin: 24px; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
        .kop h3, .kop h4, .kop p { margin: 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data td { vertical-align: top; padding: 3px 0; }
        .hasil { font-size: 16pt; font-weight: bold; text-align: center; margin: 18px 0; }
        .ttd { width: 45%; float: right; text-align: center; margin-top: 28px; }
        .watermark {
            position: fixed; top: 38%; left: 10%; font-size: 72pt; color: rgba(200,0,0,.18);
            transform: rotate(-24deg); z-index: 0; pointer-events: none; font-weight: bold;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    @if ($isDraft)
        <div class="watermark">DRAFT</div>
    @endif
    <p class="no-print"><button onclick="window.print()">Cetak</button></p>
    <div class="kop">
        <h3>PEMERINTAH PROVINSI KALIMANTAN TIMUR</h3>
        <h4>DINAS TENAGA KERJA DAN TRANSMIGRASI</h4>
        <p>Bidang Pembinaan dan Pengawasan Ketenagakerjaan</p>
        <p>Laporan Hasil Pemeriksaan dan Pengujian (LHPP) K3</p>
    </div>

    <table class="data">
        <tr>
            <td width="32%">Nomor</td>
            <td>: {{ $suket->nomor_suket ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal pemeriksaan</td>
            <td>: {{ optional($suket->tanggal_pemeriksaan)->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td>Perusahaan yang diuji</td>
            <td>: {{ $perusahaan->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $perusahaan->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIB</td>
            <td>: {{ $perusahaan->nib ?? '-' }}</td>
        </tr>
        <tr>
            <td>PJK3 pemeriksa</td>
            <td>: {{ $pjk3->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kategori / jenis</td>
            <td>: {{ $suket->kategori->nama ?? '-' }} / {{ $suket->jenis->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama alat / objek</td>
            <td>: {{ $suket->nama_alat }}</td>
        </tr>
        <tr>
            <td>Lokasi alat</td>
            <td>: {{ $suket->lokasi_alat }}</td>
        </tr>
        <tr>
            <td>Kapasitas / jumlah</td>
            <td>: {{ $suket->kapasitas_alat ?: '-' }} / {{ $suket->jumlah_unit }} unit</td>
        </tr>
    </table>

    <p><strong>Hasil analisis:</strong></p>
    <p>{!! nl2br(e($suket->hasil_analisis ?: '-')) !!}</p>
    <p><strong>Rekomendasi:</strong></p>
    <p>{!! nl2br(e($suket->rekomendasi ?: '-')) !!}</p>

    <div class="hasil">KESIMPULAN: {{ $hasil }} DIPAKAI / DIOPERASIKAN</div>

    <div class="ttd">
        <p>Pengawas Ketenagakerjaan</p>
        @if ($suket->ttd_pengawas_at)
            <p><em>Telah ditandatangani secara elektronik</em><br>
                {{ optional($suket->ttd_pengawas_at)->format('d-m-Y H:i') }}</p>
        @else
            <br><br><br>
        @endif
        <p><strong><u>{{ $suket->pengawas->name ?? '................................' }}</u></strong></p>
    </div>
</body>
</html>
