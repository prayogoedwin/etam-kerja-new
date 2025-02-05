<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kartu Tanda Bukti Pendaftaran Pencari Kerja</title>
    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/style.css" />
    <style>
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
        }



        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        hr {
            box-sizing: content-box;
            height: 0;
            overflow: visible;
        }

        a {
            background-color: transparent;
        }

        b,
        strong {
            font-weight: bolder;
        }

        small {
            font-size: 80%;
        }

        img {
            border-style: none;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin-top: 0px;
            margin-bottom: 15px;
            font-weight: normal;
        }

        h6 {
            font-size: 12px;
            text-decoration: underline;
        }

        p {
            font-size: 12px;
            margin-top: 0px;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        ul {
            padding-left: 22px;
            margin: 0px;
        }

        ul li {
            font-size: 14px;
        }

        .text-center {
            text-align: center !important;
        }

        .text-underline {
            text-decoration: underline !important;
        }

        .title-border {
            padding: 5px;
            border: 2px solid #000;
            text-align: center;
            font-weight: bold;
        }

        .line-dotted {
            position: relative;
            height: 2px;
            display: block;
            border: none;
            border-top: 2px dotted #000;
            border-bottom: 1px solid #000;
            max-width: 80%;
            margin-left: 0;
        }

        .fill-square {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .fill-square b {
            min-width: 180px;
            font-weight: normal;
        }

        .fill-square span {
            display: flex;
        }

        .fill-square span em {
            font-style: normal;
            width: 16px;
            height: 16px;
            text-align: center;
            line-height: 16px;
            border: 1px solid #000;
        }

        .fill-square span em.empty {
            border: 1px solid transparent;
        }

        .main-sign-card {
            display: block;
            overflow: auto;
        }

        .content-sign-card {
            /* min-width: 1200px; */
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            width: auto;
            margin: 5px;
            padding: 5px 5px 5px 5px;
            border: 1px solid #000;
        }

        .content-sign-card>.__left {
            flex: 0 0 40%;
            max-width: 40%;
        }

        .content-sign-card>.__right {
            flex: 0 0 58%;
            max-width: 58%;
        }

        .card-name {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: right;
            margin-bottom: 10px;
        }

        .other-content {
            /* min-width: 1200px; */
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            width: auto;
            margin: 5px;
            padding: 0px;
            border: none;
        }

        .other-content>.__left {
            flex: 0 0 calc(50% - 2.5px);
            max-width: calc(50% - 2.5px);
        }

        .other-content>.__right {
            flex: 0 0 calc(50% - 2.5px);
            max-width: calc(50% - 2.5px);
        }

        .inner-content {
            display: flex;
            flex-wrap: wrap;
            width: auto;
            margin-bottom: 15px;
        }

        .inner-content>.__left {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .inner-content>.__right {
            flex: 0 0 50%;
            max-width: 50%;
        }

        header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        header img {
            min-width: 60px;
        }

        header p {
            width: 100%;
            text-align: center;
            margin-bottom: 0;
            line-height: 1.4;
            text-transform: uppercase;
        }

        header p small {
            display: block;
            margin-top: 0px;
            text-transform: none;
            font-size: 11px;
        }

        .table-sign-card {
            position: relative;
            width: 100%;
            border-collapse: collapse;
        }

        .table-sign-card th,
        .table-sign-card td {
            font-size: 12px;
            text-align: left;
            padding: 10px;
            border: 1px solid #000;
        }

        .signing {
            display: block;
        }

        .signing p {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 0px;
        }

        .info-detail {
            position: relative;
            margin-left: 15px;
        }

        .info-detail p {
            display: flex;
            line-height: 1.8;
        }

        .info-detail p span {
            min-width: 120px;
            position: relative;
            margin-right: 5px;
        }

        .info-detail p span::after {
            content: ':';
            position: absolute;
            right: 0px;
        }

        .page-divided {
            break-after: page !important;
        }

        /* style sheet for "A4" printing */
        @media print and (width: 21cm) and (height: 29.7cm) {
            @page {
                margin: 3cm;
            }
        }

        /* style sheet for "letter" printing */
        @media print and (width: 8.5in) and (height: 11in) {
            @page {
                margin: 1in;
            }
        }

        /* A4 Landscape*/
        @page {
            size: 330mm 120mm;
            margin: 1%;
        }
    </style>
</head>

<body onload="window.print()">
    <!-- <body  > -->
    <main class="main-sign-card">
        <div class="content-sign-card">
            <p class="card-name">Kartu AK I</p>
            <div class="__left">


                <h6>PENDIDIKAN FORMAL</h6>
                <ul>

                    <ul>
                        @forelse ($pendidikans as $edu)
                            <li>{{ $edu->pendidikanteks ?? '-' }} - {{ $edu->jurusanteks ?? '-' }}</li>
                        @empty
                            <li>-</li>
                        @endforelse
                    </ul>
                </ul>

                <hr class="line-dotted" />


                <div class="inner-content" style="margin-top:50px">
                    <div class="__left">
                        <h6>KETERAMPILAN</h6>
                        <ul>
                            @forelse ($keterampilans as $skill)
                                <li>
                                    {{ $skill->keahlian ?? '-' }}<br>
                                </li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="__right">
                        <h6 class="text-center">PETUGAS ANTAR KERJA</h6>
                        <p><br /><br /><br /></p>
                        <p class="text-center" style="margin-bottom: 0px;"><span class="text-underline">
                                {{ $admins->user->name }}</span>
                            {{-- <br />NIP. ___________________</p> --}}
                    </div>
                </div>
            </div>
            <div class="__right">
                <header>
                    {{-- <img src="https://bursakerja.jatengprov.go.id/assets/images/kontingen/Kabupaten_Semarang.png"
                        width="60" height="60" alt="KABUPATEN SEMARANG" /> --}}
                    <img src="{{ url($admins->kabkota->icon) }}" width="60" height="60"
                        alt="{{ $admins->kabkota->kantor }}" />
                    {{-- <img src="{{ url($admins->kabkota->icon) }}" width="60" height="60" alt="{{ $admins->kabkota->kantor }}" /> --}}

                    <p>{{ $admins->kabkota->kantor }}
                        <small>{{ $admins->kabkota->alamat }}<br />
                            Telp:{{ $admins->kabkota->telp }}| Email:{{ $admins->kabkota->email }}<br />
                            {{ $admins->kabkota->web }} </small>
                    </p>
                </header>
                <div class="job-seeker">
                    <h5 class="title-border">Kartu Tanda Bukti Pendaftaran Pencari Kerja</h5>


                    <!-- <span><em>8</em><em>4</em><em>5</em><em>0</em><em class="empty"></em><em>0</em><em>6</em><em>2</em><em>6</em><em>6</em><em>7</em><em>8</em><em class="empty"></em><em>0</em><em>0</em><em>1</em><em>7</em><em>2</em><em>5</em><em>6</em></span></p> -->
                    <p class="fill-square"><b>No. Pendaftaran Pencari Kerja</b> <span>
                            <em class="empty"></em>


                            <em class="empty"></em>

                            @if (!empty($nakerAk1->id))
                                @foreach (str_split($nakerAk1->id) as $digit_id)
                                    <em>{{ $digit_id }}</em>
                                @endforeach
                            @else
                                <em>-</em>
                            @endif

                            {{-- <em>1</em><em>9</em><em>7</em><em>0</em><em>0</em><em>1</em><em>0</em><em>1</em> --}}
                        </span></p>


                    <!-- <p class="fill-square"><b>No. Induk Kependudukan</b> <span><em>2</em><em>6</em><em>7</em><em>8</em><em>9</em><em>0</em><em>0</em><em>0</em><em>2</em><em>1</em><em>2</em><em>4</em><em>5</em><em>8</em><em>2</em><em>1</em></span></p> -->
                    <p class="fill-square"><b>No. Induk Kependudukan</b>
                        <span>
                            <em class="empty"></em>


                            <em class="empty"></em>
                            @if (!empty($pencari->ktp))
                                @foreach (str_split($pencari->ktp) as $digit)
                                    <em>{{ $digit }}</em>
                                @endforeach
                            @else
                                <em>-</em>
                            @endif
                        </span>
                    </p>




                    <div style="display: flex; align-items: flex-end; margin-top: 15px;">
                        <div style="width: 100%;">
                            <div style="display: flex;">
                                <div class="signing">
                                    <img src="{{ asset('storage/' . $pencari->foto) }}" width="118" height="118"
                                        alt="Foto Profil" />
                                    <p>TTD Pencari Kerja</p>
                                </div>
                                <div class="info-detail">
                                    <p style="margin-bottom: 0px;"><span>Nama Lengkap</span>
                                        {{ $pencari->name ?? '-' }}
                                    </p>
                                    <p style="margin-bottom: 0px;"><span>Tempat / Tgl Lahir</span>
                                        {{ $pencari->tempat_lahir ?? '-' }} ,
                                        {{ \Carbon\Carbon::parse($pencari->tanggal_lahir)->format('d-m-Y') }}
                                    </p>
                                    <p style="margin-bottom: 0px;"><span>Jenis Kelamin</span>
                                        {{ $pencari->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    <p style="margin-bottom: 0px;"><span>Alamat</span>{{ $pencari->alamat }},
                                        {{ $pencari->kodepos }}</p>
                                    <p style="margin-bottom: 0px;"><span>No.
                                            Telp</span>{{ $pencari->user->whatsapp ?? 'Tidak tersedia' }}</p>
                                    <p style="margin-bottom: 0px;"><span>Berlaku s.d.</span>
                                        {{ \Carbon\Carbon::parse($nakerAk1->tanggal_cetak)->locale('id')->translatedFormat('d F Y') }}
                                        s.d
                                        {{ \Carbon\Carbon::parse($nakerAk1->berlaku_hingga)->locale('id')->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div style="min-width: 48px;">
                            <img src="{{ asset('storage/' . $nakerAk1->qr) }}" width="100px" height="100px"
                                alt="QR Code" style="margin-right:15px" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-divided"></div>
        <div class="other-content">
            <div class="__left">
                <table class="table-sign-card" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th colspan="2">KETENTUAN:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>BERLAKU NASIONAL</td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td>BILA ADA PERUBAHAN DATA / KETERANGAN LAINNYA ATAU TELAH MENDAPAT PEKERJAAN HARAP SEGERA
                                MELAPOR</td>
                        </tr>
                        <tr>
                            <td>3.</td>
                            <td>APABILA PENCARI KERJA YANG BERSANGKUTAN TELAH DITERIMA BEKERJA MAKA INSTANSI /
                                PERUSAHAAN YANG MENERIMA AGAR MENGEMBALIKAN KARTU AK I INI</td>
                        </tr>
                        <tr>
                            <td>4.</td>
                            <td>KARTU INI BERLAKU SELAMA 2 (DUA) TAHUN DENGAN KEHARUSAN KETENTUAN MELAPOR SETIAP 6
                                (ENAM) BULAN SEKALI BAGI PENCARI KERJA YANG BELUM MENDAPATKAN PEKERJAAN</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="__right">
                <table class="table-sign-card" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">LAPORAN</th>
                            <th class="text-center" style="width: 160px;">TANGGAL-BULAN-TAHUN</th>
                            <th class="text-center">Tanda Tangan Pengantar Kerja / Petugas Pendaftar<br />(Cantumkan
                                NIP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">PERTAMA</td>
                            <td>28-05-2025</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-center">KEDUA</td>
                            <td>28-11-2025</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-center">KETIGA</td>
                            <td>28-05-2026</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2">DITERIMA DI</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2">TERHITUNG MULAI TANGGAL</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>
