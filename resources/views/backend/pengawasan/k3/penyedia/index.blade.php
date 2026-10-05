@extends('backend.template.backend')

@section('content')

    <body class="box-layout container background-green">
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Pengawasan K3 - Permohonan Riksa Uji</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                        <h4 class="mb-0">Daftar Pengajuan Riksa Uji</h4>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button class="btn btn-success btn-sm btn-round has-ripple"
                                            onclick="openTambahModal()">
                                            <i class="feather icon-plus"></i> Tambah Pengajuan
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Alat</th>
                                                <th>Kategori</th>
                                                <th>Jenis Alat</th>
                                                <th>Lokasi</th>
                                                <th>Status Disposisi</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form (Tambah / Edit) -->
        <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pengajuan Riksa Uji K3</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAjuan" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="ajuan_id">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori K3</label>
                                    <select name="kategori_id" id="kategori_id" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach (\App\Models\Pengawasan\K3\EtamPengawasanK3Kategori::all() as $kat)
                                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Alat</label>
                                    <select name="jenis_id" id="jenis_id" class="form-control" required>
                                        <option value="">Pilih Kategori Terlebih Dahulu</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Alat / Objek</label>
                                    <input type="text" name="nama_alat" id="nama_alat" class="form-control"
                                        placeholder="Contoh: Lift Penumpang Gedung A" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi Alat</label>
                                    <input type="text" name="lokasi_alat" id="lokasi_alat" class="form-control"
                                        placeholder="Lokasi penempatan alat" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kapasitas Alat</label>
                                    <input type="text" name="kapasitas_alat" id="kapasitas_alat" class="form-control"
                                        placeholder="Contoh: 1000 Kg" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jumlah Unit</label>
                                    <input type="number" name="jumlah_unit" id="jumlah_unit" class="form-control"
                                        value="1" min="1" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Dokumen Permohonan (PDF/Gambar max 2MB) <br> <small
                                            class="text-muted" id="fileInfo"></small></label>
                                    <input type="file" name="dok_unggah_penyedia" id="dok_unggah_penyedia"
                                        class="form-control">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Keterangan (Opsional)</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var table;
        $(document).ready(function() {
            table = $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('pengawasan.k3.penyedia.index') }}',
                autoWidth: false,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_alat'
                    },
                    {
                        data: 'kategori_nama'
                    },
                    {
                        data: 'jenis_nama'
                    },
                    {
                        data: 'lokasi_alat'
                    },
                    {
                        data: 'status_disposisi',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Dependent Dropdown
            $('#kategori_id').on('change', function() {
                var kategoriId = $(this).val();
                var selectedJenisId = $('#jenis_id').data('selected'); // untuk kebutuhan edit
                $('#jenis_id').html('<option value="">Memuat...</option>');

                if (kategoriId) {
                    var url = '{{ route('pengawasan.k3.penyedia.get-jenis', ':id') }}';
                    url = url.replace(':id', kategoriId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#jenis_id').html('<option value="">Pilih Jenis Alat</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == selectedJenisId) ?
                                    'selected' : '';
                                $('#jenis_id').append('<option value="' + value.id +
                                    '" ' + selected + '>' + value.nama + '</option>'
                                    );
                            });
                            $('#jenis_id').removeData('selected');
                        }
                    });
                } else {
                    $('#jenis_id').html('<option value="">Pilih Kategori Terlebih Dahulu</option>');
                }
            });



            // Submit Form (Tambah / Update)
            $('#formAjuan').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var id = $('#ajuan_id').val();
                var url = id ? '{{ route("pengawasan.k3.penyedia.update", ":id") }}'.replace(':id', id) : '{{ route("pengawasan.k3.penyedia.store") }}';

                $('#btnSimpan').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#btnSimpan').prop('disabled', false).text('Simpan');
                        if (response.status === 'success') {
                            $('#modal-report').modal('hide');
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#btnSimpan').prop('disabled', false).text('Simpan');
                        var errorMessage = 'Terjadi kesalahan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage
                        });
                    }
                });
            });
        });

        // Buka Modal untuk Tambah
        function openTambahModal() {
            $('#formAjuan')[0].reset();
            $('#ajuan_id').val('');
            $('#modalTitle').text('Tambah Pengajuan Riksa Uji K3');
            $('#btnSimpan').text('Simpan Pengajuan');
            $('#jenis_id').html('<option value="">Pilih Kategori Terlebih Dahulu</option>');
            $('#fileInfo').text('');
            $('#modal-report').modal('show');
        }

        // Buka Modal untuk Edit (Ambil Data via AJAX)
        function editData(id) {
            var editUrl = '{{ route("pengawasan.k3.penyedia.edit", ":id") }}';
            editUrl = editUrl.replace(':id', id);

            $.ajax({
                url: editUrl,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        var data = response.data;
                        $('#ajuan_id').val(data.id);
                        $('#nama_alat').val(data.nama_alat);
                        $('#lokasi_alat').val(data.lokasi_alat);
                        $('#kapasitas_alat').val(data.kapasitas_alat);
                        $('#jumlah_unit').val(data.jumlah_unit);
                        $('#keterangan').val(data.keterangan);

                        if (data.dok_unggah_penyedia) {
                            $('#fileInfo').text('(Abaikan jika tidak ingin mengubah dokumen)');
                        } else {
                            $('#fileInfo').text('');
                        }

                        // Ambil data jenis berdasarkan kategori terlebih dahulu sebelum set value
                        var jenisUrl = '{{ route("pengawasan.k3.penyedia.get-jenis", ":kategori_id") }}';
                        jenisUrl = jenisUrl.replace(':kategori_id', data.kategori_id);

                        $.ajax({
                            url: jenisUrl,
                            type: 'GET',
                            dataType: 'json',
                            success: function(jenisData) {
                                var options = '<option value="">Pilih Jenis Alat</option>';
                                $.each(jenisData, function(key, value) {
                                    var selected = (value.id == data.jenis_id) ? 'selected' : '';
                                    options += '<option value="' + value.id + '" ' + selected + '>' + value.nama + '</option>';
                                });
                                $('#jenis_id').html(options);

                                // Set kategori setelah dropdown jenis terisi
                                $('#kategori_id').val(data.kategori_id);
                            }
                        });

                        $('#modalTitle').text('Edit Pengajuan Riksa Uji K3');
                        $('#btnSimpan').text('Perbarui Data');
                        $('#modal-report').modal('show');
                    }
                },
                error: function() {
                    Swal.fire('Gagal', 'Tidak dapat mengambil data.', 'error');
                }
            });
        }

        // Fungsi Konfirmasi Hapus Menggunakan SweetAlert2
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pengajuan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var deleteUrl = '{{ route('pengawasan.k3.penyedia.destroy', ':id') }}';
                    deleteUrl = deleteUrl.replace(':id', id);

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                table.ajax.reload();
                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                );
                            }
                        },
                        error: function(xhr) {
                            var errMessage = 'Gagal menghapus data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errMessage = xhr.responseJSON.message;
                            }
                            Swal.fire('Gagal!', errMessage, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
