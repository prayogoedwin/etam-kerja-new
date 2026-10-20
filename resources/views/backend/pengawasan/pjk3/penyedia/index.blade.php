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

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                        <h5 class="mb-0">Riwayat Pengajuan</h5>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        @if ($canAjukan)
                                            <a href="{{ route('pengawasan.pjk3.penyedia.create') }}"
                                                class="btn btn-success btn-sm">
                                                <i class="feather icon-plus"></i> Ajukan PJK3
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal Ajuan</th>
                                                <th>Status</th>
                                                <th>Masa Berlaku</th>
                                                <th>Alasan</th>
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
                ajax: '{{ route('pengawasan.pjk3.penyedia.index') }}',
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tanggal_fmt' },
                    { data: 'status_label' },
                    { data: 'masa_berlaku_fmt' },
                    { data: 'alasan' },
                    { data: 'options', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush
