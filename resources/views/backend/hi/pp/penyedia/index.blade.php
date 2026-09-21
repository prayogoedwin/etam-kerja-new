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
                                    <h5 class="m-b-10">Peraturan Perusahaan</h5>
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
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">

                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <a href="{{route('hi.pp.penyedia.tambah')}}" class="btn btn-success btn-sm"><i class="feather icon-plus"></i> Add Data</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <th>No</th>
                                            <th>Jenis Ajuan</th>
                                            <th>Nomor</th>
                                            <th>Tanggal</th>
                                            <th>Status Admin</th>
                                            <th>Status Kasi</th>
                                            <th>Options</th>
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


        <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="registerForm">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="floating-label" for="pertanyaan">Pertanyaan</label>
                                        <textarea class="form-control" id="pertanyaan"  name="pertanyaan" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="floating-label" for="jawaban">Jawaban</label>
                                        <textarea class="form-control" id="jawaban" name="jawaban" rows="3"></textarea>
                                    </div>
                                    <button class="btn btn-primary">Submit</button>
                                    <button class="btn btn-danger">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAdminForm">
                            <input type="hidden" id="editId">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="pertanyaan">Pertanyaan</label>
                                    <textarea class="form-control" id="editPertanyaan"  name="pertanyaan" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="jawaban">Jawaban</label>
                                    <textarea class="form-control" id="editJawaban" name="jawaban" rows="3"></textarea>
                                </div>
                                <button class="btn btn-primary" onclick="updateFaq()" type="button">Submit</button>

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ MODAL EDIT ============ --}}
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="form-edit" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="edit-id" name="id">

                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <a href="#edit-tab-input" class="nav-link active" data-bs-toggle="tab">Input</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#edit-tab-unggah" class="nav-link" data-bs-toggle="tab">Unggah</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                {{-- TAB INPUT --}}
                                <div class="tab-pane fade show active" id="edit-tab-input">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Jenis Ajuan <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <select id="edit-jenis-ajuan" name="jenis_ajuan" class="form-control" required></select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Surat Keputusan Izin Usaha</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="edit-surat" name="surat_keputusan_izin_usaha" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Nomor</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="edit-nomor" name="nomor" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Tanggal</label>
                                        <div class="col-sm-8">
                                            <input type="date" id="edit-tanggal" name="tanggal" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Nama Serikat Pekerja</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="edit-serikat" name="nama_serikat_pekerja" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Nomor Peserta BPJS</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="edit-bpjs" name="nomor_peserta_bpjs" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Jumlah Pekerja Pusat</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-pusat" name="jumlah_pekerja_pusat" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Jumlah Pekerja Cabang</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-cabang" name="jumlah_pekerja_cabang" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Upah Bulanan Min</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-ubmin" name="upah_pekerja_bulanan_min" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Upah Bulanan Max</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-ubmax" name="upah_pekerja_bulanan_max" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Upah Harian Min</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-uhmin" name="upah_pekerja_harian_min" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Upah Harian Max</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-uhmax" name="upah_pekerja_harian_max" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Sistem Kerja Waktu Tertentu</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-shktertentu" name="sistem_hub_kerja_tertentu" class="form-control" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Sistem Kerja Waktu Tidak Tertentu</label>
                                        <div class="col-sm-8">
                                            <input type="number" id="edit-shktidak" name="sistem_hub_kerja_tidak_tertentu" class="form-control" min="0">
                                        </div>
                                    </div>
                                </div>

                                {{-- TAB UNGGAH --}}
                                <div class="tab-pane fade" id="edit-tab-unggah">
                                    <div id="edit-dokumen-list">
                                        {{-- diisi via JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-update">
                                <i class="feather icon-save"></i> Simpan Perubahan
                            </button>
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
        $('#simpletable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('hi.pp.penyedia.index') }}',
            autoWidth: false, // Menonaktifkan auto-width
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenis_ajuan_nama', name: 'jenis_ajuan_nama' },
                { data: 'nomor', name: 'nomor' },
                { data: 'tanggal_fmt', name: 'tanggal_fmt' },
                { data: 'status_admin', name: 'status_admin', orderable: false, searchable: false },
                { data: 'status_kasi',  name: 'status_kasi',  orderable: false, searchable: false },
                { data: 'options',      name: 'options',      orderable: false, searchable: false }
            ]
        });
    });
</script>

