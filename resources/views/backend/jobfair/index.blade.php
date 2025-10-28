@extends('backend.template.backend')

@section('content')

<body class="box-layout container background-green">
    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        <div class="pcoded-content">

            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Job Fair Management</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- customar project  start -->
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center m-l-0">
                                <div class="col-sm-6">
                                </div>
                                <div class="col-sm-6 text-end">
                                    <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal"
                                        data-bs-target="#modal-report"><i class="feather icon-plus"></i> Add Data</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="simpletable" class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Job Fair</th>
                                            <th>Penyelenggara</th>
                                            <th>Jenis</th>
                                            <th>Tipe</th>
                                            <th>Partnership</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Status Verifikasi</th>
                                            <th>Status</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- customar project  end -->
            </div>
            <!-- [ Main Content ] end -->

        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Job Fair</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="jobFairForm" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="form-control" id="jenis_penyelenggara" name="jenis_penyelenggara" required>
                                        <option value="">Pilih Jenis Penyelenggara</option>
                                        <option value="0">Pemerintah</option>
                                        <option value="1">Swasta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="nama_job_fair">Nama Job Fair <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_job_fair" name="nama_job_fair" required>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="penyelenggara">Penyelenggara</label>
                                    <input type="text" class="form-control" id="penyelenggara" name="penyelenggara">
                                </div>

                                <div class="form-group">
                                    <select class="form-control" id="id_penyelenggara" name="id_penyelenggara">
                                        <option value="">Pilih User Penyelenggara</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" id="tipe_job_fair" name="tipe_job_fair" required>
                                        <option value="">Pilih Tipe Job Fair</option>
                                        <option value="0">Online</option>
                                        <option value="1">Offline</option>
                                    </select>
                                </div>

                                <!-- UPDATED: Kota dari Input ke Select -->
                                <div class="form-group">
                                    <select class="form-control" id="kota" name="kota" required>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                        @foreach (getKabkota() as $kabkota)
                                            <option value="{{ $kabkota->id }}">{{ $kabkota->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="lokasi_penyelenggaraan">Lokasi Penyelenggaraan</label>
                                    <input type="text" class="form-control" id="lokasi_penyelenggaraan" name="lokasi_penyelenggaraan">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="poster">Poster</label>
                                    <input type="file" class="form-control" id="poster" name="poster" accept="image/*">
                                    <small class="text-muted">Max 2MB (jpg, jpeg, png, gif)</small>
                                    <div id="poster_preview" class="mt-2"></div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="form-control" id="tipe_partnership" name="tipe_partnership" required>
                                        <option value="">Pilih Tipe Partnership</option>
                                        <option value="0">Tertutup</option>
                                        <option value="1">Open (Perusahaan bisa daftar)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="tanggal_open_pendaftaran_tenant">Tanggal Buka Pendaftaran Tenant</label>
                                    <input type="date" class="form-control" id="tanggal_open_pendaftaran_tenant" name="tanggal_open_pendaftaran_tenant">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="tanggal_close_pendaftaran_tenant">Tanggal Tutup Pendaftaran Tenant</label>
                                    <input type="date" class="form-control" id="tanggal_close_pendaftaran_tenant" name="tanggal_close_pendaftaran_tenant">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="tanggal_selesai">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai">
                                </div>

                                <div class="form-group">
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="1" selected>Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"></textarea>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-danger" onclick="clearForm()">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Job Fair</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editJobFairForm" enctype="multipart/form-data">
                        <input type="hidden" id="editJobFairId">
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="form-control" id="editJenisPenyelenggara" name="jenis_penyelenggara" required>
                                        <option value="">Pilih Jenis Penyelenggara</option>
                                        <option value="0">Pemerintah</option>
                                        <option value="1">Swasta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editNamaJobFair">Nama Job Fair <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editNamaJobFair" name="nama_job_fair" required>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editPenyelenggara">Penyelenggara</label>
                                    <input type="text" class="form-control" id="editPenyelenggara" name="penyelenggara">
                                </div>

                                <div class="form-group">
                                    <select class="form-control" id="editIdPenyelenggara" name="id_penyelenggara">
                                        <option value="">Pilih User Penyelenggara</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" id="editTipeJobFair" name="tipe_job_fair" required>
                                        <option value="">Pilih Tipe Job Fair</option>
                                        <option value="0">Online</option>
                                        <option value="1">Offline</option>
                                    </select>
                                </div>

                                <!-- UPDATED: Kota dari Input ke Select -->
                                <div class="form-group">
                                    <select class="form-control" id="editKota" name="kota" required>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                        @foreach (getKabkota() as $kabkota)
                                            <option value="{{ $kabkota->id }}">{{ $kabkota->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editLokasiPenyelenggaraan">Lokasi Penyelenggaraan</label>
                                    <input type="text" class="form-control" id="editLokasiPenyelenggaraan" name="lokasi_penyelenggaraan">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editPoster">Poster</label>
                                    <input type="file" class="form-control" id="editPoster" name="poster" accept="image/*">
                                    <small class="text-muted">Max 2MB (jpg, jpeg, png, gif)</small>
                                    <div id="edit_poster_preview" class="mt-2"></div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="form-control" id="editTipePartnership" name="tipe_partnership" required>
                                        <option value="">Pilih Tipe Partnership</option>
                                        <option value="0">Tertutup</option>
                                        <option value="1">Open (Perusahaan bisa daftar)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editTanggalOpenPendaftaranTenant">Tanggal Buka Pendaftaran Tenant</label>
                                    <input type="date" class="form-control" id="editTanggalOpenPendaftaranTenant" name="tanggal_open_pendaftaran_tenant">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editTanggalClosePendaftaranTenant">Tanggal Tutup Pendaftaran Tenant</label>
                                    <input type="date" class="form-control" id="editTanggalClosePendaftaranTenant" name="tanggal_close_pendaftaran_tenant">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editTanggalMulai">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="editTanggalMulai" name="tanggal_mulai">
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editTanggalSelesai">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="editTanggalSelesai" name="tanggal_selesai">
                                </div>

                                <div class="form-group">
                                    <select class="form-control" id="editStatus" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="floating-label" for="editDeskripsi">Deskripsi</label>
                                    <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="5"></textarea>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Job Fair</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#simpletable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('jobfair.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_job_fair', name: 'nama_job_fair' },
            { data: 'penyelenggara_name', name: 'penyelenggara_name' },
            { data: 'jenis_penyelenggara_text', name: 'jenis_penyelenggara' },
            { data: 'tipe_job_fair_text', name: 'tipe_job_fair' },
            { data: 'tipe_partnership_text', name: 'tipe_partnership' },
            { data: 'tanggal_mulai', name: 'tanggal_mulai' },
            { data: 'tanggal_selesai', name: 'tanggal_selesai' },
            { data: 'status_verifikasi_badge', name: 'status_verifikasi', orderable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'options', name: 'options', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    // Load users for dropdown
    loadUsers();

    // Preview poster on create form
    $('#poster').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#poster_preview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
            }
            reader.readAsDataURL(file);
        }
    });

    // Preview poster on edit form
    $('#editPoster').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#edit_poster_preview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
            }
            reader.readAsDataURL(file);
        }
    });

    // Submit Create Form
    $('#jobFairForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('jobfair.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-report').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                    $('#jobFairForm')[0].reset();
                    $('#poster_preview').html('');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = '';
                    $.each(errors, function(key, value) {
                        $.each(value, function(index, errorMessage) {
                            errorMessages += errorMessage + '\n';
                        });
                    });
                    alert('Terjadi kesalahan:\n' + errorMessages);
                } else {
                    alert('Gagal menambahkan job fair: ' + xhr.responseJSON.message);
                }
            }
        });
    });

    // Submit Edit Form
    $('#editJobFairForm').submit(function(e) {
        e.preventDefault();
        
        var id = $('#editJobFairId').val();
        var formData = new FormData(this);
        formData.append('_method', 'PUT');

        $.ajax({
            url: "{{ url('admin/jobfair') }}/" + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-edit').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = '';
                    $.each(errors, function(key, value) {
                        $.each(value, function(index, errorMessage) {
                            errorMessages += errorMessage + '\n';
                        });
                    });
                    alert('Terjadi kesalahan:\n' + errorMessages);
                } else {
                    alert('Gagal mengupdate job fair: ' + xhr.responseJSON.message);
                }
            }
        });
    });
});

