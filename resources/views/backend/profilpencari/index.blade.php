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
                                    <h5 class="m-b-10">Profil Pencari Kerja</h5>
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
                                        <div class="form-group row">
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


            </div>
        </div>

        <div class="modal fade" id="modal-report" role="dialog" aria-labelledby="myExtraLargeModalLabel"
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
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{-- <label class="floating-label" for="jdllow">Judul Lowongan</label> --}}
                                    <textarea class="form-control" id="judul_lowongan" name="judul_lowongan" rows="3"
                                        placeholder="Judul Lowongan"></textarea>
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
@endpush
