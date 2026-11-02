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
                                    <h5 class="m-b-10">Struktur</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tipe</th>
                                                <th>Kode Lokasi</th>
                                                <th>Kode Bidang</th>
                                                <th>Nama</th>
                                                <th>Alamat Kop</th>
                                                <th>Slug</th>
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

        <div class="modal fade" id="modal-edit-struktur" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alamat Kop Struktur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editStrukturId">
                        <p class="mb-2"><strong id="editStrukturNama"></strong></p>
                        <p class="text-muted small">Tampil di kop surat/sertifikat. Contoh dua baris: alamat, lalu telepon dan pos-el.</p>
                        <textarea class="form-control" id="editStrukturAlamat" rows="4"
                            placeholder="Jalan Sepinggan Baru Nomor 31, Balikpapan, Kalimantan Timur 76115&#10;Telepon/Faksimile (0542) 762681; Pos-el blkibalikpapan@kaltimprov.go.id"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="simpanAlamatStruktur()">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        window.tabelStruktur = $('#simpletable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("struktur.index") }}',
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tipe_label' },
                { data: 'kode_lokasi' },
                { data: 'kode_bidang' },
                { data: 'nama' },
                { data: 'alamat_short', orderable: false },
                { data: 'slug' },
                { data: 'options', orderable: false, searchable: false },
            ]
        });
    });

    function showEditStruktur(id) {
        $.get("{{ url('dapur/setting/struktur') }}/" + id, function(response) {
            if (!response.success) {
                alert('Gagal memuat data struktur');
                return;
            }
            $('#editStrukturId').val(response.data.id);
            $('#editStrukturNama').text(response.data.nama);
            $('#editStrukturAlamat').val(response.data.alamat || '');
            $('#modal-edit-struktur').modal('show');
        }).fail(function() {
            alert('Gagal memuat data struktur');
        });
    }

    function simpanAlamatStruktur() {
        var id = $('#editStrukturId').val();
        $.ajax({
            url: "{{ url('dapur/setting/struktur') }}/" + id,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                alamat: $('#editStrukturAlamat').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-edit-struktur').modal('hide');
                    window.tabelStruktur.ajax.reload(null, false);
                } else {
                    alert(response.message || 'Gagal menyimpan alamat');
                }
            },
            error: function() {
                alert('Gagal menyimpan alamat');
            }
        });
    }
</script>
@endpush
