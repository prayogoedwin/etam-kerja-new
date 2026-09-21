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
                                    <h5 class="m-b-10">Peserta - {{ $pelatihan->nama_pelatihan }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0 mb-3">
                                    <div class="col-sm-6">
                                        <p class="mb-0">Untuk:
                                            {{ \App\Models\BLK\EtamBlkPelatihan::untukLabels()[(int) $pelatihan->pelatihan_untuk] ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <a href="{{ route('blk.pelatihan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>{{ (int) $pelatihan->pelatihan_untuk === 1 ? 'NIB' : 'NIK' }}</th>
                                                <th>Email</th>
                                                <th>HP</th>
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

        <div class="modal fade" id="modal-status" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="statusForm">
                            <input type="hidden" id="pesertaId">
                            <div class="mb-3">
                                <label class="form-label">Status Pendaftaran</label>
                                <select class="form-control" id="status_pendaftaran" name="status_pendaftaran">
                                    @foreach ($statusLabels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alasan Status</label>
                                <textarea class="form-control" id="alasan_status" name="alasan_status" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
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
                ajax: '{{ route('blk.pelatihan.peserta', $pelatihan->id) }}',
                autoWidth: false,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'identitas'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'hp'
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

            $('#statusForm').submit(function(e) {
                e.preventDefault();
                var id = $('#pesertaId').val();
                $.ajax({
                    type: 'PUT',
                    url: '{{ url('dapur/blk/pelatihan/' . $pelatihan->id . '/peserta') }}/' + id +
                        '/status',
                    data: {
                        status_pendaftaran: $('#status_pendaftaran').val(),
                        alasan_status: $('#alasan_status').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#modal-status').modal('hide');
                            $('#simpletable').DataTable().ajax.reload();
                        } else {
                            alert(response.message || 'Gagal update status');
                        }
                    }
                });
            });
        });

        function showStatusModal(id, status, alasan) {
            $('#pesertaId').val(id);
            $('#status_pendaftaran').val(status);
            $('#alasan_status').val(alasan);
            $('#modal-status').modal('show');
        }
    </script>
@endpush
