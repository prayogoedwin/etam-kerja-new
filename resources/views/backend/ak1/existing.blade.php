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
                                <h5 class="m-b-10">AK 1</h5>
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
                        <div class="card">
                            <div class="card-header">
                                <h5>Cetak AK1 - Pencarian KTP</h5>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('ak1.existing') }}">
                                    <div class="mb-3">
                                        <label for="ktp" class="form-label">Masukkan Nomor KTP</label>
                                        <input type="text" id="ktp" name="ktp" class="form-control"
                                            value="{{ request('ktp') }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Cari KTP</button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            
                            @if ($user)
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5>Detail Profil</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $kabkotas = getKabkota();
                                        $pendidikans = getPendidikan();
                                        $maritals = getMarital();
                                        $agamas = getAgama();
                                        $sektors = getSektor();
                                        ?>
                                        <form method="POST" enctype="multipart/form-data"
                                            action="{{ route('ak1.update', $user->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="name" class="form-label">Nama Lengkap</label>
                                                            <input type="text" id="name" name="name"
                                                                class="form-control" value="{{ $user->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="ktp" class="form-label">KTP</label>
                                                            <input type="text" id="ktp" name="ktp"
                                                                class="form-control" value="{{ $user->ktp }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="tempat_lahir" class="form-label">Tempat
                                                                Lahir</label>
                                                            <input type="text" id="tempat_lahir" name="tempat_lahir"
                                                                class="form-control"
                                                                value="{{ $user->tempat_lahir ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="tanggal_lahir" class="form-label">Tanggal
                                                                Lahir</label>
                                                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                                                class="form-control"
                                                                value="{{ $user->tanggal_lahir ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="gender">Jenis Kelamin</label>
                                                            <select id="gender" class="form-control" name="gender">
                                                                <option value="L"
                                                                    {{ $user->gender == 'L' ? 'selected' : '' }}>
                                                                    Laki-laki</option>
                                                                <option value="P"
                                                                    {{ $user->gender == 'P' ? 'selected' : '' }}>
                                                                    Perempuan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="kabkota" class="form-label">Kabupaten /
                                                                Kota</label>
                                                            <select class="form-select" id="kabkota_id" name="kabkota_id"
                                                                required>
                                                                <option disabled>Pilih Kabupaten/Kota</option>
                                                                @foreach ($kabkotas as $kabkot)
                                                                    <option value="{{ $kabkot->id }}"
                                                                        {{ $user->id_kota == $kabkot->id ? 'selected' : '' }}>
                                                                        {{ $kabkot->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="kecamatan" class="form-label">Kecamatan</label>
                                                            <select class="form-select" id="kecamatan_id"
                                                                name="kecamatan_id" required>
                                                                <option disabled>Pilih Kecamatan</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="kelurahan" class="form-label">Desa /
                                                                Kelurahan</label>
                                                            <select class="form-select" id="desa_id" name="desa_id"
                                                                required>
                                                                <option disabled>Pilih Desa/Kelurahan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="alamat" class="form-label">Alamat</label>
                                                            <input type="text" id="alamat" name="alamat"
                                                                class="form-control"
                                                                value="{{ $user->alamat ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="kodepos">Kode Pos</label>
                                                            <input type="text" id="kodepos" class="form-control"
                                                                name="kodepos" value="{{ $user->kodepos }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="pendidikan_id">Pendidikan</label>
                                                            <select class="form-control" id="pendidikan_id"
                                                                name="pendidikan_id" required>
                                                                <option selected disabled>Pilih Pendidikan</option>
                                                                @foreach ($pendidikans as $pend)
                                                                    <option value="{{ $pend->id }}"
                                                                        {{ $user->id_pendidikan == $pend->id ? 'selected' : '' }}>
                                                                        {{ $pend->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="jurusan_id">Jurusan</label>
                                                            <select class="form-control" id="jurusan_id"
                                                                name="jurusan_id" required>
                                                                <option selected disabled>Pilih Jurusan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="tahun_lulus">Tahun Lulus</label>
                                                            <input type="text" id="tahun_lulus" class="form-control"
                                                                name="tahun_lulus"
                                                                value="{{ $user->tahun_lulus }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="stsperkawinan" class="form-label">Status
                                                                Perkawinan</label>
                                                            <select class="form-select" id="status_perkawinan_id"
                                                                name="status_perkawinan_id" required>
                                                                <option selected disabled>Pilih Status</option>
                                                                @foreach ($maritals as $marit)
                                                                    <option value="{{ $marit->id }}"
                                                                        {{ $user->id_status_perkawinan == $marit->id ? 'selected' : '' }}>
                                                                        {{ $marit->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="agama" class="form-label">Agama</label>
                                                            <select class="form-select" id="agama_id" name="agama_id"
                                                                required>
                                                                <option selected disabled>Pilih Agama</option>
                                                                @foreach ($agamas as $ag)
                                                                    <option value="{{ $ag->id }}"
                                                                        {{ $user->id_agama == $ag->id ? 'selected' : '' }}>
                                                                        {{ $ag->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="medsos">medsos</label>
                                                            <input type="text" id="medsos" class="form-control"
                                                                name="medsos" value="{{ $user->medsos }}">
                                                        </div>
                                                    </div>
                                                   
                                                   
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="foto" class="form-label">Upload Foto</label>
                                                            <input type="file" id="foto" name="foto"
                                                                class="form-control">
                                                            @if ($user->foto)
                                                                <img src="{{ asset('storage/' . $user->foto) }}"
                                                                    alt="Foto Profil" class="img-thumbnail mt-2"
                                                                    width="150">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Add other fields as required -->

                                            <button type="submit" class="btn btn-success mt-3">Update</button>
                                            <a href="{{ route('ak1.print', $user->id) }}"
                                                class="btn btn-primary mt-3">Cetak AK1</a>
                                        </form>
                                    </div>
                                </div>
                            @elseif(request('ktp'))
                                <div class="alert alert-warning mt-4">
                                    Data tidak ditemukan untuk KTP: {{ request('ktp') }}
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
                <!-- customar project  end -->


            </div>
            <!-- [ Main Content ] end -->


        </div>
    </div>
@endsection

@push('js')
    <script>
        // Fungsi untuk menampilkan atau menyembunyikan bidang pekerjaan
        function togglePekerjaanFields(status) {
            const pekerjaanFields = document.getElementById('pekerjaan-fields');
            pekerjaanFields.style.display = (status === '1') ? 'block' : 'none';
        }

        // Periksa status saat ini setelah halaman dimuat
        window.onload = function() {
            const statusKerjaSelect = document.getElementById('status_kerja_id');
            togglePekerjaanFields(statusKerjaSelect.value); // Periksa nilai awal pada halaman load
        };
    </script>
    @if ($user)
        <script>
            $(document).ready(function() {
                var kabkotaId = "{{ $user->id_kota }}";
                var kecamatanId = "{{ $user->id_kecamatan }}";
                var desaId = "{{ $user->id_desa }}";

                // Muat data kecamatan jika ada kabkota terpilih
                if (kabkotaId) {
                    loadKecamatan(kabkotaId, kecamatanId);
                }

                // Muat data desa jika ada kecamatan terpilih
                if (kecamatanId) {
                    loadDesa(kecamatanId, desaId);
                }

                $('#kabkota_id').on('change', function() {
                    var kabkotaId = $(this).val();
                    $('#kecamatan_id').empty().append('<option disabled>Pilih Kecamatan</option>');
                    $('#desa_id').empty().append('<option disabled>Pilih Desa/Kelurahan</option>');

                    if (kabkotaId) {
                        loadKecamatan(kabkotaId);
                    }
                });

                $('#kecamatan_id').on('change', function() {
                    var kecamatanId = $(this).val();
                    $('#desa_id').empty().append('<option disabled>Pilih Desa/Kelurahan</option>');

                    if (kecamatanId) {
                        loadDesa(kecamatanId);
                    }
                });

                function loadKecamatan(kabkotaId, selectedId = null) {
                    $.ajax({
                        url: "{{ route('get-kecamatan-bykabkota', ':id') }}".replace(':id', kabkotaId),
                        type: 'GET',
                        success: function(response) {
                            $.each(response, function(index, kecamatan) {
                                $('#kecamatan_id').append('<option value="' + kecamatan.id + '"' +
                                    (kecamatan.id == selectedId ? ' selected' : '') + '>' +
                                    kecamatan.name + '</option>');
                            });
                        }
                    });
                }

                function loadDesa(kecamatanId, selectedId = null) {
                    $.ajax({
                        url: "{{ route('get-desa-bykecamatan', ':id') }}".replace(':id', kecamatanId),
                        type: 'GET',
                        success: function(response) {
                            $.each(response, function(index, desa) {
                                $('#desa_id').append('<option value="' + desa.id + '"' +
                                    (desa.id == selectedId ? ' selected' : '') + '>' +
                                    desa.name + '</option>');
                            });
                        }
                    });
                }
            });
        </script>

        <script>
            $('#kabkota_id').on('change', function() {
                // console.log(this.value);
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-kecamatan-bykabkota', ':id') }}".replace(':id', kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#kecamatan_id').empty();

                        $('#desa_id').empty();
                        $('#desa_id').append('<option selected disabled>Pilih Desa/Kelurahan</option>');

                        // Tambahkan opsi default
                        $('#kecamatan_id').append('<option selected disabled>Pilih Kecamatan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, kecamatan) {
                            $('#kecamatan_id').append('<option value="' + kecamatan.id + '">' +
                                kecamatan.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });


            $('#kecamatan_id').on('change', function() {
                // console.log(this.value);
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-desa-bykecamatan', ':id') }}".replace(':id', kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#desa_id').empty();

                        // Tambahkan opsi default
                        $('#desa_id').append('<option selected disabled>Pilih Desa/Kelurahan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, kecamatan) {
                            $('#desa_id').append('<option value="' + kecamatan.id + '">' +
                                kecamatan.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            $('#pendidikan_id').on('change', function() {
                // console.log(this.value);
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-jurusan-bypendidikan', ':id') }}".replace(':id', kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#jurusan_id').empty();

                        // Tambahkan opsi default
                        $('#jurusan_id').append('<option selected disabled>Pilih Jurusan</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, jurusan) {
                            $('#jurusan_id').append('<option value="' + jurusan.id + '">' +
                                jurusan.nama + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });
        </script>

        <script>
            $('#provinsi_id').on('change', function() {
                // console.log(this.value);
                var kd = this.value

                // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
                $.ajax({
                    url: "{{ route('get-kabkota-byprov', ':id') }}".replace(':id', kd), // Panggil API
                    type: 'GET',
                    success: function(response) {
                        // Kosongkan dropdown kecamatan sebelumnya
                        $('#kabkota_id').empty();

                        // Tambahkan opsi default
                        $('#kabkota_id').append('<option selected disabled>Pilih Kabupaten/Kota</option>');

                        // Loop data kecamatan dan tambahkan ke dropdown
                        $.each(response, function(index, kabkota) {
                            $('#kabkota_id').append('<option value="' + kabkota.id + '">' +
                                kabkota.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                var pendidikanId = "{{ $user->id_pendidikan }}";
                var jurusanId = "{{ $user->id_jurusan }}";

                // Jika ada pendidikan terpilih, muat jurusan terkait
                if (pendidikanId) {
                    loadJurusan(pendidikanId, jurusanId);
                }

                $('#pendidikan_id').on('change', function() {
                    var pendidikanId = $(this).val();
                    $('#jurusan_id').empty().append('<option selected disabled>Pilih Jurusan</option>');

                    if (pendidikanId) {
                        loadJurusan(pendidikanId);
                    }
                });

                function loadJurusan(pendidikanId, selectedId = null) {
                    $.ajax({
                        url: "{{ route('get-jurusan-bypendidikan', ':id') }}".replace(':id', pendidikanId),
                        type: 'GET',
                        success: function(response) {
                            $.each(response, function(index, jurusan) {
                                $('#jurusan_id').append('<option value="' + jurusan.id + '"' +
                                    (jurusan.id == selectedId ? ' selected' : '') + '>' +
                                    jurusan.nama + '</option>');
                            });
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                }
            });
        </script>
     @endif

@endpush
