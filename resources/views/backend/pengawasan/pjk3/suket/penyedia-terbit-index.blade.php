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
                                    <h5 class="m-b-10">Suket Layak K3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    Menu ini untuk peran <b>perusahaan yang diperiksa</b>.
                                    Semua perusahaan berakun melihat LHPP dan Suket di sini, termasuk yang bukan PJK3.
                                </div>
                                <h5 class="mb-3">Dokumen hasil pemeriksaan perusahaan Anda</h5>
                                @include('backend.pengawasan.pjk3.suket._timeline-style')
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nomor</th>
                                                <th>PJK3</th>
                                                <th>Hasil</th>
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
                ajax: '{{ route('pengawasan.suket.penyedia.terbit.index') }}',
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nomor' },
                    { data: 'pjk3' },
                    { data: 'hasil' },
                    { data: 'status_label', orderable: false, searchable: false, width: '220px' },
                    { data: 'options', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush
