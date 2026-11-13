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
                                    <h5 class="m-b-10">Jenis objek K3</h5>
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
                                        <p class="text-muted mb-0">Master jenis alat/objek per kategori, dipakai di pengajuan
                                            dan LHPP.</p>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button type="button" class="btn btn-success btn-sm" id="btnTambahJenis">
                                            Tambah jenis
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tabelJenis" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kategori</th>
                                                <th>Nama jenis</th>
                                                <th>Keterangan</th>
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

        <div class="modal fade" id="modalJenis" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formJenis">
                        <div class="modal-header">
                            <h5 class="modal-title" id="judulJenis">Tambah jenis</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="jenisId">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" name="kategori_id" id="jenisKategori" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($kategoriList as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->nama }}
                                            @if ($kat->kode)
                                                ({{ $kat->kode }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama jenis <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" id="jenisNama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="jenisKeterangan" rows="3"></textarea>
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
            var tabel = $('#tabelJenis').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('pengawasan.k3.master.jenis.index') }}',
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kategori_nama' },
                    { data: 'nama' },
                    { data: 'keterangan' },
                    { data: 'options', orderable: false, searchable: false },
                ]
            });

            $('#btnTambahJenis').on('click', function() {
                $('#judulJenis').text('Tambah jenis');
                $('#jenisId').val('');
                $('#formJenis')[0].reset();
                $('#modalJenis').modal('show');
            });

            $(document).on('click', '.js-edit-jenis', function() {
                var id = $(this).data('id');
                $.get('{{ url('dapur/layanan-k3/master/jenis') }}/' + id, function(response) {
                    if (!response.success) {
                        Swal.fire('Gagal', 'Data tidak ditemukan', 'error');
                        return;
                    }
                    $('#judulJenis').text('Edit jenis');
                    $('#jenisId').val(response.data.id);
                    $('#jenisKategori').val(response.data.kategori_id);
                    $('#jenisNama').val(response.data.nama);
                    $('#jenisKeterangan').val(response.data.keterangan);
                    $('#modalJenis').modal('show');
                });
            });

            $('#formJenis').on('submit', function(e) {
                e.preventDefault();
                var id = $('#jenisId').val();
                var url = id
                    ? '{{ url('dapur/layanan-k3/master/jenis') }}/' + id
                    : '{{ route('pengawasan.k3.master.jenis.store') }}';
                $.ajax({
                    url: url,
                    type: id ? 'PUT' : 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        kategori_id: $('#jenisKategori').val(),
                        nama: $('#jenisNama').val(),
                        keterangan: $('#jenisKeterangan').val(),
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#modalJenis').modal('hide');
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

            $(document).on('click', '.js-hapus-jenis', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus jenis?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }
                    $.ajax({
                        url: '{{ url('dapur/layanan-k3/master/jenis') }}/' + id,
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
