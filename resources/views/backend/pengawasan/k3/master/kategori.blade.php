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
                                    <h5 class="m-b-10">Kategori K3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0 mb-3">
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-0">Master kategori objek K3 (Listrik, PTP, PUBT, dst.).</p>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button type="button" class="btn btn-success btn-sm" id="btnTambahKategori">
                                            Tambah kategori
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tabelKategori" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Kode</th>
                                                <th>Keterangan</th>
                                                <th>Jumlah jenis</th>
                                                <th>Aksi</th>
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

        <div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formKategori">
                        <div class="modal-header">
                            <h5 class="modal-title" id="judulKategori">Tambah kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="kategoriId">
                            <div class="mb-3">
                                <label class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" id="kategoriNama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input type="text" class="form-control text-uppercase" name="kode" id="kategoriKode"
                                    maxlength="20" placeholder="Contoh: LIS">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="kategoriKeterangan" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
            var tabel = $('#tabelKategori').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('pengawasan.k3.master.kategori.index') }}',
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama' },
                    { data: 'kode' },
                    { data: 'keterangan' },
                    { data: 'jumlah_jenis', searchable: false },
                    { data: 'options', orderable: false, searchable: false },
                ]
            });

            function bukaTambah() {
                $('#judulKategori').text('Tambah kategori');
                $('#kategoriId').val('');
                $('#formKategori')[0].reset();
                $('#modalKategori').modal('show');
            }

            $('#btnTambahKategori').on('click', bukaTambah);

            $(document).on('click', '.js-edit-kategori', function() {
                var id = $(this).data('id');
                $.get('{{ url('dapur/layanan-k3/master/kategori') }}/' + id, function(response) {
                    if (!response.success) {
                        Swal.fire('Gagal', 'Data tidak ditemukan', 'error');
                        return;
                    }
                    $('#judulKategori').text('Edit kategori');
                    $('#kategoriId').val(response.data.id);
                    $('#kategoriNama').val(response.data.nama);
                    $('#kategoriKode').val(response.data.kode);
                    $('#kategoriKeterangan').val(response.data.keterangan);
                    $('#modalKategori').modal('show');
                });
            });

            $('#formKategori').on('submit', function(e) {
                e.preventDefault();
                var id = $('#kategoriId').val();
                var url = id
                    ? '{{ url('dapur/layanan-k3/master/kategori') }}/' + id
                    : '{{ route('pengawasan.k3.master.kategori.store') }}';
                $.ajax({
                    url: url,
                    type: id ? 'PUT' : 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nama: $('#kategoriNama').val(),
                        kode: $('#kategoriKode').val(),
                        keterangan: $('#kategoriKeterangan').val(),
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#modalKategori').modal('hide');
                            Swal.fire('Berhasil', response.message, 'success');
                            tabel.ajax.reload(null, false);
                        } else if (response.errors) {
                            var msg = '';
                            $.each(response.errors, function(_, value) {
                                $.each(value, function(_, errorMessage) {
                                    msg += errorMessage + '\n';
                                });
                            });
                            Swal.fire('Periksa kembali', msg, 'warning');
                        } else {
                            Swal.fire('Gagal', response.message || 'Gagal menyimpan', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Gagal menyimpan', 'error');
                    }
                });
            });

            $(document).on('click', '.js-hapus-kategori', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus kategori?',
                    text: 'Jenis di kategori ini harus sudah kosong.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }
                    $.ajax({
                        url: '{{ url('dapur/layanan-k3/master/kategori') }}/' + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            Swal.fire('Berhasil', response.message, 'success');
                            tabel.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire('Tidak bisa dihapus', (xhr.responseJSON && xhr.responseJSON
                                .message) || 'Gagal menghapus', 'warning');
                        }
                    });
                });
            });
        });
    </script>
@endpush
