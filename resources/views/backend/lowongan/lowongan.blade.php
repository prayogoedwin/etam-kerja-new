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
                                <h5 class="m-b-10">Lowongan Job Fair - {{ $jobFair->nama_job_fair }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('jobfair.index') }}">Job Fair</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('jobfair.perusahaan', $jobfairId) }}">Perusahaan</a></li>
                                <li class="breadcrumb-item active">Lowongan - {{ $perusahaan->name }}</li>
                            </ul>
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
                                    <h5>Lowongan Kerja - {{ $perusahaan->name }}</h5>
                                    <p class="text-muted mb-0">Job Fair: {{ $jobFair->nama_job_fair }}</p>
                                </div>
                                <div class="col-sm-6 text-end">
                                    <a href="{{ route('jobfair.perusahaan', $jobfairId) }}" class="btn btn-secondary btn-sm btn-round me-2">
                                        <i class="feather icon-arrow-left"></i> Kembali
                                    </a>
                                    <button id="btnAdd" class="btn btn-success btn-sm btn-round has-ripple"
                                        data-bs-toggle="modal" data-bs-target="#modal-report"><i
                                            class="feather icon-plus"></i> Tambah Lowongan
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table id="simpletable" class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Lowongan</th>
                                            <th>Jabatan</th>
                                            <th>Sektor</th>
                                            <th>Lokasi</th>
                                            <th>Tgl Mulai</th>
                                            <th>Tgl Selesai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
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

    <!-- Modal Tambah -->
    <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Lowongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tambahForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Disediakan untuk</label>
                                    <select class="form-select" name="is_lowongan_disabilitas"
                                        id="is_lowongan_disabilitas" required style="width: 100%;">
                                        <option value="0">Umum</option>
                                        <option value="1">Disabilitas</option>
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="jabatan_id" id="jabatan_id" required
                                        style="width: 100%;">
                                        <option value="">Pilih Jabatan</option>
                                        @foreach ($jabatans as $jab)
                                            <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Sektor <span class="text-danger">*</span></label>
                                    <select class="form-control" name="sektor_id" id="sektor_id" required>
                                        <option value="">Pilih Sektor</option>
                                        @foreach ($sektors as $sekt)
                                            <option value="{{ $sekt->id }}">{{ $sekt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="pendid">Pendidikan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="pendidikan_id" id="pendidikan_id" required>
                                        <option value="">Pilih Pendidikan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Jurusan</label>
                                    <select class="form-control" name="jurusan_id" id="jurusan_id" style="width: 100%;">
                                        <option value="">Pilih Jurusan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="jab">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="tanggal_start"
                                            id="tanggal_start" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="jab">Tanggal Selesai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="tanggal_end" id="tanggal_end" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="judul_lowongan">Judul Lowongan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="judul_lowongan" name="judul_lowongan" rows="3" placeholder="Judul Lowongan" required></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Kabupaten / Kota <span class="text-danger">*</span></label>
                                    <select class="form-control" name="kabkota_id" id="kabkota_id" required>
                                        <option value="">Pilih Kabkota</option>
                                        @foreach ($kabkotas as $kabkot)
                                            <option value="{{ $kabkot->id }}">{{ $kabkot->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="lokasi_penempatan_text">Lokasi Penempatan</label>
                                    <input class="form-control" type="text" id="lokasi_penempatan_text"
                                        name="lokasi_penempatan_text" placeholder="Lokasi Penempatan">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="kisaran_gaji">Kisaran Gaji Awal</label>
                                            <input class="form-control" type="number" id="kisaran_gaji"
                                                name="kisaran_gaji" placeholder="Kisaran Gaji Awal">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="kisaran_gaji_akhir">Kisaran Gaji Akhir</label>
                                            <input class="form-control" type="number" id="kisaran_gaji_akhir"
                                                name="kisaran_gaji_akhir" placeholder="Kisaran Gaji Akhir">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="jumlah_pria">Jumlah Pria</label>
                                        <input type="number" class="form-control" name="jumlah_pria"
                                            id="jumlah_pria" min="0" value="0">
                                    </div>
                                    <div class="col-6">
                                        <label for="jumlah_wanita">Jumlah Wanita</label>
                                        <input type="number" class="form-control" name="jumlah_wanita"
                                            id="jumlah_wanita" min="0" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi Pekerjaan</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi Pekerjaan"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="stskawin">Status Perkawinan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status_perkawinan_id"
                                        name="marital_id" required>
                                        <option value="">Pilih Status</option>
                                        @foreach (getMarital() as $marit)
                                            <option value="{{ $marit->id }}">{{ $marit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="float-end btn btn-primary">Simpan</button>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Lowongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editId">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Disediakan untuk</label>
                                    <select class="form-select" name="is_lowongan_disabilitas_edit"
                                        id="is_lowongan_disabilitas_edit" required style="width: 100%;">
                                        <option value="0">Umum</option>
                                        <option value="1">Disabilitas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="jabatan_id_edit" id="jabatan_id_edit" required
                                        style="width: 100%;">
                                        <option value="">Pilih Jabatan</option>
                                        @foreach (getJabatan() as $jab)
                                            <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Sektor <span class="text-danger">*</span></label>
                                    <select class="form-control" name="sektor_id_edit" id="sektor_id_edit" required>
                                        <option value="">Pilih Sektor</option>
                                        @foreach ($sektors as $sekt)
                                            <option value="{{ $sekt->id }}">{{ $sekt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="pendid">Pendidikan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="pendidikan_id_edit" id="pendidikan_id_edit"
                                        required>
                                        <option value="">Pilih Pendidikan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Jurusan</label>
                                    <select class="form-control" name="jurusan_id_edit" id="jurusan_id_edit"
                                        style="width: 100%;">
                                        <option value="">Pilih Jurusan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="jab">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="tanggal_start_edit"
                                            id="tanggal_start_edit" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="jab">Tanggal Selesai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="tanggal_end_edit"
                                            id="tanggal_end_edit" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="judul_lowongan_edit">Judul Lowongan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="judul_lowongan_edit" name="judul_lowongan_edit" rows="3"
                                        placeholder="Judul Lowongan" required></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Kabupaten / Kota <span class="text-danger">*</span></label>
                                    <select class="form-control" name="kabkota_id_edit" id="kabkota_id_edit"
                                        required>
                                        <option value="">Pilih Kabkota</option>
                                        @foreach ($kabkotas as $kabkot)
                                            <option value="{{ $kabkot->id }}">{{ $kabkot->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="lokasi_penempatan_text_edit">Lokasi Penempatan</label>
                                    <input class="form-control" type="text" id="lokasi_penempatan_text_edit"
                                        name="lokasi_penempatan_text_edit" placeholder="Lokasi Penempatan">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="kisaran_gaji_edit">Kisaran Gaji Awal</label>
                                            <input class="form-control" type="number" id="kisaran_gaji_edit"
                                                name="kisaran_gaji_edit" placeholder="Kisaran Gaji Awal">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="kisaran_gaji_akhir_edit">Kisaran Gaji Akhir</label>
                                            <input class="form-control" type="number" id="kisaran_gaji_akhir_edit"
                                                name="kisaran_gaji_akhir_edit" placeholder="Kisaran Gaji Akhir">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="jumlah_pria_edit">Jumlah Pria</label>
                                        <input type="number" class="form-control" name="jumlah_pria_edit"
                                            id="jumlah_pria_edit" min="0">
                                    </div>
                                    <div class="col-6">
                                        <label for="jumlah_wanita_edit">Jumlah Wanita</label>
                                        <input type="number" class="form-control" name="jumlah_wanita_edit"
                                            id="jumlah_wanita_edit" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="deskripsi_edit">Deskripsi Pekerjaan</label>
                                    <textarea class="form-control" id="deskripsi_edit" name="deskripsi_edit" rows="3"
                                        placeholder="Deskripsi Pekerjaan"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="stskawin">Status Perkawinan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status_perkawinan_id_edit"
                                        name="marital_id_edit" required>
                                        <option value="">Pilih Status</option>
                                        @foreach (getMarital() as $marit)
                                            <option value="{{ $marit->id }}">{{ $marit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="float-end btn btn-warning">Update</button>
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
    const jobfairId = {{ $jobfairId }};
    const userId = {{ $userId }};

    // Load Pendidikan on Add Button Click
    $("#btnAdd").click(function() {
        $.ajax({
            url: '{{ route("jobfair.get-all-pendidikan") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let $pendidikanSelect = $('#pendidikan_id');
                $pendidikanSelect.empty();
                $pendidikanSelect.append('<option value="">-- Pilih Pendidikan --</option>');

                $.each(data, function(index, item) {
                    $pendidikanSelect.append(
                        `<option value="${item.id}">${item.name}</option>`);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    });

    // DataTable
    $(document).ready(function() {
        $('#simpletable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("jobfair.lowongan", [$jobfairId, $userId]) }}',
            autoWidth: false,
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'judul_lowongan',
                    render: function(data, type, row) {
                        return data && data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                },
                {
                    data: 'jabatan_nama'
                },
                {
                    data: 'sektor_nama'
                },
                {
                    data: 'lokasi'
                },
                {
                    data: 'tanggal_start'
                },
                {
                    data: 'tanggal_end'
                },
                {
                    data: 'status_badge'
                },
                {
                    data: 'options',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [{
                targets: [1, 7, 8],
                className: 'text-center'
            }]
        });
    });

    // Submit Tambah Form
    $('#tambahForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: '{{ route("jobfair.lowongan.store", [$jobfairId, $userId]) }}',
            data: $(this).serialize(),
            success: function(response) {
                alert('Berhasil menambahkan lowongan');
                $('#modal-report').modal('hide');
                $('#tambahForm').trigger("reset");
                location.reload();
            },
            error: function(xhr, status, error) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessages = '';
                    $.each(errors, function(key, value) {
                        $.each(value, function(index, errorMessage) {
                            errorMessages += errorMessage + '\n';
                        });
                    });
                    alert('Terjadi kesalahan:\n' + errorMessages);
                } else {
                    alert('Gagal menambahkan lowongan: ' + xhr.responseText);
                }
            }
        });
    });

    // Submit Edit Form
    $('#editForm').submit(function(e) {
        e.preventDefault();

        var id = $('#editId').val();
        var formData = {
            _token: "{{ csrf_token() }}",
            _method: 'PUT',
            is_lowongan_disabilitas: $('#is_lowongan_disabilitas_edit').val(),
            jabatan_id: $('#jabatan_id_edit').val(),
            sektor_id: $('#sektor_id_edit').val(),
            pendidikan_id: $('#pendidikan_id_edit').val(),
            jurusan_id: $('#jurusan_id_edit').val(),
            tanggal_start: $('#tanggal_start_edit').val(),
            tanggal_end: $('#tanggal_end_edit').val(),
            judul_lowongan: $('#judul_lowongan_edit').val(),
            kabkota_id: $('#kabkota_id_edit').val(),
            lokasi_penempatan_text: $('#lokasi_penempatan_text_edit').val(),
            kisaran_gaji: $('#kisaran_gaji_edit').val(),
            kisaran_gaji_akhir: $('#kisaran_gaji_akhir_edit').val(),
            jumlah_pria: $('#jumlah_pria_edit').val(),
            jumlah_wanita: $('#jumlah_wanita_edit').val(),
            deskripsi: $('#deskripsi_edit').val(),
            marital_id: $('#status_perkawinan_id_edit').val()
        };

        $.ajax({
            url: `/jobfair/${jobfairId}/perusahaan/${userId}/lowongan/${id}`,
            type: 'PUT',
            data: formData,
            success: function(response) {
                alert('Lowongan berhasil diupdate');
                $('#modal-edit').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    });

    // Show Edit Modal
    function showEditLowonganModal(id) {
        // Load pendidikan first
        $.ajax({
            url: '{{ route("jobfair.get-all-pendidikan") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let $pendidikanSelect = $('#pendidikan_id_edit');
                $pendidikanSelect.empty();
                $pendidikanSelect.append('<option value="">-- Pilih Pendidikan --</option>');

                $.each(data, function(index, item) {
                    $pendidikanSelect.append(
                        `<option value="${item.id}">${item.name}</option>`);
                });
            }
        });

        // Load lowongan detail
        $.ajax({
            url: `/jobfair/${jobfairId}/perusahaan/${userId}/lowongan/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let dt = response.data;

                    $('#editId').val(dt.id);
                    $('#is_lowongan_disabilitas_edit').val(dt.is_lowongan_disabilitas).trigger('change');
                    $('#jabatan_id_edit').val(dt.jabatan_id).trigger('change');
                    $('#sektor_id_edit').val(dt.sektor_id).trigger('change');
                    $('#pendidikan_id_edit').val(dt.pendidikan_id).trigger('change');
                    $('#tanggal_start_edit').val(dt.tanggal_start);
                    $('#tanggal_end_edit').val(dt.tanggal_end);
                    $('#judul_lowongan_edit').val(dt.judul_lowongan);
                    $('#kabkota_id_edit').val(dt.kabkota_id).trigger('change');
                    $('#lokasi_penempatan_text_edit').val(dt.lokasi_penempatan_text);
                    $('#kisaran_gaji_edit').val(dt.kisaran_gaji);
                    $('#kisaran_gaji_akhir_edit').val(dt.kisaran_gaji_akhir);
                    $('#jumlah_pria_edit').val(dt.jumlah_pria);
                    $('#jumlah_wanita_edit').val(dt.jumlah_wanita);
                    $('#deskripsi_edit').val(dt.deskripsi);
                    $('#status_perkawinan_id_edit').val(dt.marital_id).trigger('change');

                    setTimeout(function() {
                        $('#jurusan_id_edit').val(dt.jurusan_id).trigger('change');
                    }, 1000);

                    $('#modal-edit').modal('show');
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    }

    // Pendidikan Change - Load Jurusan
    $('#pendidikan_id').on('change', function() {
        var pendidikanId = this.value;
        if (pendidikanId) {
            $.ajax({
                url: `/get-jurusan-bypendidikan/${pendidikanId}`,
                type: 'GET',
                success: function(response) {
                    $('#jurusan_id').empty();
                    $('#jurusan_id').append('<option value="">Pilih Jurusan</option>');

                    $.each(response, function(index, jurusan) {
                        $('#jurusan_id').append('<option value="' + jurusan.id + '">' +
                            jurusan.nama + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        }
    });

    $('#pendidikan_id_edit').on('change', function() {
        var pendidikanId = this.value;
        if (pendidikanId) {
            $.ajax({
                url: `/get-jurusan-bypendidikan/${pendidikanId}`,
                type: 'GET',
                success: function(response) {
                    $('#jurusan_id_edit').empty();
                    $('#jurusan_id_edit').append('<option value="">Pilih Jurusan</option>');

                    $.each(response, function(index, jurusan) {
                        $('#jurusan_id_edit').append('<option value="' + jurusan.id +
                            '">' +
                            jurusan.nama + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        }
    });

    // Initialize Select2
    $('#modal-report').on('shown.bs.modal', function() {
        $('#jurusan_id').select2({
            placeholder: "Pilih Jurusan",
            allowClear: true,
            dropdownParent: $('#modal-report')
        });
        $("#jabatan_id").select2({
            placeholder: "Pilih Jabatan",
            allowClear: true,
            dropdownParent: $("#modal-report")
        });
    });

    $('#modal-edit').on('shown.bs.modal', function() {
        $('#jurusan_id_edit').select2({
            placeholder: "Pilih Jurusan",
            allowClear: true,
            dropdownParent: $('#modal-edit')
        });
        $("#jabatan_id_edit").select2({
            placeholder: "Pilih Jabatan",
            allowClear: true,
            dropdownParent: $("#modal-edit")
        });
    });
</script>
@endpush