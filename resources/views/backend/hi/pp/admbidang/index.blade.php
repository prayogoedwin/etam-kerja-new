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
                                    <h5 class="m-b-10">Ajuan Peraturan Perusahaan</h5>
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

                                    </div>
                                    <div class="col-sm-6 text-end">
                                        {{-- <a href="{{route('hi.pp.penyedia.tambah')}}" class="btn btn-success btn-sm"><i class="feather icon-plus"></i> Add Data</a> --}}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <th>No</th>
                                            <th>Jenis Ajuan</th>
                                            <th>Nomor</th>
                                            <th>Tanggal</th>
                                            <th>Status Admin</th>
                                            <th>Status Kasi</th>
                                            <th>Options</th>
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
    // $(document).ready(function() {
    //     $('#simpletable').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         ajax: '{{ route('hi.pp.penyedia.index') }}',
    //         autoWidth: false, // Menonaktifkan auto-width
    //         columns: [
    //             { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
    //             { data: 'jenis_ajuan_nama', name: 'jenis_ajuan_nama' },
    //             { data: 'nomor', name: 'nomor' },
    //             { data: 'tanggal_fmt', name: 'tanggal_fmt' },
    //             { data: 'status_admin', name: 'status_admin', orderable: false, searchable: false },
    //             { data: 'status_kasi',  name: 'status_kasi',  orderable: false, searchable: false },
    //             { data: 'options',      name: 'options',      orderable: false, searchable: false }
    //         ]
    //     });
    // });
</script>

<script>

</script>
@endpush

