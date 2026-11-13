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
                                    <h5 class="m-b-10">Pengajuan Suket Layak K3 oleh PJK3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                        <h5 class="mb-0">Riwayat Pengajuan</h5>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <a href="{{ route('pengawasan.suket.penyedia.pengajuan.create') }}"
                                            class="btn btn-success btn-sm">
                                            <i class="feather icon-plus"></i> Ajukan Suket
                                        </a>
                                    </div>
                                </div>
                                @include('backend.pengawasan.pjk3.suket._timeline-style')
                                <div class="table-responsive mt-3">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Perusahaan diuji</th>
                                                <th>Kategori</th>
                                                <th>Status</th>
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
                ajax: '{{ route('pengawasan.suket.penyedia.pengajuan.index') }}',
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'perusahaan' },
                    { data: 'kategori_nama' },
                    { data: 'status_label', orderable: false, searchable: false, width: '220px' },
                    { data: 'options', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush
