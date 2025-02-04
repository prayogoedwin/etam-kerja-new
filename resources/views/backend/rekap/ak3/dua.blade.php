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
                                        <h5>IPK III/2: PEMBERI KERJA YANG TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN (DESEMBER
                                            2024)</h5>
                                    </div>
                                </div>
                                <div class="table-responsive">

                                    <table class="table table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="2">Kode</th>
                                                <th rowspan="2">Jenis Pendidikan</th>
                                                <th colspan="2">Sisa Akhir Bulan</th>
                                                <th colspan="2">Pendaftaran bulan ini</th>
                                                <th colspan="2">Penempatan bulan ini</th>
                                                <th colspan="2">Penghapuskan bulan ini</th>
                                                <th colspan="2">Sisa Akhir Bulan Ini</th>
                                            </tr>
                                            <tr>
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
                                        </thead>
                                        <tbody>
                                            <tr class="text-center">
                                                <td colspan="2">1</td>
                                                <td>2</td>
                                                <td>3</td>
                                                <td>4</td>
                                                <td>5</td>
                                                <td>6</td>
                                                <td>7</td>
                                                <td>8</td>
                                                <td>9</td>
                                                <td>10</td>
                                                <td>11</td>
                                            </tr>

                                            <tr>
                                                <td>1000</td>
                                                <td>SEKOLAH DASAR</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <td>2000</td>
                                                <td>SLTP</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <td>3000</td>
                                                <td>SLTA / SMK / Sederajat</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <td>6000</td>
                                                <td>SARJANA (S1)</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <td>7000</td>
                                                <td>MAGISTER (S2)</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>



                                            <!-- REKAP -->
                                            <tr class="text-center">
                                                <td colspan="2">
                                                    <p class="fw-bold">JUMLAH</p>
                                                </td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
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
