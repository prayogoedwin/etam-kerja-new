@extends('backend.template.backend')

@section('content')

    <body class="box-layout container background-green">
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Pelatihan BLK</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Sukses!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6"></div>
                                    @if ($canManage)
                                        <div class="col-sm-6 text-end">
                                            <a href="{{ route('blk.pelatihan.create') }}"
                                                class="btn btn-success btn-sm btn-round has-ripple"><i
                                                    class="feather icon-plus"></i> Add Data</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pelatihan</th>
                                                <th>BLK</th>
                                                <th>Untuk</th>
                                                <th>Pendaftaran</th>
                                                <th>Pelaksanaan</th>
                                                <th>Status</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
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
                ajax: '{{ route('blk.pelatihan.index') }}',
                autoWidth: false,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_pelatihan'
                    },
                    {
                        data: 'blk_nama'
                    },
                    {
                        data: 'untuk_nama'
                    },
                    {
                        data: 'periode_daftar'
                    },
                    {
                        data: 'periode_pelaksanaan'
                    },
                    {
                        data: 'status_label'
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        function confirmDelete(id) {
            if (confirm('Yakin hapus data?')) {
                $.ajax({
                    url: '{{ url('dapur/blk/pelatihan') }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message);
                        $('#simpletable').DataTable().ajax.reload();
                    }
                });
            }
        }
    </script>
@endpush
