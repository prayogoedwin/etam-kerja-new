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
                                    <h5 class="m-b-10">Pelamar Lowongan</h5>
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
                            <div class="card-body">
                                <h5>Data Lowongan</h5>

                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Judul Lowongan</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->judul_lowongan }}</td>
                                        </tr>
                                        {{-- <tr>
                                            <td>Deskripsi Pekerjaan</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->deskripsi }}</td>
                                        </tr> --}}
                                        <tr>
                                            <td>Jumlah Pria</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->jumlah_pria }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Wanita</td>
                                            <td>:</td>
                                            <td>{{ $lowongan->jumlah_wanita }}</td>
                                        </tr>
                                        <tr>
                                            <td>Gaji</td>
                                            <td>:</td>
                                            <td>Rp. {{ number_format($lowongan->kisaran_gaji, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex mb-3">
                    <select id="bulk-action" class="form-select me-2" style="width: auto;">
                        <option value="">Pilih Aksi</option>
                        @foreach ($progreses as $prog)
                            <option value="{{ $prog->kode }}">{{ $prog->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="bulk-keterangan" class="form-control me-2" placeholder="Masukkan keterangan"
                        style="width: auto;">
                    <button id="bulk-update-btn" class="btn btn-warning">Proses</button>
                </div>

                <div class="row">
                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                        <h5>Data Pelamar</h5>
                                    </div>
                                    {{-- <div class="col-sm-6 text-end">
                                        <button id="btnAdd" class="btn btn-success btn-sm btn-round has-ripple"
                                            data-bs-toggle="modal" data-bs-target="#modal-report"><i
                                                class="feather icon-plus"></i> Add
                                            Data</button>
                                    </div> --}}
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>No Telepon</th>
                                                <th>Tanggal Lamar</th>
                                                <th>Status</th>
                                                <th>Options<input type="checkbox" id="select-all" style="margin-left: 4px">
                                                </th>
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
                        <h5 class="modal-title" id="modalEditLabel">Data Pelamar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <table align="center" width="100%">
                                    <thead>
                                        <tr align="center">
                                            <th colspan="3" id="fotonya"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <hr>
                                <h4>Informasi</h4>
                                <hr>
                                <table align="center" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>:</th>
                                            <th id="nama"></th>
                                        </tr>
                                        <tr>
                                            <th>Tempat Lahir</th>
                                            <th>:</th>
                                            <th id="tempat_lahir"></th>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Lahir</th>
                                            <th>:</th>
                                            <th id="tanggal_lahir"></th>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <th>:</th>
                                            <th id="alamat"></th>
                                        </tr>
                                        <tr>
                                            <th>Provinsi</th>
                                            <th>:</th>
                                            <th id="provinsi"></th>
                                        </tr>
                                        <tr>
                                            <th>Kabupaten/Kota</th>
                                            <th>:</th>
                                            <th id="kabkota"></th>
                                        </tr>
                                        <tr>
                                            <th>Kecamatan</th>
                                            <th>:</th>
                                            <th id="kecamatan"></th>
                                        </tr>
                                        <tr>
                                            <th>Kelurahan/Desa</th>
                                            <th>:</th>
                                            <th id="kelurahan"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <hr>
                                <h4>Pendidikan</h4>
                                <hr>
                                <table align="center" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Tahun Lulus</th>
                                            <th>:</th>
                                            <th id="tahun_lulus"></th>
                                        </tr>
                                        <tr>
                                            <th>Pendidikan</th>
                                            <th>:</th>
                                            <th id="pendidikan"></th>
                                        </tr>
                                        <tr>
                                            <th>Jurusan</th>
                                            <th>:</th>
                                            <th id="jurusan"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <hr>
                                <h4>Lainnya</h4>
                                <hr>
                                <table align="center" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>:</th>
                                            <th id="email"></th>
                                        </tr>
                                        <tr>
                                            <th>Whatsapp</th>
                                            <th>:</th>
                                            <th id="whatsapp"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>


                        <div class="float-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </body>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            let table = $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lowongan.pelamar', $lowongan_id) }}',
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'whatsapp',
                        name: 'whatsapp'
                    },
                    // { data: 'keterangan', name: 'keterangan' },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'statuslamaran',
                        name: 'statuslamaran'
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Handle select all checkboxes
            $('#select-all').on('change', function() {
                $('.pelamar-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Bulk update
            $('#bulk-update-btn').on('click', function() {
                let action = $('#bulk-action').val();
                let keterangan = $('#bulk-keterangan').val();

                if (!action) {
                    alert('Pilih aksi terlebih dahulu!');
                    return;
                }

                let selectedIds = $('.pelamar-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    alert('Pilih minimal satu pelamar!');
                    return;
                }

                $.ajax({
                    url: '{{ route('bulk.update.pelamar') }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        action: action,
                        keterangan: keterangan,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload();
                        // location.reload();
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat memproses data.');
                    }
                });
            });
        });
    </script>

    <script>
        function showDetailModal(pelamar_id) {
            // alert(pelamar_id)
            // // Send the data to the update route
            $.ajax({
                url: "{{ route('lowongan.detailpelamar', ':id') }}".replace(':id', pelamar_id),
                type: 'GET',
                success: function(response) {
                    // console.log(response)
                    if (response.status == 1) {

                        // Close modal
                        $('#modal-edit').modal('show');

                        // Set the data
                        $('#nama').html(response.data.name);
                        // $('#ftpencari').html(response.data.foto);
                        // $('#temp_foto').html(response.data.foto);
                        if (response.data.foto == null) {
                            $('#fotonya').html('Tidak ada foto');
                        } else {
                            $('#fotonya').html('<img src="{{ asset('storage') }}/' +
                                response.data.foto + '" alt="" width="130px">');
                        }
                        $('#tempat_lahir').html(response.data.tempat_lahir);
                        $('#tanggal_lahir').html(response.data.tanggal_lahir);
                        $('#tahun_lulus').html(response.data.tahun_lulus);
                        $('#alamat').html(response.data.alamat);

                        $('#email').html(response.data.user.email);
                        $('#whatsapp').html(response.data.user.whatsapp);

                        $('#provinsi').html(response.data.provinsi.name);
                        $('#kabkota').html(response.data.kabupaten.name);
                        $('#kecamatan').html(response.data.kecamatan.name);
                        $('#kelurahan').html(response.data.desa.name);

                        $('#pendidikan').html(response.data.pendidikan.name);
                        $('#jurusan').html(response.data.jurusan.nama);
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
@endpush
