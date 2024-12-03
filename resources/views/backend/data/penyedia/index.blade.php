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
                                    <h5 class="m-b-10">Penyedia Kerja</h5>
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

                    <div class="col-sm-6">
                        <!-- Tombol untuk mengunduh CSV -->
                        {{-- <a href="{{ route('datapencari.exportCsv') }}" class="btn btn-success">Unduh CSV</a> --}}
                        <a href="javascript:void(0);" id="downloadCsv" class="btn btn-info">Unduh CSV</a>

                    </div>
                    <div class="col-sm-6 text-end">
                    </div>
                    </div>


                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">

                                    </div>
                                    <div class="col-sm-6 text-end">
                                        {{-- <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal" data-bs-target="#modal-report"><i class="feather icon-plus"></i> Add Data</button> --}}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Whatsapp</th>
                                                <th>NIB</th>
                                                <th>Sektor</th>
                                                <th>Provinsi</th>
                                                <th>Kabkota</th>
                                                <th>Kecamatan</th>
                                                <th>Alamat</th>
                                                <th>Kodepos</th>
                                                <th>Website</th>
                                                <th>Telepon</th>
                                                <th>Jenis Perusahaan</th>
                                                <th>PJ Akun (jabatan)</th>
                                                <th>Tanggal Daftar</th>
                                            </tr>
                                        </thead>

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
    <script>
        $(document).ready(function() {
            var table = $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('datapenyedia.index') }}',
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user.name'
                    },
                    {
                        data: 'user.email'
                    },
                    {
                        data: 'user.whatsapp'
                    },
                    {
                        data: 'nib'
                    },
                    {
                        data: 'sektor.name'
                    },
                    {
                        data: 'provinsi.name'
                    },
                    {
                        data: 'kabkota.name'
                    },
                    {
                        data: 'kecamatan.name'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'kodepos'
                    },
                    {
                        data: 'website'
                    },
                    {
                        data: 'telpon'
                    },
                    {
                        data: 'jenis_perusahaan'
                    },
                    {
                        data: 'jabatan'
                    },
                    {
                        data: 'created_at'
                    }
                ]
            });

            // Tombol untuk mengunduh CSV
            $('#downloadCsv').on('click', function() {
                // Pastikan table sudah terinisialisasi sebelum digunakan
                var searchTerm = table.search(); // Ambil term pencarian dari DataTable

                // Buat URL dengan parameter pencarian
                var url = '{{ route('datapenyedia.exportCsv') }}?search=' + searchTerm;

                // Redirect ke URL ekspor CSV
                window.location.href = url;
            });
        });
    </script>

    

   
@endpush
