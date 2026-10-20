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
                                    <h5 class="m-b-10">Dashboard Kabid - Pengawasan K3</h5>
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
                                        <h5 class="mb-0">Daftar Permohonan Masuk (Disposisi Kadis)</h5>
                                    </div>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table id="tableKabid" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Perusahaan</th>
                                                <th>Nama Alat</th>
                                                <th>Kategori / Jenis</th>
                                                <th>Lokasi</th>
                                                <th>Tanggal Masuk</th>
                                                <th>Status Disposisi</th>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#tableKabid').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('pengawasan.k3.kabid.index') }}',
            autoWidth: false,
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'perusahaan_nama' },
                { data: 'nama_alat' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return (row.kategori ? row.kategori.nama : '-') + '<br><small class="text-muted">' + (row.jenis ? row.jenis.nama : '-') + '</small>';
                    }
                },
                { data: 'lokasi_alat' },
                { data: 'tanggal_fmt' },
                { data: 'status_dispo', orderable: false, searchable: false },
                { data: 'options', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
