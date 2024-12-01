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
                                                <label for="name" class="form-label">Nama
                                                    Lengkap</label>
                                                <input type="text" id="name" name="name" class="form-control"
                                                    value="{{ $user->name }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="ktp" class="form-label">KTP</label>
                                                <input type="text" id="ktp" name="ktp" class="form-control"
                                                    value="{{ $user->ktp }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="tempat_lahir" class="form-label">Tempat
                                                    Lahir</label>
                                                <input type="text" id="tempat_lahir" name="tempat_lahir"
                                                    class="form-control" value="{{ $user->tempat_lahir ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="tanggal_lahir" class="form-label">Tanggal
                                                    Lahir</label>
                                                <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                                    class="form-control" value="{{ $user->tanggal_lahir ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="gender">Jenis Kelamin</label>
                                                <select id="gender" class="form-control" name="gender">
                                                    <option value="L" {{ $user->gender == 'L' ? 'selected' : '' }}>
                                                        Laki-laki</option>
                                                    <option value="P" {{ $user->gender == 'P' ? 'selected' : '' }}>
                                                        Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="kabkota" class="form-label">Kabupaten /
                                                    Kota</label>
                                                <select class="form-select" id="kabkota_id" name="kabkota_id" required>
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
                                                <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                                                    <option disabled>Pilih Kecamatan</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- <div class="col-6">
                                            <div class="form-group">
                                                <label for="kelurahan" class="form-label">Desa /
                                                    Kelurahan</label>
                                                <select class="form-select" id="desa_id" name="desa_id"
                                                    required>
                                                    <option disabled>Pilih Desa/Kelurahan</option>
                                                </select>
                                            </div>
                                        </div> --}}
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="alamat" class="form-label">Alamat</label>
                                                <input type="text" id="alamat" name="alamat" class="form-control"
                                                    value="{{ $user->alamat ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="kodepos">Kode Pos</label>
                                                <input type="text" id="kodepos" class="form-control" name="kodepos"
                                                    value="{{ $user->kodepos }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="pendidikan_id">Pendidikan</label>
                                                <select class="form-control" id="pendidikan_id" name="pendidikan_id"
                                                    required>
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
                                                <select class="form-control" id="jurusan_id" name="jurusan_id" required>
                                                    <option value="">Pilih Jurusan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="tahun_lulus">Tahun Lulus</label>
                                                <input type="text" id="tahun_lulus" class="form-control"
                                                    name="tahun_lulus" value="{{ $user->tahun_lulus }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="stsperkawinan" class="form-label">Status
                                                    Perkawinan</label>
                                                <select class="form-select" id="status_perkawinan_id"
                                                    name="status_perkawinan_id" required>
                                                    <option value="">Pilih Status</option>
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
                                                <select class="form-select" id="agama_id" name="agama_id" required>
                                                    <option value="">Pilih Agama</option>
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
                                                <input type="text" id="medsos" class="form-control" name="medsos"
                                                    value="{{ $user->medsos }}">
                                            </div>
                                        </div>


                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="foto" class="form-label">Upload
                                                    Foto</label>
                                                <input type="file" id="foto" name="foto"
                                                    class="form-control">
                                                @if ($user->foto)
                                                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil"
                                                        class="img-thumbnail mt-2" width="150">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Add other fields as required -->

                                <button type="submit" class="btn btn-success mt-3">Update</button>
                                <a href="{{ route('ak1.pencari.print') }}" class="btn btn-primary mt-3">Cetak AK1</a>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- [ Main Content ] end -->


            </div>
        </div>

    </body>
@endsection


@push('js')
@endpush
