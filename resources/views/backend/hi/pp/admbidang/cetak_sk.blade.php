<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Keputusan Kepala Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur</title>
  <style>
    @page {
      size: A4;
      margin: 12mm 15mm 12mm 15mm;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: "Times New Roman", Times, serif;
      font-size: 11pt;
      line-height: 1.28;
      color: #000;
      background: #e5e5e5;
    }

    .page {
      width: 210mm;
      min-height: 297mm;
      margin: 8mm auto;
      background: #fff;
      padding: 10mm 15mm 12mm 15mm;
      box-shadow: 0 2px 12px rgba(0,0,0,0.15);
      position: relative;
      page-break-after: always;
      overflow: hidden;
    }

    .page:last-child {
      page-break-after: auto;
    }

    /* ===== HEADER ===== */
    .header {
      text-align: center;
      border-bottom: 3px double #000;
      padding-bottom: 4px;
      margin-bottom: 8px;
      position: relative;
    }

    .header .logo-space {
      position: absolute;
      left: 0;
      top: 0;
      width: 16mm;
      height: 16mm;
      border: 1px dashed #aaa;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 6.5pt;
      color: #aaa;
      text-align: center;
      line-height: 1.1;
    }

    .header .gov {
      font-size: 10pt;
      font-weight: bold;
      letter-spacing: 0.4px;
      text-transform: uppercase;
    }

    .header .dinas {
      font-size: 13pt;
      font-weight: bold;
      text-transform: uppercase;
      margin: 1px 0 1px;
    }

    .header .alamat {
      font-size: 8pt;
      line-height: 1.2;
    }

    /* ===== JUDUL ===== */
    .judul {
      text-align: center;
      margin: 8px 0 6px;
    }

    .judul h1 {
      font-size: 11pt;
      font-weight: bold;
      text-transform: uppercase;
      line-height: 1.25;
    }

    .judul .nomor {
      font-size: 10.5pt;
      margin: 3px 0 4px;
    }

    .judul .tentang {
      font-size: 10.5pt;
      font-weight: bold;
      text-transform: uppercase;
      margin-top: 2px;
    }

    .judul .perihal {
      font-size: 10.5pt;
      font-weight: bold;
      text-transform: uppercase;
      margin-top: 1px;
    }

    .kepala {
      text-align: center;
      font-weight: bold;
      font-size: 10.5pt;
      text-transform: uppercase;
      margin: 6px 0 8px;
      line-height: 1.25;
    }

    /* ===== BAGIAN KONSIDERAN ===== */
    .bagian {
      margin-bottom: 5px;
      display: table;
      width: 100%;
    }

    .bagian .label {
      display: table-cell;
      width: 24mm;
      font-weight: bold;
      vertical-align: top;
      padding-right: 2px;
      font-size: 10.5pt;
    }

    .bagian .titik {
      display: table-cell;
      width: 5mm;
      vertical-align: top;
      font-size: 10.5pt;
    }

    .bagian .isi {
      display: table-cell;
      vertical-align: top;
      text-align: justify;
      font-size: 10.5pt;
    }

    .bagian .isi ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .bagian .isi li {
      margin-bottom: 3px;
      text-align: justify;
      position: relative;
      padding-left: 14px;
      line-height: 1.28;
    }

    .bagian .isi li .huruf {
      position: absolute;
      left: 0;
      font-weight: bold;
    }

    .bagian .isi li.angka {
      padding-left: 16px;
    }

    .bagian .isi li.angka .nomor {
      position: absolute;
      left: 0;
    }

    /* ===== MEMUTUSKAN ===== */
    .memutuskan {
      text-align: center;
      font-weight: bold;
      font-size: 11pt;
      letter-spacing: 2.5px;
      margin: 8px 0 6px;
      text-transform: uppercase;
    }

    .diktum {
      margin-bottom: 4px;
      display: table;
      width: 100%;
    }

    .diktum .nomor-diktum {
      display: table-cell;
      width: 24mm;
      font-weight: bold;
      vertical-align: top;
      text-transform: uppercase;
      font-size: 10.5pt;
    }

    .diktum .titik {
      display: table-cell;
      width: 5mm;
      vertical-align: top;
      font-size: 10.5pt;
    }

    .diktum .isi-diktum {
      display: table-cell;
      vertical-align: top;
      text-align: justify;
      font-size: 10.5pt;
    }

    .diktum table {
      margin-top: 2px;
      border-collapse: collapse;
      width: 100%;
    }

    .diktum td {
      padding: 1px 0;
      vertical-align: top;
      font-size: 10.5pt;
    }

    .diktum td.label-perusahaan {
      width: 36mm;
      white-space: nowrap;
    }

    .diktum td.titik-perusahaan {
      width: 5mm;
    }

    /* ===== TTD ===== */
    .ttd-area {
      margin-top: 18px;
      width: 100%;
      display: table;
    }

    .ttd-kiri {
      display: table-cell;
      width: 42%;
      vertical-align: top;
    }

    .ttd-kanan {
      display: table-cell;
      width: 58%;
      vertical-align: top;
      text-align: center;
      padding-left: 8mm;
    }

    .ttd-kanan .tempat {
      margin-bottom: 1px;
      font-size: 10.5pt;
    }

    .ttd-kanan .jabatan {
      font-weight: bold;
      text-transform: uppercase;
      margin-top: 3px;
      margin-bottom: 0;
      font-size: 10.5pt;
    }

    .ttd-kanan .space-ttd {
      height: 18mm;
    }

    .ttd-kanan .nama {
      font-weight: bold;
      text-decoration: underline;
      margin-top: 1px;
      font-size: 10.5pt;
    }

    .ttd-kanan .pangkat {
      font-size: 10pt;
    }

    .ttd-kanan .nip {
      font-size: 10pt;
    }

    /* ===== TEMBUSAN ===== */
    .tembusan {
      margin-top: 14px;
      font-size: 10pt;
    }

    .tembusan .judul-tembusan {
      font-weight: bold;
      text-decoration: underline;
      margin-bottom: 2px;
    }

    .tembusan ol {
      padding-left: 15px;
      margin: 0;
    }

    .tembusan li {
      margin-bottom: 1px;
    }

    /* ===== NOMOR HALAMAN ===== */
    .halaman {
      position: absolute;
      bottom: 7mm;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 10pt;
    }

    /* ===== PRINT ===== */
    @media print {
      body {
        background: none;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .page {
        width: 100%;
        min-height: auto;
        height: auto;
        margin: 0;
        padding: 0;
        box-shadow: none;
        page-break-after: always;
        overflow: visible;
      }
      .page:last-child {
        page-break-after: auto;
      }
      .header .logo-space {
        border: none;
        color: transparent;
      }
    }
  </style>
</head>
<body>

  <!-- ==================== HALAMAN 1 ==================== -->
  <div class="page">
    <div class="header">
      {{-- <div class="logo-space">Logo<br>Dinas</div> --}}
      <div class="logo-space" style="margin-left: 50px;">
        <img src="{{asset('assets/etam_fe/images/logo/logo_kaltim.png')}}" width="50" alt="">
      </div>
      <div class="gov">Pemerintah Provinsi Kalimantan Timur</div>
      <div class="dinas">Dinas Tenaga Kerja dan Transmigrasi</div>
      <div class="alamat">
        Jln. Kemakmuran No. 2 Telp. 0541-767242, 767241 Fax. 0541-735973<br>
        Website : www.disnakertrans.kaltimprov.go.id<br>
        SAMARINDA 75117
      </div>
    </div>

    <div class="judul">
        <h1>Keputusan Kepala Dinas Tenaga Kerja dan Transmigrasi<br>Provinsi Kalimantan Timur</h1>
        <div class="nomor">Nomor : {{ $ajuan->nomor_sk ?? '..........................' }}</div>
        <div class="tentang">Tentang</div>
        <div class="perihal">
            @switch((int) $ajuan->jenis_ajuan)
                @case(1)
                    Pengesahan Pembaharuan Peraturan Perusahaan Ke 2 (Dua)
                    @break
                @case(2)
                    Pengesahan Peraturan Perusahaan
                    @break
                @case(3)
                    Perpanjangan Pengesahan Peraturan Perusahaan
                    @break
                @case(4)
                    Perubahan Pengesahan Peraturan Perusahaan
                    @break
                @default
                    Pengesahan Peraturan Perusahaan
            @endswitch
            <br>
            {{ $ajuan->perusahaan->penyedia->name ?? '..........................' }} Tahun {{ $rangeTahun }}
        </div>
    </div>

    <div class="kepala">
      Kepala Dinas Tenaga Kerja dan Transmigrasi<br>
      Provinsi Kalimantan Timur
    </div>

    <div class="bagian">
        <div class="label">Membaca</div>
        <div class="titik">:</div>
        <div class="isi">
            Surat Permohonan Pengesahan Peraturan Perusahaan
            {{ $ajuan->perusahaan->penyedia->name ?? '..........................' }}
            Nomor : {{ $ajuan->nomor ?? '..........................' }}
            Tanggal {{ $ajuan->tanggal ? tgl_indo($ajuan->tanggal) : '..........................' }}
        </div>
    </div>

    <div class="bagian">
        <div class="label">Menimbang</div>
        <div class="titik">:</div>
        <div class="isi">
            <ul>
                <li><span class="huruf">a.</span> bahwa pembuatan Peraturan Perusahaan dimaksudkan sebagai upaya mewujudkan adanya kepastian hukum bagi Pekerja/Buruh dan pengusaha dalam pelaksanaan hubungan kerja di perusahaan;</li>
                <li><span class="huruf">b.</span> bahwa pengaturan syarat-syarat kerja dimaksudkan untuk memperjelas hak dan kewajiban Pekerja/Buruh dan Pengusaha dengan tujuan untuk meningkatkan kegairahan dan ketenangan bekerja, meningkatkan kesejahteraan Pekerja/Buruh atau Serikat Pekerja/Serikat Buruh di perusahaan;</li>
                <li><span class="huruf">c.</span> bahwa sehubungan dengan pertimbangan huruf a dan b, maka dipandang perlu mengesahkan Peraturan Perusahaan
                    {{ $ajuan->perusahaan->penyedia->name ?? '..........................' }}
                    dengan keputusan Kepala Dinas Tenaga Kerja Dan Transmigrasi Provinsi Kalimantan Timur.</li>
            </ul>
        </div>
    </div>

    <div class="bagian">
      <div class="label">Mengingat</div>
      <div class="titik">:</div>
      <div class="isi">
        <ul>
          <li class="angka"><span class="nomor">1.</span> Undang-Undang Nomor 13 Tahun 2003 tentang Ketenagakerjaan;</li>
          <li class="angka"><span class="nomor">2.</span> Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintahan Daerah jo Undang-Undang Nomor 9 Tahun 2015 tentang Perubahan Kedua Undang-Undang 23 Tahun 2014 tentang Pemerintahan Daerah;</li>
          <li class="angka"><span class="nomor">3.</span> Peraturan Pemerintah Pengganti Undang-Undang Republik Indonesia Nomor 2 Tahun 2022 tentang Cipta Kerja;</li>
          <li class="angka"><span class="nomor">4.</span> Peraturan Pemerintah Nomor 35 Tahun 2021 Tentang Perjanjian Kerja Waktu Tertentu, Alih Daya, Waktu Kerja, Waktu Istirahat, dan Pemutusan Hubungan Kerja;</li>
          <li class="angka"><span class="nomor">5.</span> Peraturan Pemerintah Nomor 36 Tahun 2021 Tentang Pengupahan;</li>
          <li class="angka"><span class="nomor">6.</span> Peraturan Menteri Ketenagakerjaan RI Nomor : 28 Tahun 2014 tentang Tata Cara Pembuatan dan Pengesahan Peraturan Perusahaan serta Pembuatan dan Pendaftaran Perjanjian Kerja Bersama;</li>
          <li class="angka"><span class="nomor">7.</span> Peraturan Daerah Provinsi Kalimantan Timur Nomor 1 Tahun 2021 Tentang Perubahan Atas Peraturan Daerah Nomor 9 Tahun 2016 Tentang Pembentukan dan Susunan Perangkat Daerah Provinsi Kalimantan Timur;</li>
          <li class="angka"><span class="nomor">8.</span> Peraturan Gubernur Kalimantan Timur Nomor 58 Tahun 2016 Tentang Susunan Organisasi, Tugas, Fungsi dan Tata Kerja Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur.</li>
        </ul>
      </div>
    </div>

    <div class="memutuskan">Memutuskan</div>

    <div class="diktum">
      <div class="nomor-diktum">Menetapkan</div>
      <div class="titik">:</div>
      <div class="isi-diktum"></div>
    </div>

    <div class="diktum">
        <div class="nomor-diktum">Kesatu</div>
        <div class="titik">:</div>
        <div class="isi-diktum">
            Mengesahkan Peraturan Perusahaan :
            <table>
                <tr>
                    <td class="label-perusahaan">Nama Perusahaan</td>
                    <td class="titik-perusahaan">:</td>
                    <td>{{ $ajuan->perusahaan->penyedia->name ?? '..........................' }}</td>
                </tr>
                <tr>
                    <td class="label-perusahaan">Alamat Perusahaan</td>
                    <td class="titik-perusahaan">:</td>
                    <td>{{ $ajuan->perusahaan->penyedia->alamat ?? '..........................' }}</td>
                </tr>
                <tr>
                    <td class="label-perusahaan">Jenis Usaha</td>
                    <td class="titik-perusahaan">:</td>
                    <td>{{ getSektorById($ajuan->perusahaan->penyedia->id_sektor)->name ?? '..........................' }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- <div class="halaman">1</div> --}}
  </div>

  <!-- ==================== HALAMAN 2 ==================== -->
  <div class="page">
    <div class="diktum" style="margin-top: 4px;">
        <div class="nomor-diktum">Kedua</div>
        <div class="titik">:</div>
        <div class="isi-diktum">
            Peraturan Perusahaan {{ $ajuan->perusahaan->penyedia->name ?? '..........................' }}
            yang disahkan sebagaimana dimaksud diktum KESATU mulai berlaku terhitung tanggal
            <strong>
                {{ $tanggalMulai ? tgl_indo($tanggalMulai) : '..........................' }}
                s/d
                {{ $tanggalBerakhir ? tgl_indo($tanggalBerakhir) : '..........................' }}
            </strong>
            dan telah dimuat dalam Buku Registrasi Pengesahan Peraturan Perusahaan pada
            Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur
            Nomor: {{ $ajuan->nomor ?? '..........................' }};
        </div>
    </div>

    <div class="diktum">
      <div class="nomor-diktum">Ketiga</div>
      <div class="titik">:</div>
      <div class="isi-diktum">Pengusaha wajib memberitahukan dan menjelaskan isi serta memberikan naskah Peraturan Perusahaan kepada Pekerja/Buruh;</div>
    </div>

    <div class="diktum">
      <div class="nomor-diktum">Keempat</div>
      <div class="titik">:</div>
      <div class="isi-diktum">Dalam hal terdapat ketentuan yang diatur dalam Peraturan Perusahaan sebagaimana dimaksud diktum KESATU bertentangan dengan peraturan perundang-undangan yang berlaku, maka ketentuan yang bertentangan tersebut batal demi hukum;</div>
    </div>

    <div class="diktum">
      <div class="nomor-diktum">Kelima</div>
      <div class="titik">:</div>
      <div class="isi-diktum">Keputusan ini mulai berlaku sejak tanggal ditetapkan dan apabila kemudian ternyata terdapat kekeliruan dalam penetapannya, akan diperbaiki sebagaimana mestinya.</div>
    </div>

    <div class="ttd-area">
      <div class="ttd-kiri"></div>
      <div class="ttd-kanan">
        <div class="tempat">Ditetapkan di Samarinda</div>
        <div class="tempat">Pada tanggal {{tgl_indo($tanggalMulai)}}</div>
        <div class="jabatan">Kepala Dinas</div>
        <div class="space-ttd"></div>
        <div class="nama">H. Rozani Erawadi, S.H, M.Si</div>
        <div class="pangkat">Pembina Utama Madya</div>
        <div class="nip">NIP. 19710124 199703 1 007</div>
      </div>
    </div>

    <div class="tembusan">
      <div class="judul-tembusan">Salinan Keputusan ini disampaikan kepada Yth :</div>
      <ol>
        <li>Gubernur Kalimantan Timur di Samarinda (sebagai laporan);</li>
        <li>Dirjen PHI &amp; Jamsos Kementerian Ketenagakerjaan RI di Jakarta;</li>
        <li>Kepala Dinas Tenaga Kerja Kota Samarinda;</li>
        <li>Arsip.</li>
      </ol>
    </div>

    {{-- <div class="halaman">2</div> --}}
  </div>

</body>
</html>
