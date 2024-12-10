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
                                    <h5 class="m-b-10">Penempatan</h5>
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
                                    <div class="col-sm-6">
                                        <h5>IPK III/1: IKHTISAR STATISTIK IPK (DESEMBER 2024)</h5>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="3">I.PENCARI KERJA</th>
                                                <th colspan="10">KELOMPOK UMUR</th>
                                                <th rowspan="2" colspan="3">Jumlah</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">15-19</th>
                                                <th colspan="2">20-29</th>
                                                <th colspan="2">30-44</th>
                                                <th colspan="2">45-54</th>
                                                <th colspan="2">55+</th>
                                            </tr>
                                            <tr>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>L</th>
                                                <th>P</th>
                                                <th>L+P</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1. Pencari kerja yang belum ditempatkan pada bulan yang lalu</td>
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
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>2. Pencari kerja yang terdaftar pada bulan ini</td>
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
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>A. JUMLAH (1+2)</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>3. Pencari kerja yang ditempatkan pada bulan ini</td>
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
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>4. Pencari kerja yang dihapuskan pada bulan ini</td>
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
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>B. JUMLAH (3+4)</td>
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
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>5. Pencari kerja yang belum ditempatkan pada akhir bulan ini (A-B)</td>
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
                                                <td>0</td>
                                            </tr>
                                        </tbody>
                                        <thead class="text-center">
                                            <tr>
                                                <th >II. LOWONGAN</th>
                                                <th >L</th>
                                                <th >W</th>
                                                <th >L+W</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center">
                                                <td>1</td>
                                                <td>2</td>
                                                <td>3</td>
                                                <td>4</td>
                                            </tr>
                                            <tr>
                                                <td>1. Lowongan yang belum dipenuhi pada akhir bulan lalu</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>2. Lowongan yang terdaftar bulan ini</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>C. JUMLAH (1+2)</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>3. Lowongan yang dipenuhi bulan ini</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>4. Lowongan yang dihapuskan bulan ini</td>
                                                <td></td>
                                                <td></td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>C. JUMLAH (3+4)</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td>5. Lowongan yang belum dipenuhi akhir bulan ini (C-D)</td>
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
