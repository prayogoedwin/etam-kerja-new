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
                                    <h5 class="m-b-10">{{ $judul }}</h5>
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
                                <div class="row align-items-center m-l-0 mb-3">
                                    <div class="col-sm-6">
                                        <p class="mb-0 text-muted">
                                            Status penandatanganan menempel di masing-masing peserta.
                                            @if ($waiting)
                                                Pilih satu atau beberapa peserta, lalu TTD untuk memajukan alur.
                                                File TTE belum digenerate.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        @if ($waiting)
                                            <button type="button" id="btn-bulk-ttd" class="btn btn-primary btn-sm">TTD terpilih</button>
                                            <button type="button" id="btn-ttd-semua" class="btn btn-outline-primary btn-sm">TTD semua antrean</button>
                                        @endif
                                        <a href="{{ $waiting ? route('blk.ttd.sudah') : route('blk.ttd.menunggu') }}"
                                            class="btn btn-secondary btn-sm">
                                            {{ $waiting ? 'Sudah ditandatangani' : 'Menunggu TTD' }}
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="ttdtable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                @if ($waiting)
                                                    <th width="40">
                                                        <input type="checkbox" id="check-all" title="Pilih semua di halaman ini">
                                                    </th>
                                                @endif
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>NIK</th>
                                                <th>Pelatihan</th>
                                                <th>UPTD / BLK</th>
                                                <th>Status TTD</th>
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

        <form id="form-tte" action="{{ route('blk.ttd.tte') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="pilih_semua" id="pilih_semua" value="0">
            <div id="tte-ids"></div>
        </form>
    </body>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var waiting = {{ $waiting ? 'true' : 'false' }};
            var columns = [];
            if (waiting) {
                columns.push({
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '40px'
                });
            }
            columns.push({
                data: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'name'
            }, {
                data: 'ktp'
            }, {
                data: 'pelatihan_nama',
                orderable: false,
                searchable: false
            }, {
                data: 'blk_nama',
                orderable: false,
                searchable: false
            }, {
                data: 'status_label',
                orderable: false,
                searchable: false
            }, {
                data: 'options',
                orderable: false,
                searchable: false,
                width: '180px'
            });

            $('#ttdtable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route($waiting ? 'blk.ttd.menunggu' : 'blk.ttd.sudah') }}',
                autoWidth: false,
                columns: columns,
                drawCallback: function() {
                    $('#check-all').prop('checked', false);
                }
            });

            $('#check-all').on('change', function() {
                $('.peserta-check').prop('checked', this.checked);
            });

            $('#btn-bulk-ttd').on('click', function() {
                var ids = $('.peserta-check:checked').map(function() {
                    return this.value;
                }).get();
                if (!ids.length) {
                    alert('Pilih minimal satu peserta, atau gunakan TTD semua antrean');
                    return;
                }
                submitTte(ids, false);
            });

            $('#btn-ttd-semua').on('click', function() {
                if (!confirm('Tandatangani semua peserta dalam antrean ini?')) {
                    return;
                }
                submitTte([], true);
            });

            $(document).on('click', '.btn-ttd-satu', function() {
                submitTte([$(this).data('id')], false);
            });
        });

        function submitTte(ids, pilihSemua) {
            var $form = $('#form-tte');
            $('#tte-ids').empty();
            $('#pilih_semua').val(pilihSemua ? '1' : '0');
            ids.forEach(function(id) {
                $('#tte-ids').append($('<input>', {
                    type: 'hidden',
                    name: 'peserta_ids[]',
                    value: id
                }));
            });
            $form.trigger('submit');
        }
    </script>
@endpush
