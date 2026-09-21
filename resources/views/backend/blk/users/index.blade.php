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
                                    <h5 class="m-b-10">User BLK</h5>
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
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 text-end">
                                        <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal"
                                            data-bs-target="#modal-report"><i class="feather icon-plus"></i> Add
                                            Data</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Whatsapp</th>
                                                <th>BLK</th>
                                                <th>Tipe Akun</th>
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

        <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah User BLK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="registerForm">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="name" name="name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                        <small class="text-muted">Password awal sama dengan email</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Whatsapp</label>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Tipe Akun</label>
                                        <select class="form-control" id="tipe_akun" name="tipe_akun">
                                            @foreach ($tipeAkun as $key => $label)
                                                <option value="{{ $key }}" @selected($key == 3)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">BLK</label>
                                        <select class="form-control" id="blk_id" name="blk_id">
                                            <option value="0">-- Tidak diisi --</option>
                                            @foreach ($blkOptions as $blk)
                                                <option value="{{ $blk->id }}">{{ $blk->nama_lembaga }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User BLK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="editId">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="edit_name" name="name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Whatsapp</label>
                                <input type="text" class="form-control" id="edit_whatsapp" name="whatsapp">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipe Akun</label>
                                <select class="form-control" id="edit_tipe_akun" name="tipe_akun">
                                    @foreach ($tipeAkun as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">BLK</label>
                                <select class="form-control" id="edit_blk_id" name="blk_id">
                                    <option value="0">-- Tidak diisi --</option>
                                    @foreach ($blkOptions as $blk)
                                        <option value="{{ $blk->id }}">{{ $blk->nama_lembaga }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
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
                ajax: '{{ route('blk.users.index') }}',
                autoWidth: false,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'whatsapp'
                    },
                    {
                        data: 'blk_nama'
                    },
                    {
                        data: 'tipe_akun_nama'
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#registerForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('blk.users.store') }}',
                    data: {
                        name: $('#name').val(),
                        email: $('#email').val(),
                        whatsapp: $('#whatsapp').val(),
                        tipe_akun: $('#tipe_akun').val(),
                        blk_id: $('#blk_id').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#modal-report').modal('hide');
                            $('#simpletable').DataTable().ajax.reload();
                        } else if (response.errors) {
                            let errorMessages = '';
                            $.each(response.errors, function(key, value) {
                                $.each(value, function(index, errorMessage) {
                                    errorMessages += errorMessage + '\n';
                                });
                            });
                            alert('Terjadi kesalahan:\n' + errorMessages);
                        } else {
                            alert(response.message || 'Gagal menambahkan user');
                        }
                    }
                });
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                var id = $('#editId').val();
                $.ajax({
                    type: 'PUT',
                    url: '{{ url('dapur/blk/users') }}/' + id,
                    data: {
                        name: $('#edit_name').val(),
                        email: $('#edit_email').val(),
                        whatsapp: $('#edit_whatsapp').val(),
                        tipe_akun: $('#edit_tipe_akun').val(),
                        blk_id: $('#edit_blk_id').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#modal-edit').modal('hide');
                            $('#simpletable').DataTable().ajax.reload();
                        } else if (response.errors) {
                            let errorMessages = '';
                            $.each(response.errors, function(key, value) {
                                $.each(value, function(index, errorMessage) {
                                    errorMessages += errorMessage + '\n';
                                });
                            });
                            alert('Terjadi kesalahan:\n' + errorMessages);
                        } else {
                            alert(response.message || 'Gagal mengubah user');
                        }
                    }
                });
            });
        });

        function showEditModal(id) {
            $.get('{{ url('dapur/blk/users') }}/' + id, function(response) {
                if (response.success) {
                    var data = response.data;
                    $('#editId').val(data.id);
                    $('#edit_name').val(data.user ? data.user.name : '');
                    $('#edit_email').val(data.user ? data.user.email : '');
                    $('#edit_whatsapp').val(data.user ? data.user.whatsapp : '');
                    $('#edit_tipe_akun').val(data.tipe_akun);
                    $('#edit_blk_id').val(data.blk_id);
                    $('#modal-edit').modal('show');
                }
            });
        }

        function confirmDelete(id) {
            if (confirm('Yakin hapus data?')) {
                $.ajax({
                    url: '{{ url('dapur/blk/users') }}/' + id,
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

        function confirmReset(id) {
            if (confirm('Yakin reset password (password akan direset sesuai email)?')) {
                $.ajax({
                    url: '{{ url('dapur/blk/users') }}/' + id + '/reset',
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message);
                    }
                });
            }
        }
    </script>
@endpush
