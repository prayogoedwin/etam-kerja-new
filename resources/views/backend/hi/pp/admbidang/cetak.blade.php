<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pengajuan Peraturan Perusahaan — {{ $ajuan->nomor ?? 'Tanpa Nomor' }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            color: #000;
            margin: 30px 40px;
            line-height: 1.5;
        }
        h1, h2, h3 {
            text-align: center;
            margin: 0 0 5px 0;
        }
        h1 { font-size: 18px; text-transform: uppercase; }
        h2 { font-size: 15px; }
        h3 { font-size: 14px; margin-bottom: 20px; }
        .header {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header small { display: block; text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        table.data th {
            background: #f0f0f0;
            text-align: left;
            width: 35%;
        }
        table.doc th, table.doc td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        table.doc th {
            background: #f0f0f0;
            text-align: center;
        }
        .section-title {
            font-weight: bold;
            margin: 20px 0 8px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .ttd {
            margin-top: 50px;
            width: 100%;
            text-align: right;
        }
        .ttd div { display: inline-block; text-align: center; }
        .ttd .space { height: 60px; }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .no-print button {
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        @media print {
            body { margin: 15mm; }
            .no-print { display: none; }
            a { color: #000; text-decoration: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()">
            🖨️ Cetak / Simpan sebagai PDF
        </button>
    </div>

    {{-- HEADER --}}
    <div class="header">
        <h1>Pemerintah Provinsi</h1>
        <h2>Dinas Tenaga Kerja dan Transmigrasi</h2>
        <h3>Peraturan Perusahaan</h3>
        <small>Dokumen ini dicetak pada {{ now()->format('d F Y H:i') }}</small>
    </div>

    {{-- DATA PENGAJUAN --}}
    <div class="section-title">A. Data Pengajuan</div>
    <table class="data">
        <tr>
            <th>Nomor Pengajuan</th>
            <td>{{ $ajuan->nomor ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jenis Ajuan</th>
            <td>{{ $ajuan->jenisAjuan->nama ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ $ajuan->tanggal ? \Carbon\Carbon::parse($ajuan->tanggal)->format('d-m-Y') : '-' }}</td>
        </tr>
        <tr>
            <th>Surat Keputusan Izin Usaha</th>
            <td>{{ $ajuan->surat_keputusan_izin_usaha ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nama Serikat Pekerja</th>
            <td>{{ $ajuan->nama_serikat_pekerja ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nomor Peserta BPJS</th>
            <td>{{ $ajuan->nomor_peserta_bpjs ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jumlah Pekerja Pusat</th>
            <td>{{ $ajuan->jumlah_pekerja_pusat ?? 0 }} orang</td>
        </tr>
        <tr>
            <th>Jumlah Pekerja Cabang</th>
            <td>{{ $ajuan->jumlah_pekerja_cabang ?? 0 }} orang</td>
        </tr>
    </table>

    {{-- UPAH --}}
    <div class="section-title">B. Upah Pekerja</div>
    <table class="data">
        <tr>
            <th>Upah Bulanan Minimum</th>
            <td>Rp {{ number_format($ajuan->upah_pekerja_bulanan_min ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Upah Bulanan Maximum</th>
            <td>Rp {{ number_format($ajuan->upah_pekerja_bulanan_max ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Upah Harian Minimum</th>
            <td>Rp {{ number_format($ajuan->upah_pekerja_harian_min ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Upah Harian Maximum</th>
            <td>Rp {{ number_format($ajuan->upah_pekerja_harian_max ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- SISTEM HUBUNGAN KERJA --}}
    <div class="section-title">C. Sistem Hubungan Kerja</div>
    <table class="data">
        <tr>
            <th>Waktu Tertentu</th>
            <td>{{ $ajuan->sistem_hub_kerja_tertentu ?? 0 }} orang</td>
        </tr>
        <tr>
            <th>Waktu Tidak Tertentu</th>
            <td>{{ $ajuan->sistem_hub_kerja_tidak_tertentu ?? 0 }} orang</td>
        </tr>
    </table>

    {{-- DOKUMEN --}}
    <div class="section-title">D. Dokumen Syarat</div>
    <table class="doc">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Syarat Dokumen</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($syaratDokumen as $i => $dok)
                @php $existing = $uploadedDokumen[$dok->id] ?? null; @endphp
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $dok->nama }}</td>
                    <td style="text-align:center;">
                        {{ $existing ? 'Terlampir' : 'Tidak Terlampir' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align:center;">Tidak ada syarat dokumen.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- VERIFIKASI --}}
    <div class="section-title">E. Status Verifikasi</div>
    <table class="data">
        <tr>
            <th>Verifikasi Admin</th>
            <td>
                @if ((int) $ajuan->verifikasi_admin === 1) ACC
                @elseif ((int) $ajuan->verifikasi_admin === 2) Revisi
                @else Menunggu
                @endif
            </td>
        </tr>
        <tr>
            <th>Verifikasi Kasi</th>
            <td>
                @if ((int) $ajuan->verifikasi_kasi === 1) ACC
                @elseif ((int) $ajuan->verifikasi_kasi === 2) Revisi
                @else Menunggu
                @endif
            </td>
        </tr>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        <div>
            <p>{{ now()->format('d F Y') }}</p>
            <p>Kepala Bidang Hubungan Industrial,</p>
            <div class="space"></div>
            <p><strong>(.............................................)</strong></p>
        </div>
    </div>

</body>
</html>
