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
                                    <h5 class="m-b-10">BKK</h5>
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
                                        <h5>Data</h5>
                                    </div>
                                    {{-- <div class="col-sm-6 text-end">
                                        <a href="javascript:void(0);" id="downloadCsv" class="btn btn-success">Unduh CSV</a>
                                    </div> --}}
                                    {{-- <div class="col-sm-6 text-end">
                                        <button id="btnAdd" class="btn btn-success btn-sm btn-round has-ripple"
                                            data-bs-toggle="modal" data-bs-target="#modal-report"><i
                                                class="feather icon-plus"></i> Add
                                            Data</button>
                                    </div> --}}
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama BKK</th>
                                                <th>Alamat</th>
                                                <th>HP</th>
                                                <th>Kontak</th>
                                                <th>Jabatan</th>
                                                <th>Website</th>
                                                <th>Aksi</th>
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

        <div class="modal fade" id="modal-report" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                </div>
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
                ajax: '{{ route('bkk.admin.index') }}',
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'hp'
                    },
                    {
                        data: 'contact_person'
                    },
                    {
                        data: 'jabatan'
                    },
                    {
                        data: 'website'
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Tombol untuk mengunduh CSV
            $('#downloadCsv').on('click', function() {
                // Pastikan table sudah terinisialisasi sebelum digunakan
                var searchTerm = table.search(); // Ambil term pencarian dari DataTable

                // Buat URL dengan parameter pencarian
                var url = '{{ route('databkk.exportCsv') }}?search=' + searchTerm;

                // Redirect ke URL ekspor CSV
                window.location.href = url;
            });
        });
    </script>
@endpush
