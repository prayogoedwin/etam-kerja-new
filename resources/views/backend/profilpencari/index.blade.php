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
                                    <h5 class="m-b-10">Profil Pemberi Kerja</h5>
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
                    <div class="col-12">
                        <div class="card">
                            <h5 class="card-header">Profil Data</h5>
                            <div class="card-body">
                                <form id="profilForm">
                                    <input type="hidden"id="editId" value="{{ $profil->id }}">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="posted_by" value="{{ auth()->user()->id }}">
                                    <div class="row">
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Status Akun</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" name="status_id" value="{{ $profil->status_id }}">
                                                @if ($profil->status_id == 1)
                                                    <span class="badge badge-light-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-light-danger">Tidak Aktif</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Status Kerja</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" name="temp_is_diterima"
                                                    value="{{ $profil->is_diterima }}">
                                                <select name="is_diterima" id="is_diterima" class=" form-select">
                                                    <option value="0"
                                                        {{ $profil->is_diterima == 0 ? 'selected' : '' }}>Belum Bekerja
                                                    </option>
                                                    <option value="1"
                                                        {{ $profil->is_diterima == 1 ? 'selected' : '' }}>Sudah Bekerja
                                                        (Sistem)</option>
                                                    <option value="2"
                                                        {{ $profil->is_diterima == 2 ? 'selected' : '' }}>Sudah Bekerja
                                                        (Mandiri)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Foto</label>
                                            </div>
                                            <div class="col-10">
                                                @php
                                                    $xfoto = $profil->foto;
                                                @endphp

                                                @if ($xfoto != null)
                                                    <img alt="Uploaded Image" src="{{ asset('storage/' . $profil->foto) }}"
                                                        style="width: 180px;">
                                                @else
                                                    <small>Belum ada foto, silahkan unggah</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for=""></label>
                                            </div>
                                            <div class="col-10">
                                                <input type="file" class="validation-file" id="foto" name="foto"
                                                    accept="image/png, image/gif, image/jpeg">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">KTP</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="text" class="form-control" name="ktp" id="ktp"
                                                    value="{{ $profil->ktp }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Nama</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="text" class="form-control" name="name" id="name"
                                                    value="{{ $profil->name }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Tempat Lahir</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="text" class="form-control" name="tempat_lahir"
                                                    id="tempat_lahir" value="{{ $profil->tempat_lahir }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Tanggal Lahir</label>
                                            </div>
                                            <div class="col-2">
                                                <input type="date" class="form-control" name="tanggal_lahir"
                                                    id="tanggal_lahir" value="{{ $profil->tanggal_lahir }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Agama</label>
                                            </div>
                                            <div class="col-10">
                                                <select class="form-select" id="id_agama" name="id_agama" required>
                                                    <option value="">Pilih Agama</option>
                                                    @foreach ($agamas as $ag)
                                                        <option value="{{ $ag->id }}"
                                                            {{ $profil->id_agama == $ag->id ? 'selected' : '' }}>
                                                            {{ $ag->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Provinsi</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" id="temp_provinsi_id"
                                                    value="{{ $profil->id_provinsi }}">
                                                <select class="form-select" id="provinsi_id" name="provinsi_id" disabled>
                                                    <option value="">Pilih Provinsi</option>
                                                    @foreach ($provinsis as $prov)
                                                        <option value="{{ $prov->id }}">
                                                            {{ $prov->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Kabupaten / Kota</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" id="temp_kabkota_id"
                                                    value="{{ $profil->id_kota }}">
                                                <select class="form-select" id="kabkota_id" name="kabkota_id" disabled>
                                                    <option value="">Pilih Kabkota</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Kecamatan</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" id="temp_kecamatan_id"
                                                    value="{{ $profil->id_kecamatan }}">
                                                <select class="form-select" id="kecamatan_id" name="kecamatan_id"
                                                    disabled>
                                                    <option value="">Pilih Kecamatan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row" hidden>
                                            <div class="col-2">
                                                <label for="">Kelurahan</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" id="temp_desa_id" value="{{ $profil->id_desa }}">
                                                <select class="form-select" id="desa_id" name="desa_id" disabled>
                                                    <option value="">Pilih Kelurahan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Alamat</label>
                                            </div>
                                            <div class="col-10">
                                                <textarea class="form-control" name="alamat" id="alamat" cols="30" rows="3">{{ $profil->alamat }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Kodepos</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="text" class=" form-control" name="kodepos"
                                                    id="kodepos" value="{{ $profil->kodepos }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Pendidikan</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" id="temp_pendidikan_id"
                                                    value="{{ $profil->id_pendidikan }}">
                                                <select class="form-select" id="id_pendidikan" name="id_pendidikan"
                                                    required>
                                                    <option value="">Pilih Pendidikan</option>
                                                    @foreach ($pendidikans as $ag)
                                                        <option value="{{ $ag->id }}">{{ $ag->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Jurusan</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="hidden" id="temp_jurusan_id"
                                                    value="{{ $profil->id_jurusan }}">
                                                <select class="form-select" id="id_jurusan" name="id_jurusan" required>
                                                    <option value="">Pilih Jurusan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Telpon</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="number" class=" form-control" name="whatsapp"
                                                    id="whatsapp" value="{{ $profil->user->whatsapp }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Sosial Media</label>
                                            </div>
                                            <div class="col-10">
                                                <input type="text" class=" form-control" name="medsos"
                                                    id="medsos" value="{{ $profil->medsos }}">
                                            </div>
                                        </div>
                                        <hr>
                                        <h5>Disabilitas</h5>
                                        <hr>
                                        <div class="form-group row">
                                            <div class="col-2">
                                                <label for="">Disabilitas</label>
                                            </div>
                                            <div class="col-4">
                                                <select name="disabilitas" id="disabilitas" class=" form-select"
                                                    disabled>
                                                    <option value="0"
                                                        {{ $profil->disabilitas == (0 || null) ? 'selected' : '' }}>Tidak
                                                    </option>
                                                    <option value="1"
                                                        {{ $profil->disabilitas == 1 ? 'selected' : '' }}>Ya</option>
                                                </select>
                                            </div>
                                        </div>
                                        @if ($profil->disabilitas == 1)
                                            <div class="form-group row">
                                                <div class="col-2">
                                                    <label for="">Jenis Disabilitas</label>
                                                </div>
                                                <div class="col-4">
                                                    <select name="jenis_disabilitas" id="jenis_disabilitas"
                                                        class=" form-select" disabled>
                                                        @foreach ($disabilitases as $disabb)
                                                            <option value="{{ $disabb->id }}"
                                                                {{ $profil->jenis_disabilitas == $disabb->id ? 'selected' : '' }}>
                                                                {{ $disabb->nama_disabilitas }}</option>
                                                        @endforeach
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-2">
                                                    <label for="">Keterangan</label>
                                                </div>
                                                <div class="col-10">
                                                    <input type="text" class="form-control"
                                                        name="keterangan_disabilitas" id="keterangan_disabilitas"
                                                        value="{{ $profil->keterangan_disabilitas }}" readonly>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-end">
                                        <button class="btn btn-primary" onclick="updateData()"
                                            type="button">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- [ Main Content ] end -->

                {{-- START PENDIDIKAN FORMAL --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="row" style="padding: 8px">
                                <h5 class="card-header col-6">Pendidikan Formal</h5>
                                <h5 class="card-header col-6 text-end"><a href="#!" class="btn btn-sm btn-info"
                                        onclick="addPendidikan()">Tambah</a>
                                </h5>
                            </div>

                            <div class="card-body">
                                <table id="pendidikanTable" class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tingkat Pendidikan</th>
                                            <th>Jurusan</th>
                                            <th>Sekolah/Perguruan Tinggi</th>
                                            <th>Tahun</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END PENDIDIKAN FORMAL --}}

                {{-- START KETRAMPILAN --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="row" style="padding: 8px">
                                <h5 class="card-header col-6">Ketrampilan/Keahlian</h5>
                                <h5 class="card-header col-6 text-end"><a href="#!" class="btn btn-sm btn-info"
                                        onclick="addKeahlian()">Tambah</a>
                                </h5>
                            </div>

                            <div class="card-body">
                                <table id="keahlianTable" class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Keahlian</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END KETRAMPILAN --}}

            </div>
        </div>

        <div class="modal fade" id="modal-addpendidikan" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="tambahFormPendidikan">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Pendidikan</label>
                                    <select class="form-select" name="pendidikan_id_modal" id="pendidikan_id_modal">
                                        <option value="">Pilih Pendidikan</option>
                                        @foreach ($pendidikans as $pendidikan)
                                            <option value="{{ $pendidikan->id }}">{{ $pendidikan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Jurusan</label>
                                    <select class="form-select" name="jurusan_id_modal" id="jurusan_id_modal"
                                        style="width: 100%">
                                        <option value="">Pilih Jurusan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Sekolah</label>
                                    <input type="text" class="form-control" name="instansi" id="instansi">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Tahun Lulus</label>
                                    <select name="tahun" id="tahun" class="form-select">
                                        @php
                                            for ($i = date('Y'); $i >= date('Y') - 70; $i -= 1) {
                                                echo "<option value='$i'> $i </option>";
                                            }
                                        @endphp
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button class="float-end btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-addkeahlian" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="tambahFormKeahlian">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Keahlian</label>
                                    <input type="text" class="form-control" name="keahlian" id="keahlian">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button class="float-end btn btn-primary">Simpan</button>
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
            $("#id_jurusan").select2({
                placeholder: "Pilih Jurusan"
            });

            // Pertama pasang event handler change
            $('#provinsi_id').on('change', function() {
                // alert(this.value); // Menampilkan nilai yang dipilih
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-kabkota-byprov', ':id') }}".replace(':id',
                        kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#kabkota_id').empty();

                        // Tambahkan opsi default
                        $('#kabkota_id').append(
                            '<option selected disabled>Pilih Kabupaten/Kota</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, kabkota) {
                            $('#kabkota_id').append('<option value="' + kabkota.id +
                                '">' +
                                kabkota.name + '</option>');
                        });

                        genKabkota();
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            // Kemudian, set nilai dan trigger event
            var kdprov = $('#temp_provinsi_id').val();
            $('#provinsi_id').val(kdprov).trigger('change'); // Memicu event change

            //onchange pendidikan
            $('#id_pendidikan').on('change', function() {
                // alert(this.value); // Menampilkan nilai yang dipilih
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-jurusan-bypendidikan', ':id') }}".replace(':id',
                        kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#id_jurusan').empty();

                        // Tambahkan opsi default
                        $('#id_jurusan').append(
                            '<option selected disabled>Pilih Jurusan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, jur) {
                            $('#id_jurusan').append('<option value="' + jur.id +
                                '">' +
                                jur.nama + '</option>');
                        });

                        var kdjur = $('#temp_jurusan_id').val();
                        $('#id_jurusan').val(kdjur).trigger('change'); // Memicu event change
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            var kdpendidikan = $('#temp_pendidikan_id').val();
            $('#id_pendidikan').val(kdpendidikan).trigger('change'); // Memicu event change

        });

        function genKabkota() {
            $('#kabkota_id').on('change', function() {
                // alert(this.value); // Menampilkan nilai yang dipilih
                var kabkd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-kecamatan-bykabkota', ':id') }}".replace(':id',
                        kabkd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#kecamatan_id').empty();

                        // Tambahkan opsi default
                        $('#kecamatan_id').append(
                            '<option selected disabled>Pilih Kecamatan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, kabkota) {
                            $('#kecamatan_id').append('<option value="' + kabkota.id +
                                '">' +
                                kabkota.name + '</option>');
                        });

                        genKec();
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            var kdkab = $('#temp_kabkota_id').val();
            $('#kabkota_id').val(kdkab).trigger('change');
        }

        function genKec() {
            $('#kecamatan_id').on('change', function() {
                // alert(this.value); // Menampilkan nilai yang dipilih
                var kec = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-desa-bykecamatan', ':id') }}".replace(':id',
                        kec), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#desa_id').empty();

                        // Tambahkan opsi default
                        $('#desa_id').append(
                            '<option selected disabled>Pilih Kelurahan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, kabkota) {
                            $('#desa_id').append('<option value="' + kabkota.id +
                                '">' +
                                kabkota.name + '</option>');
                        });

                        genDesa();
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            var kdkec = $('#temp_kecamatan_id').val();
            $('#kecamatan_id').val(kdkec).trigger('change');
        }

        function genDesa() {
            // $('#desa_id').on('change', function() {
            //     // alert(this.value); // Menampilkan nilai yang dipilih
            //     var kel = this.value

            //     // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
            //     $.ajax({
            //         url: "{{ route('get-desa-bykecamatan', ':id') }}".replace(':id',
            //             kel), // Panggil API
            //         type: 'GET',
            //         success: function(response) {
            //             // Kosongkan dropdown kecamatan sebelumnya
            //             $('#desa_id').empty();

            //             // Tambahkan opsi default
            //             $('#desa_id').append(
            //                 '<option selected disabled>Pilih Kelurahan</option>');

            //             // Loop data kecamatan dan tambahkan ke dropdown
            //             $.each(response, function(index, kabkota) {
            //                 $('#desa_id').append('<option value="' + kabkota.id +
            //                     '">' +
            //                     kabkota.name + '</option>');
            //             });
            //         },
            //         error: function(xhr) {
            //             console.error(xhr);
            //         }
            //     });
            // });

            var kddes = $('#temp_desa_id').val();
            $('#desa_id').val(kddes).trigger('change');
        }
    </script>
    <script>
        function updateData() {
            var id = $('#editId').val();
            var tkn = $('#_token').val();
            let dataf = new FormData(document.getElementById("profilForm"));

            // Tambahkan CSRF token dan metode PUT
            dataf.append('_token', tkn);
            dataf.append('_method', 'PUT');

            // Send the data to the update route
            $.ajax({
                url: "{{ route('profil.pencari.update', ':id') }}".replace(':id', id),
                type: 'POST',
                data: dataf,
                processData: false,
                contentType: false,
                success: function(response) {
                    // console.log(response);
                    if (response.status == 1) {
                        // Optionally, reload the table or page to reflect the update
                        alert(response.message);
                        location.reload();
                    } else {
                        // Display error message
                        alert('Error: ' + JSON.stringify(response.errors));
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    </script>
    <script>
        $("#jurusan_id_modal").select2({
            placeholder: "Pilih Jurusan",
            allowClear: true,
            dropdownParent: $("#modal-addpendidikan")
        });

        let tablePendidikan = $('#pendidikanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('profilpendidikanformal.pencari.index') }}',
            autoWidth: false, // Menonaktifkan auto-width
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pendidikanteks',
                    name: 'pendidikanteks'
                },
                {
                    data: 'jurusanteks',
                    name: 'jurusanteks'
                },
                {
                    data: 'instansi',
                    name: 'instansi'
                },
                {
                    data: 'tahun',
                    name: 'tahun'
                },
                {
                    data: 'options',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        function addPendidikan() {
            $('#modal-addpendidikan').modal('show');
        }

        $('#pendidikan_id_modal').on('change', function() {
            // alert(this.value); // Menampilkan nilai yang dipilih
            var kd = this.value

            // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
            $.ajax({
                url: "{{ route('get-jurusan-bypendidikan', ':id') }}".replace(':id',
                    kd), // Panggil API
                type: 'GET',
                success: function(response) {
                    // Kosongkan dropdown kecamatan sebelumnya
                    $('#jurusan_id_modal').empty();

                    // Tambahkan opsi default
                    $('#jurusan_id_modal').append(
                        '<option selected disabled>Pilih Jurusan</option>');

                    // Loop data kecamatan dan tambahkan ke dropdown
                    $.each(response, function(index, jur) {
                        $('#jurusan_id_modal').append('<option value="' + jur.id +
                            '">' +
                            jur.nama + '</option>');
                    });


                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });

        $('#tambahFormPendidikan').submit(function(e) {
            e.preventDefault();

            // var tkn = $('#_token').val();
            // let dataf = new FormData(document.getElementById("tambahFormPendidikan"));

            // Tambahkan CSRF token dan metode PUT
            // dataf.append('_token', '{{ csrf_token() }}');

            // Send the data to the update route
            $.ajax({
                url: "{{ route('profilpendidikanformal.pencari.store') }}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "pendidikan_id": $('#pendidikan_id_modal').val(),
                    "jurusan_id": $('#jurusan_id_modal').val(),
                    "instansi": $('#instansi').val(),
                    "tahun": $('#tahun').val()
                },
                // processData: false,
                // contentType: false,
                success: function(response) {
                    // console.log(response);
                    if (response.status == 1) {
                        // Optionally, reload the table or page to reflect the update
                        alert(response.message);
                        $('#modal-addpendidikan').modal('hide');
                        tablePendidikan.ajax.reload();
                    } else {
                        // Display error message
                        alert('Error: ' + JSON.stringify(response.errors));
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        function confirmDeletePendidikan(id) {
            if (confirm('Apakah Anda yakin akan menghapus data ini?')) {
                $.ajax({
                    url: "{{ route('profilpendidikanformal.pencari.destroy', ':id') }}".replace(':id',
                        id),
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            alert(response.message);
                            tablePendidikan.ajax.reload();
                        } else {
                            alert('Error: ' + JSON.stringify(response.errors));
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        }

        let tableKeahlian = $('#keahlianTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('profilkeahlian.pencari.index') }}',
            auto: false,
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'keahlian',
                    name: 'keahlian'
                },
                {
                    data: 'options',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        function addKeahlian() {
            $('#modal-addkeahlian').modal('show');
        }

        $('#tambahFormKeahlian').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('profilkeahlian.pencari.store') }}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "keahlian": $('#keahlian').val()
                },
                success: function(response) {
                    if (response.status == 1) {
                        alert(response.message);
                        $('#modal-addkeahlian').modal('hide');
                        tableKeahlian.ajax.reload();
                    } else {
                        alert('Error: ' + JSON.stringify(response.errors));
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        function confirmDeleteKeahlian(id) {
            if (confirm('Apakah Anda yakin akan menghapus data ini?')) {
                $.ajax({
                    url: "{{ route('profilkeahlian.pencari.destroy', ':id') }}".replace(':id',
                        id),
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            alert(response.message);
                            tableKeahlian.ajax.reload();
                        } else {
                            alert('Error: ' + JSON.stringify(response.errors));
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>
@endpush