function loadUsers() {
    $.ajax({
        url: "{{ route('jobfair.users') }}",
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var selectCreate = $('#id_penyelenggara');
                var selectEdit = $('#editIdPenyelenggara');
                
                selectCreate.empty();
                selectEdit.empty();
                
                selectCreate.append('<option value="">Pilih User Penyelenggara</option>');
                selectEdit.append('<option value="">Pilih User Penyelenggara</option>');
                
                $.each(response.data, function(key, user) {
                    selectCreate.append('<option value="' + user.id + '">' + user.name + ' (' + user.email + ')</option>');
                    selectEdit.append('<option value="' + user.id + '">' + user.name + ' (' + user.email + ')</option>');
                });
            }
        }
    });
}

function clearForm() {
    $('#jobFairForm')[0].reset();
    $('#poster_preview').html('');
}

function showEditModal(id) {
    $.ajax({
        url: "{{ url('admin/jobfair') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                
                $('#editJobFairId').val(data.id);
                $('#editJenisPenyelenggara').val(data.jenis_penyelenggara);
                $('#editIdPenyelenggara').val(data.id_penyelenggara);
                $('#editNamaJobFair').val(data.nama_job_fair);
                $('#editPenyelenggara').val(data.penyelenggara);
                $('#editDeskripsi').val(data.deskripsi);
                $('#editTipeJobFair').val(data.tipe_job_fair);
                $('#editKota').val(data.kota);
                $('#editLokasiPenyelenggaraan').val(data.lokasi_penyelenggaraan);
                $('#editTanggalOpenPendaftaranTenant').val(data.tanggal_open_pendaftaran_tenant);
                $('#editTipePartnership').val(data.tipe_partnership);
                $('#editTanggalClosePendaftaranTenant').val(data.tanggal_close_pendaftaran_tenant);
                $('#editTanggalMulai').val(data.tanggal_mulai);
                $('#editTanggalSelesai').val(data.tanggal_selesai);
                $('#editStatus').val(data.status);
                
                // Show current poster if exists
                if (data.poster) {
                    $('#edit_poster_preview').html('<img src="{{ asset("storage") }}/' + data.poster + '" class="img-thumbnail" style="max-width: 200px;">');
                } else {
                    $('#edit_poster_preview').html('');
                }
                
                $('#modal-edit').modal('show');
            }
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseText);
        }
    });
}

function showDetailModal(id) {
    $.ajax({
        url: "{{ url('admin/jobfair') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                var html = '<div class="row">';
                html += '<div class="col-md-6">';
                html += '<table class="table table-bordered">';
                html += '<tr><th width="40%">Nama Job Fair</th><td>' + (data.nama_job_fair || '-') + '</td></tr>';
                html += '<tr><th>Slug</th><td>' + (data.slug || '-') + '</td></tr>';
                html += '<tr><th>Jenis Penyelenggara</th><td>' + (data.jenis_penyelenggara == 0 ? 'Pemerintah' : 'Swasta') + '</td></tr>';
                html += '<tr><th>Penyelenggara</th><td>' + (data.penyelenggara || '-') + '</td></tr>';
                html += '<tr><th>User Penyelenggara</th><td>' + (data.penyelenggara_user ? data.penyelenggara_user.name : '-') + '</td></tr>';
                html += '<tr><th>Tipe Job Fair</th><td>' + (data.tipe_job_fair == 0 ? 'Online' : 'Offline') + '</td></tr>';
                html += '<tr><th>Kota</th><td>' + (data.kota || '-') + '</td></tr>';
                html += '<tr><th>Lokasi</th><td>' + (data.lokasi_penyelenggaraan || '-') + '</td></tr>';
                html += '<tr><th>Tipe Partnership</th><td>' + (data.tipe_partnership == 0 ? 'Tertutup' : 'Open') + '</td></tr>';
                html += '</table></div>';
                
                html += '<div class="col-md-6">';
                html += '<table class="table table-bordered">';
                html += '<tr><th width="40%">Buka Pendaftaran</th><td>' + (data.tanggal_open_pendaftaran_tenant || '-') + '</td></tr>';
                html += '<tr><th>Tutup Pendaftaran</th><td>' + (data.tanggal_close_pendaftaran_tenant || '-') + '</td></tr>';
                html += '<tr><th>Tanggal Mulai</th><td>' + (data.tanggal_mulai || '-') + '</td></tr>';
                html += '<tr><th>Tanggal Selesai</th><td>' + (data.tanggal_selesai || '-') + '</td></tr>';
                html += '<tr><th>Status Verifikasi</th><td>' + (data.status_verifikasi == 1 ? '<span class="badge bg-success">Terverifikasi</span>' : '<span class="badge bg-warning">Belum Verifikasi</span>') + '</td></tr>';
                html += '<tr><th>Verifikator</th><td>' + (data.verifikator ? data.verifikator.name : '-') + '</td></tr>';
                html += '<tr><th>Status</th><td>' + (data.status == 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Tidak Aktif</span>') + '</td></tr>';
                html += '<tr><th>Dibuat Oleh</th><td>' + (data.creator ? data.creator.name : '-') + '</td></tr>';
                html += '<tr><th>Poster</th><td>' + (data.poster ? '<img src="{{ asset("storage") }}/' + data.poster + '" class="img-thumbnail" style="max-width: 150px;">' : '-') + '</td></tr>';
                html += '</table></div>';
                
                html += '<div class="col-12">';
                html += '<table class="table table-bordered">';
                html += '<tr><th width="20%">Deskripsi</th><td>' + (data.deskripsi || '-') + '</td></tr>';
                html += '</table></div>';
                html += '</div>';
                
                $('#detailContent').html(html);
                $('#modal-detail').modal('show');
            }
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseText);
        }
    });
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this job fair?")) {
        deleteJobFair(id);
    }
}

function deleteJobFair(id) {
    $.ajax({
        url: "{{ url('admin/jobfair') }}/" + id,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#simpletable').DataTable().ajax.reload();
                alert(response.message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseText);
        }
    });
}
</script>
@endpush