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
                                    
                                </div>
                                <div class="table-responsive">
                                    <table id="ak1table" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Tenaga Kerja</th>
                                                <th>Tanggal Cetak</th>
                                                <th>Status Cetak</th>
                                                <th>Berhenti Berlaku</th>
                                                <th>QR Code</th>
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
        $('#ak1table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('ak1.data') }}',
            autoWidth: false,
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama_tenaga_kerja',
                    name: 'user.name'
                },
                {
                    data: 'tanggal_cetak',
                    name: 'tanggal_cetak'
                },
                {
                    data: 'status_cetak',
                    name: 'status_cetak'
                },
                {
                    data: 'berlaku_hingga',
                    name: 'berlaku_hingga'
                },
                {
                    data: 'qr_code',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
</script>
@endpush
