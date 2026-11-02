<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - {{ $sertifikat['peserta']['nama'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Times New Roman", Times, "Liberation Serif", serif;
            font-size: 12pt;
            font-weight: 400;
            line-height: 1.4;
            color: #000;
            background: #e5e5e5;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #1b5e20;
            color: #fff;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .toolbar a, .toolbar button {
            background: #fff;
            color: #1b5e20;
            border: 0;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-size: 13px;
            cursor: pointer;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 10mm auto;
            background: #fff;
            padding: 10mm;
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
            position: relative;
            page-break-after: always;
        }

        .page:last-of-type { page-break-after: auto; }

        .frame {
            min-height: calc(297mm - 20mm);
            border: 1.5px solid #000;
            padding: 10mm 16mm 14mm;
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 46%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-28deg);
            font-size: 92px;
            font-weight: 700;
            letter-spacing: 8px;
            color: rgba(176, 0, 0, .12);
            pointer-events: none;
            z-index: 1;
            text-transform: uppercase;
        }

        .content { position: relative; z-index: 2; }

        .header {
            text-align: center;
            border-bottom: 1.6px solid #000;
            padding-bottom: 7px;
            margin-bottom: 16px;
        }

        .header .logo-wrap {
            margin: 2mm 0 4mm;
        }

        .header img {
            display: block;
            margin: 0 auto;
            width: 24mm;
            height: auto;
        }

        .header .gov {
            font-size: 13pt;
            font-weight: 400;
            letter-spacing: 0.6px;
            line-height: 1.25;
        }

        .header .dinas {
            font-size: 15.5pt;
            font-weight: 700;
            letter-spacing: 0.2px;
            line-height: 1.2;
            margin-top: 1px;
        }

        .header .uptd {
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 0.15px;
            line-height: 1.2;
            margin-top: 1px;
        }

        .header .alamat {
            font-size: 11pt;
            font-weight: 400;
            margin-top: 2px;
            line-height: 1.3;
            white-space: pre-line;
        }

        .judul {
            text-align: center;
            margin: 14px 0 3px;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .nomor {
            text-align: center;
            margin-bottom: 16px;
            font-size: 12pt;
            font-weight: 400;
        }

        .pernyataan {
            text-align: left;
            font-size: 12pt;
            font-weight: 400;
            margin: 0 2mm 4px;
        }

        .identitas {
            width: auto;
            margin: 0 2mm 12px;
            border-collapse: collapse;
        }

        .identitas td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 12pt;
            font-weight: 400;
        }

        .identitas .label {
            width: 48mm;
            white-space: nowrap;
        }

        .identitas .sep {
            width: 14px;
            padding-right: 6px;
        }

        .uraian {
            text-align: justify;
            margin: 8px 2mm 0;
            font-size: 12pt;
            font-weight: 400;
            line-height: 1.45;
        }

        .ttd {
            width: 78mm;
            margin: 18mm 4mm 0 auto;
            text-align: center;
            font-size: 12pt;
            font-weight: 400;
            line-height: 1.4;
        }

        .ttd .space {
            height: 26mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            font-size: 9pt;
            font-style: italic;
            font-weight: 400;
        }

        .ttd .nama {
            font-weight: 700;
            text-decoration: underline;
            text-transform: uppercase;
            text-underline-offset: 2px;
            min-height: 18px;
        }

        .section-title {
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 6px 0 12px;
        }

        table.kompetensi {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            font-weight: 400;
            margin-bottom: 12px;
        }

        table.kompetensi th, table.kompetensi td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }

        table.kompetensi th { text-align: center; font-weight: 700; }
        table.kompetensi .no { width: 28px; text-align: center; }
        table.kompetensi .kode { width: 28%; }
        table.kompetensi .hasil { width: 70px; text-align: center; }

        .catatan {
            margin-top: 18px;
            font-size: 9pt;
            color: #555;
            font-style: italic;
        }

        @media print {
            body { background: none; }
            .toolbar { display: none; }
            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .frame {
                min-height: calc(297mm - 16mm);
                border: 1.6px solid #000;
            }
            .watermark { color: rgba(176, 0, 0, .18); -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>{{ $sertifikat['is_draft'] ? 'Draf sertifikat — belum TTE' : 'Sertifikat' }}</div>
        <div>
            <button type="button" onclick="window.print()">Cetak / PDF</button>
            <a href="{{ route('blk.pelatihan.peserta.show', [$pelatihan->id, $peserta->id]) }}">Kembali</a>
        </div>
    </div>

    <div class="page">
        <div class="frame">
        @if ($sertifikat['is_draft'])
            <div class="watermark">DRAFT</div>
        @endif
        <div class="content">
            <div class="header">
                <div class="logo-wrap">
                    <img src="{{ asset('assets/etam_fe/images/logo/logo_kaltim.png') }}" alt="Lambang Kalimantan Timur">
                </div>
                <div class="gov">{{ $sertifikat['kop']['pemerintah'] }}</div>
                <div class="dinas">{{ $sertifikat['kop']['dinas'] }}</div>
                @if ($sertifikat['kop']['uptd'])
                    <div class="uptd">{{ $sertifikat['kop']['uptd'] }}</div>
                @endif
                @if ($sertifikat['kop']['alamat'])
                    <div class="alamat">{{ $sertifikat['kop']['alamat'] }}</div>
                @endif
            </div>

            <div class="judul">SERTIFIKAT KELULUSAN PESERTA</div>
            <div class="nomor">NOMOR: {{ $sertifikat['nomor'] }}</div>
            <div class="pernyataan">Dengan ini menyatakan bahwa:</div>

            <table class="identitas">
                <tr>
                    <td class="label">Nama</td>
                    <td class="sep">:</td>
                    <td>{{ $sertifikat['peserta']['nama'] }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="sep">:</td>
                    <td>{{ $sertifikat['peserta']['nik'] }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat, Tanggal Lahir</td>
                    <td class="sep">:</td>
                    <td>{{ $sertifikat['peserta']['ttl'] }}</td>
                </tr>
            </table>

            <p class="uraian">{{ $sertifikat['pelatihan']['uraian'] }}</p>

            <div class="ttd">
                <div>{{ $sertifikat['penandatangan']['dinas']['tempat'] }}, {{ $sertifikat['penandatangan']['dinas']['tanggal'] }}</div>
                <div>{{ $sertifikat['penandatangan']['dinas']['instansi'] }}</div>
                <div>{{ $sertifikat['penandatangan']['dinas']['jabatan'] }}</div>
                <div class="space">
                    @if ($sertifikat['is_draft'])
                        Menunggu TTE
                    @endif
                </div>
                <div class="nama">{{ $sertifikat['penandatangan']['dinas']['nama'] ?: '................................' }}</div>
            </div>
        </div>
        </div>
    </div>

    <div class="page">
        <div class="frame">
        @if ($sertifikat['is_draft'])
            <div class="watermark">DRAFT</div>
        @endif
        <div class="content">
            <div class="section-title">DAFTAR UNIT KOMPETENSI</div>

            <table class="kompetensi">
                <thead>
                    <tr>
                        <th class="no">No.</th>
                        <th>Unit Kompetensi Inti</th>
                        <th class="kode">Kode Unit</th>
                        <th class="hasil">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sertifikat['kompetensi_inti'] as $item)
                        <tr>
                            <td class="no">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kodeLabel() }}</td>
                            <td class="hasil">Lulus</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="no">-</td>
                            <td colspan="3">Belum ada unit kompetensi inti</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="kompetensi">
                <thead>
                    <tr>
                        <th class="no">No.</th>
                        <th>Unit Kompetensi Penunjang</th>
                        <th class="kode">Kode Unit</th>
                        <th class="hasil">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sertifikat['kompetensi_penunjang'] as $item)
                        <tr>
                            <td class="no">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kodeLabel() }}</td>
                            <td class="hasil">Lulus</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="no">-</td>
                            <td colspan="3">Belum ada unit kompetensi penunjang</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="ttd">
                <div>{{ $sertifikat['penandatangan']['balai']['tempat'] }}, {{ $sertifikat['penandatangan']['balai']['tanggal'] }}</div>
                <div>{{ $sertifikat['penandatangan']['balai']['instansi'] }}</div>
                <div>{{ $sertifikat['penandatangan']['balai']['jabatan'] }}</div>
                <div class="space">
                    @if ($sertifikat['is_draft'])
                        Menunggu TTE
                    @endif
                </div>
                <div class="nama">{{ $sertifikat['penandatangan']['balai']['nama'] ?: '................................' }}</div>
            </div>

            @if ($sertifikat['is_draft'])
                <p class="catatan">Dokumen draf. Nama penanda tangan sudah terisi, tanpa nomor induk pegawai. Proses tanda tangan elektronik (TTE) belum dijalankan.</p>
            @endif
        </div>
        </div>
    </div>
</body>
</html>
