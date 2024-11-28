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
                                    <h5 class="m-b-10">Pelamar Lowongan</h5>
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>Data Lowongan</h5>

                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Judul Lowongan</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->judul_lowongan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Deskripsi Pekerjaan</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->deskripsi }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Pria</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->jumlah_pria }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Wanita</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->jumlah_wanita }}</td>
                                        </tr>
                                        <tr>
                                            <td>Gaji</td>
                                            <td>:</td>
                                            <td> migrate db dlu gan</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                        <h5>Data Pelamar</h5>
                                    </div>
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
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>No Telepon</th>
                                                <th>Tanggal Lamar</th>
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

        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Data Pelamar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pertanyaan"><strong>Nama</strong></label>
                                    <input type="text" id="nama" disabled>
                                </div>
                            </div>
                        </div>


                        <div class="float-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </body>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lowongan.pelamar', $lowongan_id) }}',
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'whatsapp',
                        name: 'whatsapp'
                    },
                    // { data: 'keterangan', name: 'keterangan' },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>

    <script>
        function showDetailModal(pelamar_id) {
            // alert(pelamar_id)
            // // Send the data to the update route
            $.ajax({
                url: "{{ route('lowongan.detailpelamar', ':id') }}".replace(':id', pelamar_id),
                type: 'GET',
                success: function(response) {
                    // console.log(response)
                    if (response.status == 1) {

                        // Close modal
                        $('#modal-edit').modal('show');

                        // Set the data
                        $('#nama').val(response.data.name);
                    } else {
                        // Display error message
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    </script>
@endpush
