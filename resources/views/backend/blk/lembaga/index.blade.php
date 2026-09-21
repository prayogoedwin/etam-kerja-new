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
                                    <h5 class="m-b-10">Daftar BLK</h5>
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
                                                <th>Nama Lembaga</th>
                                                <th>Tipe</th>
                                                <th>Kab/Kota</th>
                                                <th>Email</th>
                                                <th>Whatsapp</th>
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
                        <h5 class="modal-title">Tambah BLK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="registerForm">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Lembaga</label>
                                        <input type="text" class="form-control" id="nama_lembaga" name="nama_lembaga">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Tipe Lembaga</label>
                                        <select class="form-control" id="tipe_lembaga" name="tipe_lembaga">
                                            <option value="1">Provinsi</option>
                                            <option value="2">Kabupaten/Kota</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
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
                                        <label class="form-label">Telepon</label>
                                        <input type="text" class="form-control" id="telepon" name="telepon">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Website</label>
                                        <input type="text" class="form-control" id="website" name="website">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Instagram</label>
                                        <input type="text" class="form-control" id="instagram" name="instagram">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Kabupaten/Kota</label>
                                        <select class="form-control" id="kabkota_id" name="kabkota_id">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($kabkota as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Alamat Lengkap</label>
                                        <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3"></textarea>
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
                        <h5 class="modal-title">Edit BLK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="editId">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Lembaga</label>
                                        <input type="text" class="form-control" id="edit_nama_lembaga" name="nama_lembaga">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Tipe Lembaga</label>
                                        <select class="form-control" id="edit_tipe_lembaga" name="tipe_lembaga">
                                            <option value="1">Provinsi</option>
                                            <option value="2">Kabupaten/Kota</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="edit_email" name="email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Whatsapp</label>
                                        <input type="text" class="form-control" id="edit_whatsapp" name="whatsapp">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" class="form-control" id="edit_telepon" name="telepon">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Website</label>
                                        <input type="text" class="form-control" id="edit_website" name="website">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Instagram</label>
                                        <input type="text" class="form-control" id="edit_instagram" name="instagram">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Kabupaten/Kota</label>
                                        <select class="form-control" id="edit_kabkota_id" name="kabkota_id">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($kabkota as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Alamat Lengkap</label>
                                        <textarea class="form-control" id="edit_alamat_lengkap" name="alamat_lengkap" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
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
                ajax: '{{ route('blk.lembaga.index') }}',
                autoWidth: false,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lembaga'
                    },
                    {
                        data: 'tipe_lembaga_nama'
                    },
                    {
                        data: 'kabkota_nama'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'whatsapp'
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
                    url: '{{ route('blk.lembaga.store') }}',
                    data: {
                        nama_lembaga: $('#nama_lembaga').val(),
                        tipe_lembaga: $('#tipe_lembaga').val(),
                        email: $('#email').val(),
                        whatsapp: $('#whatsapp').val(),
                        telepon: $('#telepon').val(),
                        website: $('#website').val(),
                        instagram: $('#instagram').val(),
                        kabkota_id: $('#kabkota_id').val(),
                        alamat_lengkap: $('#alamat_lengkap').val(),
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
                            alert(response.message || 'Gagal menambahkan data');
                        }
                    }
                });
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                var id = $('#editId').val();
                $.ajax({
                    type: 'PUT',
                    url: '{{ url('dapur/blk/lembaga') }}/' + id,
                    data: {
                        nama_lembaga: $('#edit_nama_lembaga').val(),
                        tipe_lembaga: $('#edit_tipe_lembaga').val(),
                        email: $('#edit_email').val(),
                        whatsapp: $('#edit_whatsapp').val(),
                        telepon: $('#edit_telepon').val(),
                        website: $('#edit_website').val(),
                        instagram: $('#edit_instagram').val(),
                        kabkota_id: $('#edit_kabkota_id').val(),
                        alamat_lengkap: $('#edit_alamat_lengkap').val(),
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
                            alert(response.message || 'Gagal mengubah data');
                        }
                    }
                });
            });
        });

        function showEditModal(id) {
            $.get('{{ url('dapur/blk/lembaga') }}/' + id, function(response) {
                if (response.success) {
                    var data = response.data;
                    $('#editId').val(data.id);
                    $('#edit_nama_lembaga').val(data.nama_lembaga);
                    $('#edit_tipe_lembaga').val(data.tipe_lembaga);
                    $('#edit_email').val(data.email);
                    $('#edit_whatsapp').val(data.whatsapp);
                    $('#edit_telepon').val(data.telepon);
                    $('#edit_website').val(data.website);
                    $('#edit_instagram').val(data.instagram);
                    $('#edit_kabkota_id').val(data.kabkota_id);
                    $('#edit_alamat_lengkap').val(data.alamat_lengkap);
                    $('#modal-edit').modal('show');
                }
            });
        }

        function confirmDelete(id) {
            if (confirm('Yakin hapus data?')) {
                $.ajax({
                    url: '{{ url('dapur/blk/lembaga') }}/' + id,
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
    </script>
@endpush
