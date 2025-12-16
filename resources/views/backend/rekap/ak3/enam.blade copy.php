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
                                        <h5>IPK-lll/6: LOWONGAN KERJA TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN MENURUT SEKTOR LAPANGAN USAHA (DESEMBER 2024)</h5>
                                    </div>
                                </div>
                                <div class="table-responsive">

                                    <table class="table table-bordered">
                                        
                                            <thead>
                                                <tr class="header-title">
                                                    <th rowspan="3" class="col-kode">Kode</th>
                                                    <th rowspan="3" class="col-uraian">Uraian Jenis Sektor Lapangan Usaha</th>
                                                    <th colspan="2">Sisa akhir Triwulan Lalu</th>
                                                    <th colspan="2">Lowongan Terdaftar Triwulan ini</th>
                                                    <th colspan="2">Lowongan Dipenuhi Triwulan ini</th>
                                                    <th colspan="2">Dihapuskan pada Triwulan ini</th>
                                                    <th colspan="2">Sisa Lowongan akhir Triwulan ini</th>
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
                                                <tr class="header-sub">
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Sektor A: Pertanian, Kehutanan dan Perikanan -->
                                                <tr class="row-group">
                                                    <td>A</td>
                                                    <td class="col-uraian">PERTANIAN, KEHUTANAN DAN PERIKANAN</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                </tr>
                                                <tr>
                                                    <td>011</td>
                                                    <td class="col-uraian">PERTANIAN TANAMAN SEMUSIM</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>012</td>
                                                    <td class="col-uraian">PERTANIAN TANAMAN TAHUNAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>013</td>
                                                    <td class="col-uraian">PERTANIAN TANAMAN HIAS DAN PENGEMBANGBIAKAN TANAMAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>014</td>
                                                    <td class="col-uraian">PETERNAKAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>016</td>
                                                    <td class="col-uraian">JASA PENUNJANG PERTANIAN DAN PASCA PANEN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>017</td>
                                                    <td class="col-uraian">PERBURUAN, PENANGKAPAN DAN PENANGKARAN SATWA LIAR</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>021</td>
                                                    <td class="col-uraian">PENGUSAHAAN HUTAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>022</td>
                                                    <td class="col-uraian">PENEBANGAN DAN PEMUNGUTAN KAYU</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>023</td>
                                                    <td class="col-uraian">PEMUNGUTAN HASIL HUTAN BUKAN KAYU</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>024</td>
                                                    <td class="col-uraian">JASA PENUNJANG KEHUTANAN</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>031</td>
                                                    <td class="col-uraian">PERIKANAN TANGKAP</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>032</td>
                                                    <td class="col-uraian">PERIKANAN BUDIDAYA</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>

                                                <!-- Sektor B: Pertambangan dan Penggalian -->
                                                <tr class="row-group">
                                                    <td>B</td>
                                                    <td class="col-uraian">PERTAMBANGAN DAN PENGGALIAN</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                </tr>
                                                <tr>
                                                    <td>051</td>
                                                    <td class="col-uraian">PERTAMBANGAN BATU BARA</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>052</td>
                                                    <td class="col-uraian">PERTAMBANGAN LIGNIT</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>061</td>
                                                    <td class="col-uraian">PERTAMBANGAN MINYAK BUMI</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>062</td>
                                                    <td class="col-uraian">PERTAMBANGAN GAS ALAM DAN PANAS BUMI</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>071</td>
                                                    <td class="col-uraian">PERTAMBANGAN PASIR BESI DAN BIJIH BESI</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>072</td>
                                                    <td class="col-uraian">PERTAMBANGAN BIJIH LOGAM YANG TIDAK MENGANDUNG BESI, TIDAK TERMASUK BIJIH LOGAM MULIA</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>073</td>
                                                    <td class="col-uraian">PERTAMBANGAN BIJIH LOGAM MULIA</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>081</td>
                                                    <td class="col-uraian">PENGGALIAN BATU, PASIR DAN TANAH LIAT</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>089</td>
                                                    <td class="col-uraian">PERTAMBANGAN DAN PENGGALIAN LAINNYA YTDL</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>091</td>
                                                    <td class="col-uraian">JASA PERTAMBANGAN MINYAK BUMI DAN GAS ALAM</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>099</td>
                                                    <td class="col-uraian">JASA PERTAMBANGAN DAN PENGGALIAN LAINNYA</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>

                                                <!-- Sektor C: Industri Pengolahan -->
                                                <tr class="row-group">
                                                    <td>C</td>
                                                    <td class="col-uraian">INDUSTRI PENGOLAHAN</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                </tr>
                                                <tr>
                                                    <td>101</td>
                                                    <td class="col-uraian">INDUSTRI PENGOLAHAN DAN PENGAWETAN DAGING</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                </tr>
                                                <tr>
                                                    <td>102</td>
                                                    <td class="col-uraian">INDUSTRI PENGOLAHAN DAN PENGAWETAN IKAN DAN BIOTA AIR</td>
                                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
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