<script>
   $(document).ready(function () {
        $('#registerForm').submit(function (e) {
            e.preventDefault(); // Prevent form from submitting normally

            // Clear previous error messages
            $('#errorMessages').html('').addClass('d-none');

            var formData = {
                pertanyaan: $('#pertanyaan').val(),
                jawaban: $('#jawaban').val(),
                _token: '{{ csrf_token() }}' // Add CSRF token for security
            };

            $.ajax({
                type: 'POST',
                url: '{{ route('faq.add') }}', // Ganti dengan rute yang sesuai
                data: formData,
                success: function (response) {
                    if (response.success) {
                        alert('Berhasil menambahkan data');
                        $('#modal-report').modal('hide');
                        location.reload(); // Refresh halaman
                    } else {
                        // If validation errors are found, display them in an alert
                        if (response.errors) {
                            let errorMessages = '';
                            $.each(response.errors, function (key, value) {
                                $.each(value, function (index, errorMessage) {
                                    errorMessages += errorMessage + '\n'; // Gabungkan pesan error
                                });
                            });
                            alert('Terjadi kesalahan:\n' + errorMessages);
                        } else {
                            alert('Gagal menambahkan data');
                        }
                    }
                },
                error: function (xhr, status, error) {
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        });
    });
</script>

{{-- <script>
    window.showEditModal = function (id) {
        $.ajax({
            url: "{{ url('dapur/penyedias/peraturan-perusahaan/edit') }}/" + id,
            type: 'GET',
            success: function (res) {
                if (!res.status) {
                    Swal.fire('Gagal', res.message || 'Data tidak ditemukan', 'error');
                    return;
                }

                let d = res.data;

                // Set hidden id
                $('#edit-id').val(d.id);

                // Isi dropdown jenis ajuan
                let opts = '<option value="">-- Pilih Jenis Ajuan --</option>';
                res.jenis_ajuan_options.forEach(function (j) {
                    let sel = (d.jenis_ajuan == j.id) ? 'selected' : '';
                    opts += `<option value="${j.id}" ${sel}>${j.nama}</option>`;
                });
                $('#edit-jenis-ajuan').html(opts);

                // Isi field
                $('#edit-surat').val(d.surat_keputusan_izin_usaha || '');
                $('#edit-nomor').val(d.nomor || '');
                $('#edit-tanggal').val(d.tanggal ? d.tanggal.substring(0, 10) : '');
                $('#edit-serikat').val(d.nama_serikat_pekerja || '');
                $('#edit-bpjs').val(d.nomor_peserta_bpjs || '');
                $('#edit-pusat').val(d.jumlah_pekerja_pusat || 0);
                $('#edit-cabang').val(d.jumlah_pekerja_cabang || 0);
                $('#edit-ubmin').val(d.upah_pekerja_bulanan_min || '');
                $('#edit-ubmax').val(d.upah_pekerja_bulanan_max || '');
                $('#edit-uhmin').val(d.upah_pekerja_harian_min || '');
                $('#edit-uhmax').val(d.upah_pekerja_harian_max || '');
                $('#edit-shktertentu').val(d.sistem_hub_kerja_tertentu || 0);
                $('#edit-shktidak').val(d.sistem_hub_kerja_tidak_tertentu || 0);

                // Render list dokumen dinamis
                let html = '';
                res.syarat_dokumen_options.forEach(function (s, i) {
                    let uploaded = res.uploaded_dokumen[s.id];
                    let link = uploaded
                        ? `<a href="{{ asset('storage') }}/${uploaded}" target="_blank" class="text-primary">
                            <i class="feather icon-file"></i> Lihat file
                        </a>`
                        : `<span class="text-muted">Belum diunggah</span>`;

                    html += `
                        <div class="form-group row align-items-center">
                            <label class="col-sm-6 col-form-label">${i + 1}. ${s.nama}</label>
                            <div class="col-sm-6">
                                <div class="mb-1">${link}</div>
                                <input type="file" name="dokumen[${s.id}]" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </div>
                        </div>`;
                });
                $('#edit-dokumen-list').html(html || '<div class="alert alert-warning">Belum ada syarat dokumen.</div>');

                // Tampilkan modal
                $('#modalEdit').modal('show');
            },
            error: function () {
                Swal.fire('Error', 'Gagal mengambil data.', 'error');
            }
        });
    };

    // ============ SUBMIT UPDATE ============
    $('#form-edit').on('submit', function (e) {
        e.preventDefault();

        let id = $('#edit-id').val();
        let formData = new FormData(this);
        let btn = $('.btn-update');
        let original = btn.html();

        btn.prop('disabled', true).html('<i class="feather icon-loader"></i> Menyimpan...');

        $.ajax({
            url: "{{ url('dapur/penyedias/peraturan-perusahaan/update') }}/" + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                btn.prop('disabled', false).html(original);
                if (res.status) {
                    $('#modalEdit').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#simpletable').DataTable().ajax.reload();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html(original);
                let msg = 'Terjadi kesalahan.';
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Validasi Gagal', msg, 'error');
            }
        });
    });

    // ============ CONFIRM DELETE ============
    window.confirmDelete = function (id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('dapur/penyedias/peraturan-perusahaan/destroy') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'DELETE'
                    },
                    success: function (res) {
                        if (res.status) {
                            Swal.fire('Terhapus', res.message, 'success');
                            $('#simpletable').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    };
</script> --}}




{{-- <script>
    function confirmDelete(id) {
        // Konfirmasi penghapusan
        var deleteUrl = "{{ route('faq.softdelete', ':id') }}".replace(':id', id);
        if (confirm("Yakin hapus data?")) {
            // Kirim request ke server untuk menghapus data
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),  // Menyertakan CSRF token
                },
                success: function(response) {
                    // Jika berhasil, reload DataTable
                    alert(response.message);  // Menampilkan pesan
                    $('#simpletable').DataTable().ajax.reload();  // Reload data tabel
                },
                error: function(xhr, status, error) {
                    // Tampilkan error jika ada masalah
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    }
</script> --}}

@endpush

