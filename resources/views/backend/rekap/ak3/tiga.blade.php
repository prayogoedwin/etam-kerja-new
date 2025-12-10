@extends('backend.template.backend')

@section('content')

    <body class="box-layout container background-green">
        <!-- [ Main Content ] start -->
        <div class="pcoded-main-container">
            <div class="pcoded-content">


                <!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Rekap</h5>
                                </div>
                                {{-- <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="#!">Hospital</a></li>
                                    <li class="breadcrumb-item"><a href="#!">Department</a></li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->


                <!-- [ Main Content ] start -->
                <div class="row">


                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-12">
                                        <h5>IPK-lll/3: PENCARI KEERJA TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN MENURUT GOLONG JABATAN (DESEMBER 2024)</h5>
                                    </div>
                                </div>
                                <div class="table-responsive">

                                    <table class="table table-bordered">
                                        
                                            <thead>
                                                <tr class="header-title">
                                                    <th rowspan="3" class="col-kode">Kode</th>
                                                    <th rowspan="3" class="col-uraian">Uraian Jenis Golongan Pokok Jabatan</th>
                                                    <th colspan="2">Sisa akhir Triwulan Lalu</th>
                                                    <th colspan="2">Pendaftaran pada Triwulan ini</th>
                                                    <th colspan="2">Penempatan pada Triwulan ini</th>
                                                    <th colspan="2">Penghapusan pada Triwulan ini</th>
                                                    <th colspan="2">Sisa Akhir Triwulan Ini</th>
                                                </tr>
                                                <tr class="header-title">
                                                    <th>L</th>
                                                    <th>W</th>
                                                    <th>L</th>
                                                    <th>W</th>
                                                    <th>L</th>
                                                    <th>W</th>
                                                    <th>L</th>
                                                    <th>W</th>
                                                    <th>L</th>
                                                    <th>W</th>
                                                </tr>
                                                {{-- <tr class="header-sub">
                                                    <th>(1)</th>
                                                    <th>(2)</th>
                                                    <th>(3)</th>
                                                    <th>(4)</th>
                                                    <th>(5)</th>
                                                    <th>(6)</th>
                                                    <th>(7)</th>
                                                    <th>(8)</th>
                                                    <th>(9)</th>
                                                    <th>(10)</th>
                                                    <th>(11)</th>
                                                </tr> --}}
                                            </thead>
                                            <tbody>
                                                <tr class="row-group">
                                                    <td>0</td>
                                                    <td class="col-uraian">ANGGOTA ANGKATAN BERSENJATA (KECUALI KEPOLISIAN)</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                </tr>
                                                <tr>
                                                    <td>0110</td>
                                                    <td class="col-uraian">ANGGOTA ANGKATAN BERSENJATA</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr class="row-group">
                                                    <td>1</td>
                                                    <td class="col-uraian">ANGGOTA BADAN LEGISLATIF, PEJABAT TINGGI PEMERINTAH</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                </tr>
                                                <tr>
                                                    <td>1110</td>
                                                    <td class="col-uraian">ANGGOTA BADAN LEGISLATIF</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1120</td>
                                                    <td class="col-uraian">PEJABAT TINGGI PEMERINTAH</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1130</td>
                                                    <td class="col-uraian">KEPALA DESA DAN LURAH</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1141</td>
                                                    <td class="col-uraian">PEMIMPIN ORGANISASI PARTAI POLITIK</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1142</td>
                                                    <td class="col-uraian">PIMPINAN ORGANISASI PENGUSAHA, PEKERJA DAN ORGANISASI YANG BERKAITAN DENGAN KEPENTINGAN DARI ORGANISASI EKONOMI</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1143</td>
                                                    <td class="col-uraian">PIMPINAN ORGANISASI KEMANUSIAAN DAN ORGANISASI KHUSUS</td>
                                                 <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1210</td>
                                                    <td class="col-uraian">DIREKTUR DAN KEPALA EKSEKUTIF</td>
                                               <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1221</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI DALAM BIDANG PERTANIAN PERBURUAN DAN PERIKANAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1223</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI DALAM BIDANG KONSTRUKSI</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1224</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI DOKUMEN DALAM BIDANG PERDAGANGA BESAR DAN ECERAN</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1225</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI DALAM BIDANG HOTEL DAN RESTORAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1226</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI DALAM BIDANG ANGKUTAN (LAYANAN TELEKOMUNIKASI) PERGUDANGAN & KOMUNIKASI</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1227</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI DALAM BIDANG PERUSAHAAN JASA</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1228</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI DALAM BIDANG KESEJAHTERAAN DAN KEBERSIHAN</td>
                                                     <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1229</td>
                                                    <td class="col-uraian">MANAJER PRODUKSI DAN OPERASI TIDAK DAPAT DIKLASIFIKASIKAN PADA 1221-1228</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1231</td>
                                                    <td class="col-uraian">MANAJER KEUANGAN DAN ADMINISTRASI</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1232</td>
                                                    <td class="col-uraian">MANAJER PEMASARAN DAN HUBUNGAN INDUSTRI</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>1233</td>
                                                    <td class="col-uraian">MANAJER PENJUALAN DAN PEMASARAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                            </tbody>

                                    </table>





                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- customar project  end -->


                </div>
                <!-- [ Main Content ] end -->


            </div>
        </div>

    </body>
@endsection


@push('js')
@endpush
