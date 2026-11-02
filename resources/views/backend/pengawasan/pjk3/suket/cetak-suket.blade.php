@php
    $perusahaan = $suket->profilPerusahaan;
    $isDraft = $suket->isDraft();
    $layak = (int) $suket->is_layak === 1;
    $judul = $layak ? 'SURAT KETERANGAN LAYAK K3' : 'SURAT KETERANGAN TIDAK LAYAK K3';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Suket {{ $suket->nomor_suket }}</title>
    <style>
        body { font-family: "Times New Roman", serif; font-size: 12pt; color: #000; margin: 28px; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 18px; }
        .kop h3, .kop h4, .kop p { margin: 0; }
        h2 { text-align: center; text-decoration: underline; font-size: 14pt; margin: 18px 0 4px; }
        .nomor { text-align: center; margin-bottom: 18px; }
        p { text-align: justify; line-height: 1.5; }
        table.data { width: 100%; border-collapse: collapse; margin: 12px 0; }
        table.data td { vertical-align: top; padding: 3px 0; }
        .hasil { text-align: center; font-size: 18pt; font-weight: bold; margin: 20px 0; letter-spacing: 1px; }
        .ttd { width: 48%; float: right; text-align: center; margin-top: 28px; }
        .watermark {
            position: fixed; top: 38%; left: 12%; font-size: 72pt; color: rgba(200,0,0,.18);
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
        <p>Jalan Kesuma Bangsa No. 4, Samarinda</p>
    </div>

    <h2>{{ $judul }}</h2>
    <p class="nomor">Nomor: {{ $suket->nomor_suket ?? '-' }}</p>

    <p>Yang bertanda tangan di bawah ini, Kepala Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur,
        menerangkan bahwa objek K3 berikut:</p>

    <table class="data">
        <tr>
            <td width="32%">Nama perusahaan</td>
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
            <td>Objek / alat</td>
            <td>: {{ $suket->nama_alat }}</td>
        </tr>
        <tr>
            <td>Jenis / kategori</td>
            <td>: {{ $suket->jenis->nama ?? '-' }} / {{ $suket->kategori->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td>: {{ $suket->lokasi_alat }}</td>
        </tr>
        <tr>
            <td>Tanggal pemeriksaan</td>
            <td>: {{ optional($suket->tanggal_pemeriksaan)->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td>PJK3 pemeriksa</td>
            <td>: {{ $suket->profilPjk3->name ?? '-' }}</td>
        </tr>
    </table>

    <p>Berdasarkan hasil pemeriksaan dan pengujian yang dituangkan dalam LHPP nomor
        <strong>{{ $suket->nomor_suket ?? '-' }}</strong>, objek dimaksud dinyatakan:</p>

    <div class="hasil">{{ $layak ? 'LAYAK' : 'TIDAK LAYAK' }}</div>

    <p>{{ $layak
        ? 'dengan demikian objek tersebut dapat dioperasikan sesuai ketentuan peraturan perundang-undangan di bidang keselamatan dan kesehatan kerja.'
        : 'dengan demikian objek tersebut belum dapat dioperasikan sampai dilakukan perbaikan dan pemeriksaan ulang sesuai ketentuan peraturan perundang-undangan.'
    }}</p>

    <div class="ttd">
        <p>Kepala Dinas</p>
        @if ($suket->ttd_kadis_at)
            <p><em>Telah ditandatangani secara elektronik</em><br>
                {{ optional($suket->ttd_kadis_at)->format('d-m-Y H:i') }}</p>
        @else
            <br><br><br>
        @endif
        <p><strong><u>Kepala Dinas Tenaga Kerja dan Transmigrasi</u></strong></p>
        <p>Pengawas: {{ $suket->pengawas->name ?? '-' }}
            @if ($suket->ttd_pengawas_at)
                (TTE {{ optional($suket->ttd_pengawas_at)->format('d-m-Y') }})
            @endif
        </p>
    </div>
</body>
</html>
