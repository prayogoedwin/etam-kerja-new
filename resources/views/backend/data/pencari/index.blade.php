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
                                    <h5 class="m-b-10">Pemberi Kerja</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->


                <!-- [ Main Content ] start -->
                <div class="row">

                    <div class="row align-items-center m-l-0" hidden>
                        <div class="col-sm-6">
                            <!-- Tombol untuk mengunduh CSV -->
                            {{-- <a href="{{ route('datapencari.exportCsv') }}" class="btn btn-success">Unduh CSV</a> --}}
                            {{-- <a href="javascript:void(0);" id="downloadCsv" class="btn btn-info">Unduh CSV</a> --}}

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
                                        <a href="javascript:void(0);" id="downloadCsv" class="btn btn-success">Unduh CSV</a>
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
                                                <th>NIK</th>
                                                <th>Tempat Lahir</th>
                                                <th>Tanggal Lahir</th>
                                                <th>Gender</th>
                                                <th>Status Perkawinan</th>
                                                <th>Agama</th>
                                                <th>Provinsi</th>
                                                <th>Kabkota</th>
                                                <th>Kecamatan</th>
                                                <th>Alamat</th>
                                                <th>Kodepos</th>
                                                <th>Pendidikan Terakhir</th>
                                                <th>Jurusan</th>
                                                <th>Tahun Lulus</th>
                                                <th>Medsos</th>
                                                <th>Disabilitas</th>
                                                <th>Status Kerja</th>
                                                <th>Tanggal Daftar</th>
                                                <th>Options</th>
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
            // Inisialisasi DataTable
            var table = $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('datapencari.index') }}',
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
                        data: 'ktp'
                    },
                    {
                        data: 'tempat_lahir'
                    },
                    {
                        data: 'tanggal_lahir'
                    },
                    {
                        data: 'gender'
                    },
                    {
                        data: 'id_status_perkawinan'
                    },
                    {
                        data: 'agama.name'
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
                        data: 'pendidikan.name'
                    },
                    {
                        data: 'jurusan.nama'
                    },
                    {
                        data: 'tahun_lulus'
                    },
                    {
                        data: 'medsos'
                    },
                    {
                        data: 'disabilitas'
                    },
                    {
                        data: 'is_diterima'
                    },
                    {
                        data: 'created_at'
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
                var url = '{{ route('datapencari.exportCsv') }}?search=' + searchTerm;

                // Redirect ke URL ekspor CSV
                window.location.href = url;
            });
        });
    </script>
@endpush
