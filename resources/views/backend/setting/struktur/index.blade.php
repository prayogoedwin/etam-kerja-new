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
                                    <h5 class="m-b-10">Struktur</h5>
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
                                                <th>Tipe</th>
                                                <th>Kode Lokasi</th>
                                                <th>Kode Bidang</th>
                                                <th>Nama</th>
                                                <th>Slug</th>
                                                <th>Dibuat</th>
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
            ajax: '{{ route("struktur.index") }}',
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tipe_label' },
                { data: 'kode_lokasi' },
                { data: 'kode_bidang' },
                { data: 'nama' },
                { data: 'slug' },
                { data: 'created_at_fmt', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
