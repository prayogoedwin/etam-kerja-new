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
                                        <h5>IPK III/8 : PENEMPATAN PENCARI KERJA MENURUT JENIS ANTAR KERJA TINGKAT
                                            PENDIDIKAN PENCARI KERJA, PENERIMA TENAGA KERJA DAN JENIS KELAMIN (DESEMBER
                                            2024)</h5>
                                    </div>
                                </div>
                                <div class="table-responsive">

                                    <table class="table table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="3">Kode</th>
                                                <th rowspan="3">I . Tingkat Pendidikan Pencari Kerja yg ditempatkan</th>
                                                <th colspan="9">Jenis Antar Kerja</th>
                                                <th rowspan="2" colspan="3">Jumlah</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">AKL</th>
                                                <th colspan="3">AKAD</th>
                                                <th colspan="3">AKAN</th>
                                            </tr>
                                            <tr>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center">
                                                <td>1</td>
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
                                                <td>12</td>
                                                <td>13</td>
                                                <td>14</td>
                                            </tr>
                                            <tr class="text-center">
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
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>NON</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>SD</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>SLTP</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>SLTA</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>DIMPLOMA 1</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>DIMPLOMA 2</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>D3/SARJANA MUDA</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>D4/SARJANA SAINS TERAPAN</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>AKTA 2</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td>AKTA 3</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>11</td>
                                                <td>SARJANA</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>12</td>
                                                <td>MAGISTER</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>13</td>
                                                <td>DOKTORAL</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr class="text-center">
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
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>


                                            <!-- REKAP -->
                                            <tr class="text-center">
                                                <td></td>
                                                <td>
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
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <br />


                                    <table class="table table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="3">Kode</th>
                                                <th rowspan="3">I . Tingkat Pendidikan Pencari Kerja yg ditempatkan</th>
                                                <th colspan="9">Jenis Antar Kerja</th>
                                                <th rowspan="2" colspan="3">Jumlah</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">AKL</th>
                                                <th colspan="3">AKAD</th>
                                                <th colspan="3">AKAN</th>
                                            </tr>
                                            <tr>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>JML</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>INSTANSI PEMERINTAH</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>BUMN/BUMD</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>KOPERASI</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>PERUSAHAAN SWASTA</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>BADAN USAHA LAINYA</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>PERORANGAN</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr class="text-center">
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
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>


                                            <!-- REKAP -->
                                            <tr class="text-center">
                                                <td></td>
                                                <td>
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
