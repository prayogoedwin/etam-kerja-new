<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - {{ $sertifikat['peserta']['nama'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
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
            margin: 10mm auto;
            background: #fff;
            padding: 10mm;
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
            position: relative;
            page-break-after: auto;
        }

        .sheet {
            width: 100%;
            border: 1.5px solid #000;
            position: relative;
        }

        .sheet-main {
            padding: 10mm 12mm 6mm;
        }

        .sheet-foot {
            padding: 0 12mm 6mm;
        }

        body:not(.mode-pdf) .page {
            min-height: 297mm;
        }

        body:not(.mode-pdf) .sheet {
            min-height: 257mm;
            display: flex;
            flex-direction: column;
        }

        body:not(.mode-pdf) .sheet-foot {
            margin-top: auto;
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
            margin: 12mm 4mm 0 auto;
            text-align: center;
            font-size: 12pt;
            font-weight: 400;
            line-height: 1.4;
        }

        .ttd .space {
            height: 22mm;
            text-align: center;
            color: #888;
            font-size: 9pt;
            font-style: italic;
            font-weight: 400;
        }

        .ttd-bsre-wrap {
            width: 100%;
            border-collapse: collapse;
            margin: 2mm 0 1mm;
        }

        .ttd-bsre-wrap td {
            text-align: center;
            vertical-align: middle;
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

        .bsre-footer {
            width: 100%;
            border-collapse: collapse;
            margin-top: auto;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.35;
            color: #222;
            font-style: normal;
            font-weight: 400;
        }

        .bsre-footer .bsre-logo {
            width: 38mm;
            vertical-align: middle;
            padding: 2mm 6px 0 0;
        }

        .bsre-footer .bsre-logo img {
            width: 36mm;
            height: auto;
        }

        .bsre-footer .bsre-text {
            vertical-align: middle;
            text-align: left;
            padding-top: 2mm;
        }

        .qr-tte {
            margin-top: 4px;
            text-align: center;
        }

        .qr-tte img {
            width: 22mm;
            height: 22mm;
        }

        .qr-tte small {
            display: block;
            font-size: 7pt;
            color: #444;
        }

        .ttd .space .tte-ok {
            color: #1b5e20;
            font-style: italic;
            font-size: 8.5pt;
        }

        body.mode-pdf {
            background: #fff;
            margin: 0;
            padding: 0;
        }

        body.mode-pdf .page {
            width: auto;
            height: auto;
            min-height: 0;
            margin: 0;
            padding: 0;
            box-shadow: none;
        }

        body.mode-pdf .sheet {
            width: 194mm;
            height: 277mm;
            margin: 8mm;
            border: 2px solid #000;
            position: relative;
            overflow: hidden;
        }

        body.mode-pdf .sheet-main {
            padding: 8mm 10mm 28mm;
        }

        body.mode-pdf .sheet-foot {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            padding: 2mm 12mm 6mm;
        }

        body.mode-pdf .ttd {
            margin-top: 8mm;
        }

        body.mode-pdf .ttd .space {
            height: auto;
            min-height: 22mm;
            text-align: center;
        }

        .ttd .space .ttd-bsre {
            width: 32mm;
            height: auto;
            display: inline-block;
            margin: 0 auto;
        }

        body.mode-pdf .bsre-footer {
            position: static;
            width: 100%;
            margin: 0;
        }

        body.mode-pdf .bsre-footer .bsre-logo,
        body.mode-pdf .bsre-footer .bsre-text {
            padding-top: 0;
        }

        @media print {
            body { background: none; }
            .toolbar { display: none; }
            body.mode-pdf .page {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .watermark { color: rgba(176, 0, 0, .18); -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .bsre-footer { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body @class(['mode-pdf' => ($modeUnduh ?? false) || ($untukDs ?? false)])>
    @php
        $halaman = in_array($halaman ?? '', ['sertifikat', 'kompetensi'], true) ? $halaman : 'sertifikat';
        $modeUnduh = $modeUnduh ?? false;
        $untukDs = $untukDs ?? $modeUnduh;
        $tampilanAdmin = $tampilanAdmin ?? false;
        $urlVerifikasi = $urlVerifikasi ?? null;
        $qrVerifikasi = $qrVerifikasi ?? null;
        $signedSertifikat = (bool) ($sertifikat['signed']['sertifikat'] ?? false);
        $signedKompetensi = (bool) ($sertifikat['signed']['kompetensi'] ?? false);
        $lihatSertifikatResmi = ! $untukDs && ($tampilanAdmin || $signedSertifikat);
        $lihatKompetensiResmi = ! $untukDs && ($tampilanAdmin || $signedKompetensi);
        $tampilFooterSertifikat = $untukDs || $lihatSertifikatResmi;
        $tampilFooterKompetensi = $untukDs || $lihatKompetensiResmi;
        $tampilWatermarkSertifikat = ! $untukDs && ! $lihatSertifikatResmi;
        $tampilWatermarkKompetensi = ! $untukDs && ! $lihatKompetensiResmi;
        $tampilSertifikat = $halaman === 'sertifikat';
        $tampilKompetensi = $halaman === 'kompetensi';
    @endphp
    @unless ($modeUnduh)
    <div class="toolbar">
        <div>
            @if ($halaman === 'kompetensi')
                @if ($signedKompetensi)
                    Daftar Unit Kompetensi
                @elseif ($tampilanAdmin)
                    Diajukan, menunggu TTE
                @else
                    Draf daftar unit kompetensi — belum TTE
                @endif
            @elseif ($halaman === 'sertifikat')
                @if ($signedSertifikat)
                    Sertifikat Kelulusan
                @elseif ($tampilanAdmin)
                    Diajukan, menunggu TTE
                @else
                    Draf sertifikat — belum TTE
                @endif
            @else
                {{ $sertifikat['is_draft'] ? 'Draf sertifikat — belum TTE' : 'Sertifikat' }}
            @endif
        </div>
        <div>
            @unless ($modeUnduh)
                @if ($peserta->bisaUnduhBerkasTte())
                    {!! $peserta->tombolUnduhBerkas('blk.pelatihan.peserta.unduh', [
                        'id' => $pelatihan->id,
                        'pesertaId' => $peserta->id,
                    ], 'btn') !!}
                @endif
                @if ($halaman === 'sertifikat')
                    <a href="{{ route('blk.pelatihan.peserta.sertifikat', [$pelatihan->id, $peserta->id, 'kompetensi']) }}">Unit Kompetensi</a>
                @else
                    <a href="{{ route('blk.pelatihan.peserta.sertifikat', [$pelatihan->id, $peserta->id, 'sertifikat']) }}">Sertifikat</a>
                @endif
            @endunless
            <button type="button" onclick="window.print()">Cetak / PDF</button>
            @unless ($modeUnduh)
                <a href="{{ route('blk.pelatihan.peserta.show', [$pelatihan->id, $peserta->id]) }}">Kembali</a>
            @endunless
        </div>
    </div>
    @endunless

    @if ($tampilSertifikat)
    <div class="page">
        <div class="sheet">
        <div class="sheet-main">
        @if ($tampilWatermarkSertifikat)
            <div class="watermark">DRAFT</div>
        @endif
        <div class="content">
            <div class="header">
                <div class="logo-wrap">
                    <img src="{{ $sertifikat['logo_kaltim'] }}" alt="Lambang Kalimantan Timur">
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
                    @if ($lihatSertifikatResmi)
                        <span class="tte-ok">Telah ditandatangani secara elektronik</span>
                    @endif
                    @if ($untukDs && ! empty($sertifikat['logo_bsre']))
                        <table class="ttd-bsre-wrap">
                            <tr>
                                <td><img src="{{ $sertifikat['logo_bsre'] }}" alt="Logo BSrE" class="ttd-bsre"></td>
                            </tr>
                        </table>
                    @endif
                </div>
                <div class="nama">{{ $sertifikat['penandatangan']['dinas']['nama'] ?: '................................' }}</div>
            </div>
        </div>
        </div>
        @if ($tampilFooterSertifikat)
        <div class="sheet-foot">
            @include('backend.blk.pelatihan._bsre-footer')
        </div>
        @endif
        </div>
    </div>
    @endif

    @if ($tampilKompetensi)
    <div class="page">
        <div class="sheet">
        <div class="sheet-main">
        @if ($tampilWatermarkKompetensi)
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
                    @if ($lihatKompetensiResmi)
                        <span class="tte-ok">Telah ditandatangani secara elektronik</span>
                    @endif
                    @if ($untukDs && ! empty($sertifikat['logo_bsre']))
                        <table class="ttd-bsre-wrap">
                            <tr>
                                <td><img src="{{ $sertifikat['logo_bsre'] }}" alt="Logo BSrE" class="ttd-bsre"></td>
                            </tr>
                        </table>
                    @endif
                </div>
                <div class="nama">{{ $sertifikat['penandatangan']['balai']['nama'] ?: '................................' }}</div>
            </div>
        </div>
        </div>
        @if ($tampilFooterKompetensi)
        <div class="sheet-foot">
            @include('backend.blk.pelatihan._bsre-footer')
        </div>
        @endif
        </div>
    </div>
    @endif
    @include('backend.blk.pelatihan._unduh-tte-js')
</body>
</html>
