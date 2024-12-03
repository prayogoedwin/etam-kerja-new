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
                                    {{-- <div class="col-sm-6 text-end">
                                        <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal"
                                            data-bs-target="#modal-report"><i class="feather icon-plus"></i> Add
                                            Data</button>
                                    </div> --}}
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

        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAdminForm">
                            <input type="hidden" id="editId">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Jabatan</label>
                                    <select class="form-control" name="jabatan_id" id="jabatan_id" disabled>
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
                                    <select class="form-control" name="sektor_id" id="sektor_id" disabled>
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
                                        <input type="date" class="form-control" name="tanggal_start" id="tanggal_start">
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
                                    <textarea class="form-control" id="judul_lowongan" name="judul_lowongan" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jab">Kabupaten / Kota</label>
                                    <select class="form-control" name="kabkota_id" id="kabkota_id" disabled>
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
                                            name="lokasi_penempatan_text" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="floating-label" for="jumpri">Jumlah Pria</label>
                                        <input type="number" class="form-control" name="jumlah_pria" id="jumlah_pria"
                                            disabled>
                                    </div>
                                    <div class="col-6">
                                        <label class="floating-label" for="jumwat">Jumlah Wanita</label>
                                        <input type="number" class="form-control" name="jumlah_wanita"
                                            id="jumlah_wanita" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{-- <label class="floating-label" for="desk">Deskripsi</label> --}}
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="pendid">Pendidikan</label>
                                    <select class="form-control" name="pendidikan_id" id="pendidikan_id" disabled>
                                        <option value="">Pilih Pendidikan</option>
                                        @foreach ($pendidikans as $pend)
                                            <option value="{{ $pend->id }}">{{ $pend->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jur">Jurusan</label>
                                    <select class="form-control" name="jurusan_id" id="jurusan_id" required>
                                        <option selected disabled>Pilih Jurusan</option>
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="stskawin">Status Perkawinan</label>
                                    <select class="form-control" id="status_perkawinan_id" name="status_perkawinan_id"
                                        disabled>
                                        <option value="">Pilih Status</option>
                                        @foreach ($maritals as $marit)
                                            <option value="{{ $marit->id }}">{{ $marit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <h5>Data Lamaran</h5>
                            <hr>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Status Lamaran</label>
                                    <span id="kontenstatuslamaran"></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" id="keterangan" disabled>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button class="float-end btn btn-warning" onclick="lamarAction()"
                                    type="button">Lamar</button>
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
                ajax: '{{ route('lowongan.pencari.index') }}',
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
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>

    <script>
        function showEditModal(id) {
            var detailUrl = "{{ route('lowongan.pencari.detail', ':id') }}".replace(':id', id);
            $.ajax({
                url: detailUrl,
                type: 'GET',
                success: function(response) {
                    let dt = response.data;

                    // Isi data modal dengan data yang diperoleh
                    $('#editId').val(dt.id);
                    $('#kabkota_id').val(dt.kabkota_id).change();
                    $('#jabatan_id').val(dt.jabatan_id).change();
                    $('#sektor_id').val(dt.sektor_id).change();
                    $('#tanggal_start').val(dt.tanggal_start);
                    $('#tanggal_end').val(dt.tanggal_end);


                    $('#judul_lowongan').val(dt.judul_lowongan);
                    $('#lokasi_penempatan_text').val(dt.lokasi_penempatan_text);
                    $('#jumlah_pria').val(dt.jumlah_pria);
                    $('#jumlah_wanita').val(dt.jumlah_wanita);
                    $('#deskripsi').val(dt.deskripsi);

                    $('#pendidikan_id').val(dt.pendidikan_id);
                    $('#status_perkawinan_id').val(dt.marital_id);


                    // $('#editPertanyaan').val(dt.name);
                    // $('#editJawaban').val(dt.description);
                    var stslmr = '';
                    var cekstslmr = dt.statuslamaran;
                    if (cekstslmr) {
                        if (dt.statuslamaran.progres_id == 1) {
                            stslmr = '<span class="badge rounded-pill bg-warning">Diperiksa</span>';
                        } else if (dt.statuslamaran.progres_id == 2) {
                            stslmr = '<span class="badge rounded-pill bg-warning">Panggilan</span>';
                        } else if (dt.statuslamaran.progres_id == 3) {
                            stslmr = '<span class="badge rounded-pill bg-success">Diterima</span>';
                        } else if (dt.statuslamaran.progres_id == 4) {
                            stslmr = '<span class="badge rounded-pill bg-warning">Belum Ditanggapi</span>';
                        } else if (dt.statuslamaran.progres_id == 5) {
                            stslmr = '<span class="badge rounded-pill bg-warning">Tidak Sesuai Kriteria</span>';
                        }

                        $('#kontenstatuslamaran').html(stslmr);
                        $('#keterangan').val(dt.statuslamaran.keterangan);
                    }

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
        function lamarAction() {
            // Konfirmasi penghapusan
            var id = $('#editId').val();

            var updateUrl = "{{ route('lowongan.pencari.lamar', ':id') }}".replace(':id', id);
            if (confirm("Yakin lamar lowongan ini?")) {
                // Kirim request ke server untuk menghapus data
                $.ajax({
                    url: updateUrl,
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // Menyertakan CSRF token
                    },
                    success: function(response) {
                        // Jika berhasil, reload DataTable
                        alert(response.message); // Menampilkan pesan
                        $('#simpletable').DataTable().ajax.reload(); // Reload data tabel
                        $('#modal-edit').modal('hide');
                        // location.reload(); // Refresh halaman
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
