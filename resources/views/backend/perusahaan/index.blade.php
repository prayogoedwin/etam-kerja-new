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
                                    <h5 class="m-b-10">Perusahaan</h5>
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

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


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
                                        {{-- <button id="btnAdd" class="btn btn-success btn-sm btn-round has-ripple"
                                            data-bs-toggle="modal" data-bs-target="#modal-report"><i
                                                class="feather icon-plus"></i> Add
                                            Data</button> --}}
                                        @php
                                            $prefix = encode_url('penyedia-kerja');
                                            $furl = url('/dapur/bkks/daftar-perusahaan-by-bkk?rl=' . $prefix);
                                        @endphp
                                        <a href="{{ $furl }}" class="btn btn-success btn-sm btn-round has-ripple"><i
                                                class="feather icon-plus"></i> Tambah</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Alamat</th>
                                                <th>Telpon</th>
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

    </body>
@endsection


@push('js')
    <script>
        function confirmDelete(id) {
            // Konfirmasi penghapusan
            var deleteUrl = "{{ route('perusahaan.softdelete', ':id') }}".replace(':id', id);
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
            $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('perusahaan.index') }}',
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'telpon'
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

        });
    </script>

    <script>
        function showData(id) {

            // $.ajax({
            //     url: '{{ route('get-all-pendidikan') }}', // Endpoint Laravel
            //     type: 'GET',
            //     dataType: 'json',
            //     success: function(data) {
            //         // Kosongkan dropdown
            //         let $pendidikanSelect = $('#pendidikan_id_edit');
            //         $pendidikanSelect.empty();
            //         $pendidikanSelect.append(
            //             '<option value="">-- Pilih Pendidikan --</option>');

            //         // Looping data dari response dan tambahkan ke select
            //         $.each(data, function(index, item) {
            //             $pendidikanSelect.append(
            //                 `<option value="${item.id}">${item.name}</option>`
            //             );
            //         });
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Error fetching data:', error);
            //     }
            // });

            // var detailUrl = "{{ route('lowongan.detail', ':id') }}".replace(':id', id);
            // $.ajax({
            //     url: detailUrl,
            //     type: 'GET',
            //     success: function(response) {
            //         console.log(response);

            //         let dt = response.data;
            //         var sts = response.success;

            //         if (sts == 1) {
            //             // Isi data modal dengan data yang diperoleh
            //             $('#editId').val(dt.id);

            //             $('#is_lowongan_disabilitas_edit').val(dt.is_lowongan_disabilitas).trigger('change');
            //             $('#jabatan_id_edit').val(dt.jabatan_id).trigger('change');
            //             $('#sektor_id_edit').val(dt.sektor_id).trigger('change');
            //             $('#pendidikan_id_edit').val(dt.pendidikan_id).trigger('change');

            //             $('#tanggal_start_edit').val(dt.tanggal_start);
            //             $('#tanggal_end_edit').val(dt.tanggal_end);
            //             $('#judul_lowongan_edit').val(dt.judul_lowongan);
            //             $('#kabkota_id_edit').val(dt.kabkota_id).trigger('change');
            //             $('#lokasi_penempatan_text_edit').val(dt.lokasi_penempatan_text);
            //             $('#kisaran_gaji_edit').val(dt.kisaran_gaji);
            //             $('#kisaran_gaji_akhir_edit').val(dt.kisaran_gaji_akhir);
            //             $('#jumlah_pria_edit').val(dt.jumlah_pria);
            //             $('#jumlah_wanita_edit').val(dt.jumlah_wanita);
            //             $('#deskripsi_edit').val(dt.deskripsi);
            //             $('#status_perkawinan_id_edit').val(dt.marital_id).trigger('change');

            //             //set timeout
            //             setTimeout(function() {
            //                 $('#jurusan_id_edit').val(dt.jurusan_id).trigger('change');
            //             }, 1000);

            //             // Tampilkan modal edit
            //             $('#modal-edit').modal('show');
            //         }


            //     },
            //     error: function(xhr) {
            //         alert('Error: ' + xhr.responseText);
            //     }
            // });
        }
    </script>
@endpush
