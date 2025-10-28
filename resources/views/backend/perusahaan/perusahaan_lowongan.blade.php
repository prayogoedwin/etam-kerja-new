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
                                    <h5 class="m-b-10">Lowongan Kerja</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('dapur/dashboard') }}"><i
                                                class="feather icon-home"></i> \</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('/dapur/bkks/perusahaan') }}">Perusahaan
                                            \</a></li>
                                    <li class="breadcrumb-item"><a href="#!">Lowongan</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->


                <!-- [ Main Content ] start -->
                <div class="row">

                    <input type="hidden" id="perushId" value="{{ $perusahaan_id }}">
                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                        <h5>Data</h5>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button id="btnAdd" class="btn btn-success btn-sm btn-round has-ripple"
                                            data-bs-toggle="modal" data-bs-target="#modal-report"><i
                                                class="feather icon-plus"></i> Add
                                            Data</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Judul Lowongan</th>
                                                <th>Tgl Mulai</th>
                                                <th>Tgl Selesai</th>
                                                <th>Deskripsi</th>
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

        <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="tambahForm">
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="jab">Jabatan</label>
                                        <select class="form-control" name="jabatan_id" id="jabatan_id" required
                                            style="width: 100%;">
                                            <option value="">Pilih Jabatan</option>
                                            @foreach ($jabatans as $jab)
                                                <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="jab">Sektor</label>
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
                                        <label for="pendid">Pendidikan</label>
                                        <select class="form-control" name="pendidikan_id" id="pendidikan_id" required>
                                            <option value="">Pilih Pendidikan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="">Jurusan</label>
                                        <select class="form-control" name="jurusan_id" id="jurusan_id" style="width: 100%;"
                                            required>
                                            <option value="">Pilih Jurusan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="jab">Tanggal Mulai</label>
                                            <input type="date" class="form-control" name="tanggal_start"
                                                id="tanggal_start">
                                        </div>
                                        <div class="col-6">
                                            <label for="jab">Tanggal Selesai</label>
                                            <input type="date" class="form-control" name="tanggal_end"
                                                id="tanggal_end">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{-- <label class="floating-label" for="jdllow">Judul Lowongan</label> --}}
                                        <textarea class="form-control" id="judul_lowongan" name="judul_lowongan" rows="3"
                                            placeholder="Judul Lowongan"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="jab">Kabupaten / Kota</label>
                                        <select class="form-control" name="kabkota_id" id="kabkota_id" required>
                                            <option value="">Pilih Kabkota</option>
                                            @foreach ($kabkotas as $kabkot)
                                                <option value="{{ $kabkot->id }}">{{ $kabkot->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            {{-- <label class="floating-label" for="pertanyaan">Lokasi Penempatan</label> --}}
                                            <input class="form-control" type="text" id="lokasi_penempatan_text"
                                                name="lokasi_penempatan_text" placeholder="Lokasi Penempatan">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input class="form-control" type="number" id="kisaran_gaji"
                                                    name="kisaran_gaji" placeholder="Kisaran Gaji Awal">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input class="form-control" type="number" id="kisaran_gaji_akhir"
                                                    name="kisaran_gaji_akhir" placeholder="Kisaran Gaji Akhir">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="floating-label" for="jumpri">Jumlah Pria</label>
                                            <input type="number" class="form-control" name="jumlah_pria"
                                                id="jumlah_pria">
                                        </div>
                                        <div class="col-6">
                                            <label class="floating-label" for="jumwat">Jumlah Wanita</label>
                                            <input type="number" class="form-control" name="jumlah_wanita"
                                                id="jumlah_wanita">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{-- <label class="floating-label" for="desk">Deskripsi</label> --}}
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi Pekerjaan"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="stskawin">Status Perkawinan</label>
                                        <select class="form-control" id="status_perkawinan_id"
                                            name="status_perkawinan_id" required>
                                            <option selected>Pilih Status</option>
                                            @foreach ($maritals as $marit)
                                                <option value="{{ $marit->id }}">{{ $marit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-sm-12">

                                    <button class="float-end btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Data Lowongan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
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
                                        <label for="jab">Jabatan</label>
                                        <select class="form-control" name="jabatan_id_edit" id="jabatan_id_edit" required
                                            style="width: 100%;">
                                            <option value="">Pilih Jabatan</option>
                                            @foreach ($jabatans as $jab)
                                                <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="jab">Sektor</label>
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
                                        <label for="pendid">Pendidikan</label>
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
                                            style="width: 100%;" required>
                                            <option value="">Pilih Jurusan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="jab">Tanggal Mulai</label>
                                            <input type="date" class="form-control" name="tanggal_start_edit"
                                                id="tanggal_start_edit">
                                        </div>
                                        <div class="col-6">
                                            <label for="jab">Tanggal Selesai</label>
                                            <input type="date" class="form-control" name="tanggal_end_edit"
                                                id="tanggal_end_edit">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <textarea class="form-control" id="judul_lowongan_edit" name="judul_lowongan_edit" rows="3"
                                            placeholder="Judul Lowongan"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="jab">Kabupaten / Kota</label>
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
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input class="form-control" type="text" id="lokasi_penempatan_text_edit"
                                                name="lokasi_penempatan_text_edit" placeholder="Lokasi Penempatan">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input class="form-control" type="number" id="kisaran_gaji_edit"
                                                    name="kisaran_gaji_edit" placeholder="Kisaran Gaji Awal">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input class="form-control" type="number" id="kisaran_gaji_akhir_edit"
                                                    name="kisaran_gaji_akhir_edit" placeholder="Kisaran Gaji Akhir">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="floating-label" for="jumpri">Jumlah Pria</label>
                                            <input type="number" class="form-control" name="jumlah_pria_edit"
                                                id="jumlah_pria_edit">
                                        </div>
                                        <div class="col-6">
                                            <label class="floating-label" for="jumwat">Jumlah Wanita</label>
                                            <input type="number" class="form-control" name="jumlah_wanita_edit"
                                                id="jumlah_wanita_edit">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <textarea class="form-control" id="deskripsi_edit" name="deskripsi_edit" rows="3"
                                            placeholder="Deskripsi Pekerjaan"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="stskawin">Status Perkawinan</label>
                                        <select class="form-control" id="status_perkawinan_id_edit"
                                            name="status_perkawinan_id_edit" required>
                                            <option selected>Pilih Status</option>
                                            @foreach ($maritals as $marit)
                                                <option value="{{ $marit->id }}">{{ $marit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-sm-12">

                                    <button class="float-end btn btn-warning">Update</button>
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
        $("#btnAdd").click(function() {
            $.ajax({
                url: '{{ route('get-all-pendidikan') }}', // Endpoint Laravel
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Kosongkan dropdown
                    let $pendidikanSelect = $('#pendidikan_id');
                    $pendidikanSelect.empty();
                    $pendidikanSelect.append('<option value="">-- Pilih Pendidikan --</option>');

                    // Looping data dari response dan tambahkan ke select
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
    </script>

    <script>
        $(document).ready(function() {
            var aidi = $('#perushId').val();
            var bUrl = "{{ route('perusahaan.lowongan', ':id') }}".replace(':id', aidi);
            $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: bUrl,
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'judul_lowongan'
                    },
                    {
                        data: 'tanggal_start'
                    },
                    {
                        data: 'tanggal_end'
                    },
                    // {
                    //     data: 'deskripsi'
                    // },
                    {
                        data: 'deskripsi',
                        render: function(data, type, row) {
                            return data.length > 100 ? data.substring(0, 100) + '...' : data;
                        }
                    },
                    {
                        data: 'progres_name'
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#tambahForm').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally

                // Clear previous error messages
                $('#errorMessages').html('').addClass('d-none');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('perusahaanlowongan.add') }}', // Ganti dengan rute yang sesuai
                    data: {
                        is_lowongan_disabilitas: $('#is_lowongan_disabilitas').val(),
                        jabatan_id: $('#jabatan_id').val(),
                        sektor_id: $('#sektor_id').val(),
                        tanggal_start: $('#tanggal_start').val(),
                        tanggal_end: $('#tanggal_end').val(),
                        judul_lowongan: $('#judul_lowongan').val(),
                        kabkota_id: $('#kabkota_id').val(),
                        lokasi_penempatan_text: $('#lokasi_penempatan_text').val(),
                        kisaran_gaji: $('#kisaran_gaji').val(),
                        kisaran_gaji_akhir: $('#kisaran_gaji_akhir').val(),
                        jumlah_pria: $('#jumlah_pria').val(),
                        jumlah_wanita: $('#jumlah_wanita').val(),
                        deskripsi: $('#deskripsi').val(),
                        pendidikan_id: $('#pendidikan_id').val(),
                        jurusan_id: $('#jurusan_id').val(),
                        marital_id: $('#status_perkawinan_id').val(),
                        perusahaan_id: $('#perushId').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Berhasil menambahkan data');
                            $('#modal-report').modal('hide');

                            $('#tambahForm').trigger("reset");

                            location.reload(); // Refresh halaman
                        } else {
                            // If validation errors are found, display them in an alert
                            if (response.errors) {
                                let errorMessages = '';
                                $.each(response.errors, function(key, value) {
                                    $.each(value, function(index, errorMessage) {
                                        errorMessages += errorMessage +
                                            '\n'; // Gabungkan pesan error
                                    });
                                });
                                alert('Terjadi kesalahan:\n' + errorMessages);
                            } else {
                                alert('Gagal menambahkan data');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });


            $('#editForm').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally

                // Clear previous error messages
                $('#errorMessages').html('').addClass('d-none');

                // Get data from the modal form
                var id = $('#editId').val();
                var islowongandisabilitas = $('#is_lowongan_disabilitas_edit').val();
                var jabatan_id = $('#jabatan_id_edit').val();
                var sektor_id = $('#sektor_id_edit').val();
                var pendidikan_id = $('#pendidikan_id_edit').val();
                var jurusan_id = $('#jurusan_id_edit').val();
                var tanggal_start = $('#tanggal_start_edit').val();
                var tanggal_end = $('#tanggal_end_edit').val();
                var judul_lowongan = $('#judul_lowongan_edit').val();
                var kabkota_id = $('#kabkota_id_edit').val();
                var lokasi_penempatan_text = $('#lokasi_penempatan_text_edit').val();
                var kisaran_gaji = $('#kisaran_gaji_edit').val();
                var kisaran_gaji_akhir = $('#kisaran_gaji_akhir_edit').val();
                var jumlah_pria = $('#jumlah_pria_edit').val();
                var jumlah_wanita = $('#jumlah_wanita_edit').val();
                var deskripsi = $('#deskripsi_edit').val();
                var marital_id = $('#status_perkawinan_id_edit').val();

                // Send the data to the update route
                $.ajax({
                    url: "{{ route('lowongan.update', ':id') }}".replace(':id', id),
                    type: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}", // CSRF token for security
                        is_lowongan_disabilitas: islowongandisabilitas,
                        jabatan_id: jabatan_id,
                        sektor_id: sektor_id,
                        pendidikan_id: pendidikan_id,
                        jurusan_id: jurusan_id,
                        tanggal_start: tanggal_start,
                        tanggal_end: tanggal_end,
                        judul_lowongan: judul_lowongan,
                        kabkota_id: kabkota_id,
                        lokasi_penempatan_text: lokasi_penempatan_text,
                        kisaran_gaji: kisaran_gaji,
                        kisaran_gaji_akhir: kisaran_gaji_akhir,
                        jumlah_pria: jumlah_pria,
                        jumlah_wanita: jumlah_wanita,
                        deskripsi: deskripsi,
                        marital_id: marital_id
                    },
                    success: function(response) {
                        if (response.success) {
                            // Display success message
                            alert(response.message);
                            // Close modal
                            $('#modal-edit').modal('hide');
                            // Optionally, reload the table or page to reflect the update
                            location.reload();
                        } else {
                            // Display error message
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        function showData(id) {

            $.ajax({
                url: '{{ route('get-all-pendidikan') }}', // Endpoint Laravel
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Kosongkan dropdown
                    let $pendidikanSelect = $('#pendidikan_id_edit');
                    $pendidikanSelect.empty();
                    $pendidikanSelect.append(
                        '<option value="">-- Pilih Pendidikan --</option>');

                    // Looping data dari response dan tambahkan ke select
                    $.each(data, function(index, item) {
                        $pendidikanSelect.append(
                            `<option value="${item.id}">${item.name}</option>`
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });

            var detailUrl = "{{ route('lowongan.detail', ':id') }}".replace(':id', id);
            $.ajax({
                url: detailUrl,
                type: 'GET',
                success: function(response) {
                    console.log(response);

                    let dt = response.data;
                    var sts = response.success;

                    if (sts == 1) {
                        // Isi data modal dengan data yang diperoleh
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

                        //set timeout
                        setTimeout(function() {
                            $('#jurusan_id_edit').val(dt.jurusan_id).trigger('change');
                        }, 1000);

                        // Tampilkan modal edit
                        $('#modal-edit').modal('show');
                    }


                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    </script>

    <script>
        function confirmDelete(id) {
            // Konfirmasi penghapusan
            var deleteUrl = "{{ route('perusahaanlowongan.softdelete', ':id') }}".replace(':id', id);
            if (confirm("Yakin hapus data?")) {
                // Kirim request ke server untuk menghapus data
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // Menyertakan CSRF token
                    },
                    success: function(response) {
                        // Jika berhasil, reload DataTable
                        alert(response.message); // Menampilkan pesan
                        $('#simpletable').DataTable().ajax.reload(); // Reload data tabel
                    },
                    error: function(xhr, status, error) {
                        // Tampilkan error jika ada masalah
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#pendidikan_id').on('change', function() {
                // console.log(this.value);
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-jurusan-bypendidikan', ':id') }}".replace(':id',
                        kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#jurusan_id').empty();

                        // Tambahkan opsi default
                        $('#jurusan_id').append(
                            '<option selected disabled>Pilih Jurusan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, jurusan) {
                            $('#jurusan_id').append('<option value="' + jurusan.id +
                                '">' +
                                jurusan.nama + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            $('#pendidikan_id_edit').on('change', function() {
                // console.log(this.value);
                console.log('pendidikan aidi edit change');
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-jurusan-bypendidikan', ':id') }}".replace(':id',
                        kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#jurusan_id_edit').empty();

                        // Tambahkan opsi default
                        $('#jurusan_id_edit').append(
                            '<option selected disabled>Pilih Jurusan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, jurusan) {
                            $('#jurusan_id_edit').append('<option value="' + jurusan
                                .id +
                                '">' +
                                jurusan.nama + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            $('#modal-report').on('shown.bs.modal', function() {
                $('#jurusan_id').select2({
                    placeholder: "Pilih Jurusan",
                    allowClear: true,
                    dropdownParent: $('#modal-report')
                });
            });

            $('#modal-edit').on('shown.bs.modal', function() {
                $('#jurusan_id_edit').select2({
                    placeholder: "Pilih Jurusan",
                    allowClear: true,
                    dropdownParent: $('#modal-edit')
                });
            });


            $("#jabatan_id").select2({
                placeholder: "Pilih Jabatan",
                allowClear: true,
                dropdownParent: $("#modal-report")
            });

            $("#jabatan_id_edit").select2({
                placeholder: "Pilih Jabatan",
                allowClear: true,
                dropdownParent: $("#modal-edit")
            });
        });
    </script>
@endpush
