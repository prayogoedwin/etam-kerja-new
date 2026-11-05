@extends('backend.template.backend')

@section('title', 'PP Expired')

@section('content')
<body class="box-layout container background-green">
    <div class="pcoded-main-container">
        <div class="pcoded-content">

            {{-- Page header --}}
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Peraturan Perusahaan Mendekati Expired</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#"><i class="feather icon-home"></i></a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="#">PP Expired</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <label class="small text-muted mb-1">Rentang ke depan</label>
                                    <select id="filter-range" class="form-control">
                                        <option value="3">3 Bulan ke depan</option>
                                        <option value="6" selected>6 Bulan ke depan</option>
                                        <option value="12">12 Bulan ke depan</option>
                                        <option value="24">24 Bulan ke depan</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <label class="small text-muted mb-1">Status</label>
                                    <select id="filter-status" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="akan_expired">Akan Expired</option>
                                        <option value="expired">Sudah Expired</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="table-expired" class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Perusahaan</th>
                                            <th>Jenis Ajuan</th>
                                            <th>Masa Berlaku</th>
                                            <th>Nomor SK</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Expired</th>
                                            <th>Sisa Hari</th>
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
$(function () {
    let table = $('#table-expired').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('hi.pp.admbidang.expired') }}",
            data: function (d) {
                d.range_bulan = $('#filter-range').val();
                d.filter      = $('#filter-status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',        name: 'DT_RowIndex',        orderable: false, searchable: false },
            { data: 'perusahaan_nama',    name: 'perusahaan_nama' },
            { data: 'jenis_ajuan_nama',   name: 'jenis_ajuan_nama' },
            { data: 'masa_berlaku',       name: 'masa_berlaku',       orderable: false, searchable: false },
            { data: 'nomor_sk',           name: 'nomor_sk' },
            { data: 'tanggal_mulai',      name: 'tanggal_mulai' },
            { data: 'tanggal_expired',    name: 'tanggal_expired',    orderable: false, searchable: false },
            { data: 'sisa_hari',          name: 'sisa_hari',          orderable: false, searchable: false },
            { data: 'options',            name: 'options',            orderable: false, searchable: false }
        ]
    });

    $('#filter-range, #filter-status').on('change', function () {
        table.ajax.reload();
    });
});
</script>
@endpush
