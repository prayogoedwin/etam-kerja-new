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
                    <div class="col-12">
                        <div class="card">
                            <h5 class="card-header">Profil userid : {{ auth()->user()->id }}</h5>
                            <div class="card-body">

                                <div class="row">
                                    <div class="form-group row">
                                        <div class="col-2">
                                            <label for="">Nama</label>
                                        </div>
                                        <div class="col-10">
                                            <input type="text" class="form-control" name="nama" id="nama"
                                                value="{{ $profil->name }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-2">
                                            <label for="">Deskripsi</label>
                                        </div>
                                        <div class="col-10">
                                            <textarea class="form-control" name="deskripsi" id="deskripsi" cols="30" rows="3">{{ $profil->deskripsi }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-2">
                                            <label for="">Jenis Perusahaan</label>
                                        </div>
                                        <div class="col-10">
                                            <select class="form-select" id="jenis_perusahaan" name="jenis_perusahaan"
                                                required>
                                                <option value="">Pilih Jenis</option>
                                                <option value="bumd"
                                                    {{ $profil->jenis_perusahaan == 'bumd' ? 'selected' : '' }}>Badan Usaha
                                                    Milik Daerah</option>
                                                <option value="bumn"
                                                    {{ $profil->jenis_perusahaan == 'bumn' ? 'selected' : '' }}>Badan Usaha
                                                    Milik Negara</option>
                                                <option value="cv"
                                                    {{ $profil->jenis_perusahaan == 'cv' ? 'selected' : '' }}>Comanditer
                                                    Venotschaap</option>
                                                <option value="firma"
                                                    {{ $profil->jenis_perusahaan == 'firma' ? 'selected' : '' }}>Firma
                                                </option>
                                                <option value="instansi"
                                                    {{ $profil->jenis_perusahaan == 'instansi' ? 'selected' : '' }}>Instansi
                                                </option>
                                                <option value="kp"
                                                    {{ $profil->jenis_perusahaan == 'kp' ? 'selected' : '' }}>Koperasi
                                                </option>
                                                <option value="pt"
                                                    {{ $profil->jenis_perusahaan == 'pt' ? 'selected' : '' }}>Perseroan
                                                    Terbatas</option>
                                                <option value="pp"
                                                    {{ $profil->jenis_perusahaan == 'pp' ? 'selected' : '' }}>Perusahaan
                                                    Perorangan</option>
                                                <option value="po"
                                                    {{ $profil->jenis_perusahaan == 'po' ? 'selected' : '' }}>PO*</option>
                                                <option value="yayasan"
                                                    {{ $profil->jenis_perusahaan == 'yayasan' ? 'selected' : '' }}>Yayasan
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-2">
                                            <label for="">NIB</label>
                                        </div>
                                        <div class="col-10">
                                            <input type="text" class="form-control" name="nama" id="nama"
                                                value="{{ $profil->nib }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-2">
                                            <label for="">Sektor</label>
                                        </div>
                                        <div class="col-10">
                                            <select class="form-select" id="sektor_id" name="sektor_id" required>
                                                <option value="">Pilih Sektor</option>
                                                @foreach ($sektors as $sekt)
                                                    <option value="{{ $sekt->id }}"
                                                        {{ $profil->id_sektor == $sekt->id ? 'selected' : '' }}>
                                                        {{ $sekt->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-2">
                                            <label for="">Provinsi</label>
                                        </div>
                                        <div class="col-10">
                                            <input type="hidden" id="temp_provinsi_id" value="{{ $profil->id_provinsi }}">
                                            <select class="form-select" id="provinsi_id" name="provinsi_id" required>
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
                                            <select class="form-select" id="kabkota_id" name="kabkota_id" required>
                                                <option value="">Pilih Kabkota</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <a href="#!" class="btn  btn-primary">Go somewhere</a>
                                </div>

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
            $("#sektor_id").select2({
                placeholder: "Pilih Sektor"
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
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            // Kemudian, set nilai dan trigger event
            var kdprov = $('#temp_provinsi_id').val();
            $('#provinsi_id').val(kdprov).trigger('change'); // Memicu event change
        });
    </script>
@endpush
