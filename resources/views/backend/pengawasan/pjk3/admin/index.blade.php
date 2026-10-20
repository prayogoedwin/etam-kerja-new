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
                                    <h5 class="m-b-10">Ajuan PJK3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Perusahaan</th>
                                                <th>Tanggal Ajuan</th>
                                                <th>Status</th>
                                                <th>Masa Berlaku</th>
                                                <th>Aksi</th>
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
                ajax: '{{ route('pengawasan.pjk3.admin.index') }}',
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'perusahaan' },
                    { data: 'tanggal_fmt' },
                    { data: 'status_label' },
                    { data: 'masa_berlaku_fmt' },
                    { data: 'options', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush
