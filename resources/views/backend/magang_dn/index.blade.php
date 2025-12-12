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
                                    <h5 class="m-b-10">Magang Pemerintah Management</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->

                <!-- Alert Messages -->
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

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Validation Error!</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- [ Main Content ] start -->
                <div class="row">
                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                    </div>


                                    @if (Auth::user()->roles[0]['name'] != 'penyedia-kerja' && Auth::user()->roles[0]['name'] != 'pencari-kerja')
                                        <div class="col-sm-6 text-end">
                                            <button class="btn btn-success btn-sm btn-round has-ripple"
                                                data-bs-toggle="modal" data-bs-target="#modal-report"><i
                                                    class="feather icon-plus"></i> Add Data</button>
                                        </div>
                                    @endif



                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Magang</th>
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
                    <form action="{{ route('magang_dn.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Magang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-sm-6">
                                    <div class="form-group" hidden>
                                        <label for="jenis_penyelenggara">Jenis Penyelenggara <span
                                                class="text-danger">*</span></label>
                                        {{-- <select class="form-control" id="jenis_penyelenggara" name="jenis_penyelenggara" required>
                                        <option value="">Pilih Jenis Penyelenggara</option>
                                        <option value="0">Pemerintah</option>
                                        <option value="1">Swasta</option>
                                    </select> --}}
                                        <input type="text" class="form-control" id="jenis_penyelenggara"
                                            name="jenis_penyelenggara" value="0">
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="nama_magang">Nama Magang <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_magang" name="nama_magang"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="penyelenggara">Penyelenggara</label>
                                        <input type="text" class="form-control" id="penyelenggara" name="penyelenggara">
                                    </div>

                                    <div class="form-group">
                                        <label for="tipe_magang">Tipe Magang <span class="text-danger">*</span></label>
                                        <select class="form-control" id="tipe_magang" name="tipe_magang" required>
                                            <option value="">Pilih Tipe Magang</option>
                                            <option value="0">Online</option>
                                            <option value="1">Offline</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="kota">Kabupaten/Kota <span class="text-danger">*</span></label>
                                        <select class="form-control" id="kota" name="kota" required>
                                            <option value="">Pilih Kabupaten/Kota</option>
                                            @foreach (getKabkota() as $kabkota)
                                                <option value="{{ $kabkota->id }}">{{ $kabkota->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="lokasi_penyelenggaraan">Lokasi
                                            Penyelenggaraan</label>
                                        <input type="text" class="form-control" id="lokasi_penyelenggaraan"
                                            name="lokasi_penyelenggaraan">
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="poster">Poster</label>
                                        <input type="file" class="form-control" id="poster" name="poster"
                                            accept="image/*">
                                        <small class="text-muted">Max 2MB (jpg, jpeg, png, gif)</small>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="tipe_partnership">Tipe Partnership <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="tipe_partnership" name="tipe_partnership"
                                            required>
                                            <option value="">Pilih Tipe Partnership</option>
                                            <option value="0">Tertutup</option>
                                            <option value="1">Open (Perusahaan bisa daftar)</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="tanggal_open_pendaftaran_tenant">Tanggal Buka
                                            Pendaftaran Tenant</label>
                                        <input type="date" class="form-control" id="tanggal_open_pendaftaran_tenant"
                                            name="tanggal_open_pendaftaran_tenant">
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="tanggal_close_pendaftaran_tenant">Tanggal Tutup
                                            Pendaftaran Tenant</label>
                                        <input type="date" class="form-control" id="tanggal_close_pendaftaran_tenant"
                                            name="tanggal_close_pendaftaran_tenant">
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="tanggal_mulai">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="tanggal_mulai"
                                            name="tanggal_mulai">
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="tanggal_selesai">Tanggal Selesai</label>
                                        <input type="date" class="form-control" id="tanggal_selesai"
                                            name="tanggal_selesai">
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="floating-label" for="deskripsi">Deskripsi</label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="editForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Magang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editMagangId" name="id">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-sm-6">
                                    <div class="form-group" hidden>
                                        <label for="editJenisPenyelenggara">Jenis Penyelenggara <span
                                                class="text-danger">*</span></label>
                                        {{-- <select class="form-control" id="editJenisPenyelenggara" name="jenis_penyelenggara" required>
                                        <option value="">Pilih Jenis Penyelenggara</option>
                                        <option value="0">Pemerintah</option>
                                        <option value="1">Swasta</option>
                                    </select> --}}
                                        <input type="text" class="form-control" id="editJenisPenyelenggara"
                                            name="jenis_penyelenggara" value="0">
                                    </div>

                                    <div class="form-group">
                                        <label for="editNamaMagang">Nama Magang <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNamaMagang"
                                            name="nama_magang" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="editPenyelenggara">Penyelenggara</label>
                                        <input type="text" class="form-control" id="editPenyelenggara"
                                            name="penyelenggara">
                                    </div>

                                    <div class="form-group">
                                        <label for="editTipeMagang">Tipe Magang <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editTipeMagang" name="tipe_magang" required>
                                            <option value="">Pilih Tipe Magang</option>
                                            <option value="0">Online</option>
                                            <option value="1">Offline</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="editKota">Kabupaten/Kota <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editKota" name="kota" required>
                                            <option value="">Pilih Kabupaten/Kota</option>
                                            @foreach (getKabkota() as $kabkota)
                                                <option value="{{ $kabkota->id }}">{{ $kabkota->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="editLokasiPenyelenggaraan">Lokasi Penyelenggaraan</label>
                                        <input type="text" class="form-control" id="editLokasiPenyelenggaraan"
                                            name="lokasi_penyelenggaraan">
                                    </div>

                                    <div class="form-group">
                                        <label for="editPoster">Poster</label>
                                        <input type="file" class="form-control" id="editPoster" name="poster"
                                            accept="image/*">
                                        <small class="text-muted">Max 2MB (jpg, jpeg, png, gif)</small>
                                        <div id="edit_poster_preview" class="mt-2"></div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="editTipePartnership">Tipe Partnership <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="editTipePartnership" name="tipe_partnership"
                                            required>
                                            <option value="">Pilih Tipe Partnership</option>
                                            <option value="0">Tertutup</option>
                                            <option value="1">Open (Perusahaan bisa daftar)</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="editTanggalOpenPendaftaranTenant">Tanggal Buka Pendaftaran
                                            Tenant</label>
                                        <input type="date" class="form-control" id="editTanggalOpenPendaftaranTenant"
                                            name="tanggal_open_pendaftaran_tenant">
                                    </div>

                                    <div class="form-group">
                                        <label for="editTanggalClosePendaftaranTenant">Tanggal Tutup Pendaftaran
                                            Tenant</label>
                                        <input type="date" class="form-control" id="editTanggalClosePendaftaranTenant"
                                            name="tanggal_close_pendaftaran_tenant">
                                    </div>

                                    <div class="form-group">
                                        <label for="editTanggalMulai">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="editTanggalMulai"
                                            name="tanggal_mulai">
                                    </div>

                                    <div class="form-group">
                                        <label for="editTanggalSelesai">Tanggal Selesai</label>
                                        <input type="date" class="form-control" id="editTanggalSelesai"
                                            name="tanggal_selesai">
                                    </div>

                                    <div class="form-group">
                                        <label for="editStatus">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editStatus" name="status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="editDeskripsi">Deskripsi</label>
                                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Magang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="detailContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </body>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // DataTable
            $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('magang_dn.index') }}',
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.log('DataTable Error:', xhr.responseText);
                        alert('Error loading data: ' + xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_magang',
                        name: 'nama_magang'
                    },
                    {
                        data: 'penyelenggara',
                        name: 'penyelenggara'
                    },
                    {
                        data: 'jenis_penyelenggara_text',
                        name: 'jenis_penyelenggara'
                    },
                    {
                        data: 'tipe_magang_text',
                        name: 'tipe_magang'
                    },
                    {
                        data: 'tipe_partnership_text',
                        name: 'tipe_partnership'
                    },
                    {
                        data: 'tanggal_mulai',
                        name: 'tanggal_mulai'
                    },
                    {
                        data: 'tanggal_selesai',
                        name: 'tanggal_selesai'
                    },
                    {
                        data: 'status_verifikasi_badge',
                        name: 'status_verifikasi'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'options',
                        name: 'options',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Auto hide alert after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        // Helper function untuk format tanggal dari ISO ke YYYY-MM-DD
        function formatDateForInput(dateString) {
            if (!dateString) return '';
            // Ambil hanya bagian tanggal (YYYY-MM-DD) dari ISO string
            // Format dari database: 2025-10-27T16:00:00.000000Z
            // Format yang dibutuhkan input date: 2025-10-27
            return dateString.split('T')[0];
        }

        function showEditModal(id) {
            var showUrl = "{{ route('magang_dn.show', ':id') }}".replace(':id', id);
            var updateUrl = "{{ route('magang_dn.update', ':id') }}";

            $.ajax({
                url: showUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;

                        // Set form action
                        $('#editForm').attr('action', updateUrl.replace(':id', data.id));

                        // Fill form
                        $('#editMagangId').val(data.id);
                        $('#editJenisPenyelenggara').val(data.jenis_penyelenggara);
                        $('#editNamaMagang').val(data.nama_magang);
                        $('#editPenyelenggara').val(data.penyelenggara);
                        $('#editDeskripsi').val(data.deskripsi);
                        $('#editTipeMagang').val(data.tipe_magang);
                        $('#editKota').val(data.kota);
                        $('#editLokasiPenyelenggaraan').val(data.lokasi_penyelenggaraan);

                        // Format tanggal dari ISO ke YYYY-MM-DD untuk input date
                        $('#editTanggalOpenPendaftaranTenant').val(formatDateForInput(data
                            .tanggal_open_pendaftaran_tenant));
                        $('#editTanggalClosePendaftaranTenant').val(formatDateForInput(data
                            .tanggal_close_pendaftaran_tenant));
                        $('#editTanggalMulai').val(formatDateForInput(data.tanggal_mulai));
                        $('#editTanggalSelesai').val(formatDateForInput(data.tanggal_selesai));

                        $('#editTipePartnership').val(data.tipe_partnership);
                        $('#editStatus').val(data.status);

                        // Show current poster if exists
                        if (data.poster) {
                            $('#edit_poster_preview').html('<img src="{{ asset('storage') }}/' + data.poster +
                                '" class="img-thumbnail" style="max-width: 200px;">');
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
            var showUrl = "{{ route('magang_dn.show', ':id') }}".replace(':id', id);

            $.ajax({
                url: showUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        var html = '<div class="row">';
                        html += '<div class="col-md-6">';
                        html += '<table class="table table-bordered">';
                        html += '<tr><th width="40%">Nama Magang</th><td>' + (data.nama_magang || '-') +
                            '</td></tr>';
                        html += '<tr><th>Slug</th><td>' + (data.slug || '-') + '</td></tr>';
                        html += '<tr><th>Jenis Penyelenggara</th><td>' + (data.jenis_penyelenggara == 0 ?
                            'Pemerintah' : 'Swasta') + '</td></tr>';
                        html += '<tr><th>Penyelenggara</th><td>' + (data.penyelenggara || '-') + '</td></tr>';
                        html += '<tr><th>Tipe Magang</th><td>' + (data.tipe_magang == 0 ? 'Online' :
                            'Offline') + '</td></tr>';
                        html += '<tr><th>Kota</th><td>' + (data.kota || '-') + '</td></tr>';
                        html += '<tr><th>Lokasi</th><td>' + (data.lokasi_penyelenggaraan || '-') + '</td></tr>';
                        html += '<tr><th>Tipe Partnership</th><td>' + (data.tipe_partnership == 0 ? 'Tertutup' :
                            'Open') + '</td></tr>';
                        html += '</table></div>';

                        html += '<div class="col-md-6">';
                        html += '<table class="table table-bordered">';
                        html += '<tr><th width="40%">Buka Pendaftaran</th><td>' + (formatDateForInput(data
                            .tanggal_open_pendaftaran_tenant) || '-') + '</td></tr>';
                        html += '<tr><th>Tutup Pendaftaran</th><td>' + (formatDateForInput(data
                            .tanggal_close_pendaftaran_tenant) || '-') + '</td></tr>';
                        html += '<tr><th>Tanggal Mulai</th><td>' + (formatDateForInput(data.tanggal_mulai) ||
                            '-') + '</td></tr>';
                        html += '<tr><th>Tanggal Selesai</th><td>' + (formatDateForInput(data
                            .tanggal_selesai) || '-') + '</td></tr>';
                        html += '<tr><th>Status Verifikasi</th><td>' + (data.status_verifikasi == 1 ?
                            '<span class="badge bg-success">Terverifikasi</span>' :
                            '<span class="badge bg-warning">Belum Verifikasi</span>') + '</td></tr>';
                        html += '<tr><th>Verifikator</th><td>' + (data.verifikator ? data.verifikator.name :
                            '-') + '</td></tr>';
                        html += '<tr><th>Status</th><td>' + (data.status == 1 ?
                            '<span class="badge bg-success">Aktif</span>' :
                            '<span class="badge bg-secondary">Tidak Aktif</span>') + '</td></tr>';
                        html += '<tr><th>Dibuat Oleh</th><td>' + (data.creator ? data.creator.name : '-') +
                            '</td></tr>';
                        html += '<tr><th>Poster</th><td>' + (data.poster ?
                            '<img src="{{ asset('storage') }}/' + data.poster +
                            '" class="img-thumbnail" style="max-width: 150px;">' : '-') + '</td></tr>';
                        html += '</table></div>';

                        html += '<div class="col-12">';
                        html += '<table class="table table-bordered">';
                        html += '<tr><th width="20%">Deskripsi</th><td>' + (data.deskripsi || '-') +
                            '</td></tr>';
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
    </script>
@endpush
