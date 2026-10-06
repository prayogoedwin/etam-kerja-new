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
                                    <h5 class="m-b-10">User Integrasi API</h5>
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
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 text-end">
                                        <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal"
                                            data-bs-target="#modal-report">
                                            <i class="feather icon-plus"></i> Add Data
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Username</th>
                                                <th>Client ID</th>
                                                <th>X-API-Key</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                                <th>Created At</th>
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

        <div class="modal fade" id="modal-report" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah User Integrasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createForm">
                            <div class="mb-3">
                                <label class="form-label">Username (untuk dokumentasi)</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password (untuk dokumentasi)</label>
                                <input type="text" class="form-control" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Client ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="client_id" id="create_client_id">
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCredentials('create')">Generate</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">X-API-Key</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="api_key" id="create_api_key">
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCredentials('create')">Generate</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User Integrasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="edit_id">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="edit_username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password (kosongkan jika tidak diganti)</label>
                                <input type="text" class="form-control" name="password" id="edit_password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Client ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="client_id" id="edit_client_id" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCredentials('edit')">Generate</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">X-API-Key</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="api_key" id="edit_api_key" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCredentials('edit')">Generate</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status" id="edit_status" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan" id="edit_keterangan">
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function () {
        const table = $('#simpletable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('userintegrasi.index') }}",
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'username' },
                { data: 'client_id' },
                { data: 'api_key' },
                { data: 'status_label', orderable: false, searchable: false },
                { data: 'keterangan' },
                { data: 'created_at' },
                { data: 'options', orderable: false, searchable: false },
            ]
        });

        $('#createForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('userintegrasi.add') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.success) {
                        $('#modal-report').modal('hide');
                        $('#createForm')[0].reset();
                        table.ajax.reload();
                        alert(res.message || 'Berhasil');
                    } else {
                        alert(JSON.stringify(res.errors || res.message));
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Gagal menyimpan');
                }
            });
        });

        $('#editForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            $.ajax({
                url: `/dapur/users/integrasi/update/${id}`,
                method: 'PUT',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.success) {
                        $('#modal-edit').modal('hide');
                        table.ajax.reload();
                        alert(res.message || 'Berhasil');
                    } else {
                        alert(JSON.stringify(res.errors || res.message));
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Gagal update');
                }
            });
        });
    });

    function generateCredentials(mode) {
        $.get("{{ route('userintegrasi.generate') }}", function (res) {
            if (!res.success) return;
            if (mode === 'create') {
                $('#create_client_id').val(res.client_id);
                $('#create_api_key').val(res.api_key);
            } else {
                $('#edit_client_id').val(res.client_id);
                $('#edit_api_key').val(res.api_key);
            }
        });
    }

    function showEditModal(id) {
        $.get(`/dapur/users/integrasi/get/${id}`, function (res) {
            if (!res.success) return alert('Data tidak ditemukan');
            const d = res.data;
            $('#edit_id').val(d.id);
            $('#edit_username').val(d.username);
            $('#edit_password').val('');
            $('#edit_client_id').val(d.client_id);
            $('#edit_api_key').val(d.api_key);
            $('#edit_status').val(d.status);
            $('#edit_keterangan').val(d.keterangan);
            $('#modal-edit').modal('show');
        });
    }

    function regenerateKey(id) {
        if (!confirm('Generate ulang API key? Token aktif akan direset.')) return;
        $.ajax({
            url: `/dapur/users/integrasi/regen-key/${id}`,
            method: 'PUT',
            success: function (res) {
                alert(res.message + (res.api_key ? '\n' + res.api_key : ''));
                $('#simpletable').DataTable().ajax.reload();
            }
        });
    }

    function confirmDelete(id) {
        if (!confirm('Hapus user integrasi ini?')) return;
        $.ajax({
            url: `/dapur/users/integrasi/delete/${id}`,
            method: 'DELETE',
            success: function (res) {
                alert(res.message || 'Berhasil dihapus');
                $('#simpletable').DataTable().ajax.reload();
            }
        });
    }
</script>
@endpush
