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
                                {{-- <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="#!">Hospital</a></li>
                                    <li class="breadcrumb-item"><a href="#!">Department</a></li>
                                </ul> --}}
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
                                        <h5>Data</h5>
                                    </div>
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
                                        <label for="jab">Jabatan</label>
                                        <select class="form-control" name="jabatan_id" id="jabatan_id" required>
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
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="jab">Tanggal Mulai</label>
                                            <input type="date" class="form-control" name="tanggal_start"
                                                id="tanggal_start">
                                        </div>
                                        <div class="col-6">
                                            <label for="jab">Tanggal Selesai</label>
                                            <input type="date" class="form-control" name="tanggal_end" id="tanggal_end">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{-- <label class="floating-label" for="jdllow">Judul Lowongan</label> --}}
                                        <textarea class="form-control" id="judul_lowongan" name="judul_lowongan" rows="3" placeholder="Judul Lowongan"></textarea>
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
                                        <label for="pendid">Pendidikan</label>
                                        <select class="form-control" name="pendidikan_id" id="pendidikan_id" required>
                                            <option value="">Pilih Pendidikan</option>
                                            @foreach ($pendidikans as $pend)
                                                <option value="{{ $pend->id }}">{{ $pend->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="jur">Jurusan</label>
                                        <select class="form-control" name="jurusan_id" id="jurusan_id" required>
                                            <option selected disabled>Pilih Jurusan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="stskawin">Status Perkawinan</label>
                                        <select class="form-control" id="status_perkawinan_id"
                                            name="status_perkawinan_id" required>
                                            <option selected disabled>Pilih Status</option>
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
                        <h5 class="modal-title" id="modalEditLabel">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAdminForm">
                            <input type="hidden" id="editId">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="pertanyaan">Pertanyaan</label>
                                    <textarea class="form-control" id="editPertanyaan" name="pertanyaan" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="jawaban">Jawaban</label>
                                    <textarea class="form-control" id="editJawaban" name="jawaban" rows="3"></textarea>
                                </div>
                                <button class="btn btn-primary" onclick="updateFaq()" type="button">Submit</button>

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
                ajax: '{{ route('lowongan.index') }}',
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
                    {
                        data: 'deskripsi'
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
                    url: '{{ route('lowongan.add') }}', // Ganti dengan rute yang sesuai
                    data: {
                        jabatan_id: $('#jabatan_id').val(),
                        sektor_id: $('#sektor_id').val(),
                        tanggal_start: $('#tanggal_start').val(),
                        tanggal_end: $('#tanggal_end').val(),
                        judul_lowongan: $('#judul_lowongan').val(),
                        kabkota_id: $('#kabkota_id').val(),
                        lokasi_penempatan_text: $('#lokasi_penempatan_text').val(),
                        jumlah_pria: $('#jumlah_pria').val(),
                        jumlah_wanita: $('#jumlah_wanita').val(),
                        deskripsi: $('#deskripsi').val(),
                        pendidikan_id: $('#pendidikan_id').val(),
                        jurusan_id: $('#jurusan_id').val(),
                        marital_id: $('#status_perkawinan_id').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Berhasil menambahkan data');
                            $('#modal-report').modal('hide');
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
        });
    </script>

    <script>
        function showEditModal(id) {
            var detailUrl = "{{ route('faq.detail', ':id') }}".replace(':id', id);
            $.ajax({
                url: detailUrl,
                type: 'GET',
                success: function(response) {
                    let dt = response.data;

                    // Isi data modal dengan data yang diperoleh
                    $('#editId').val(dt.id);
                    $('#editPertanyaan').val(dt.name);
                    $('#editJawaban').val(dt.description);

                    // Tampilkan modal edit
                    $('#modal-edit').modal('show');
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    </script>

    <script>
        function updateFaq() {
            // Get data from the modal form
            var id = $('#editId').val();
            var name = $('#editPertanyaan').val();
            var description = $('#editJawaban').val();

            // Send the data to the update route
            $.ajax({
                url: "{{ route('faq.update', ':id') }}".replace(':id', id),
                type: 'PUT',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token for security
                    name: name,
                    description: description
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
        }
    </script>

    <script>
        function confirmDelete(id) {
            // Konfirmasi penghapusan
            var deleteUrl = "{{ route('faq.softdelete', ':id') }}".replace(':id', id);
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
        });
    </script>
@endpush
