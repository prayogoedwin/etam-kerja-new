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
                                    <h5 class="m-b-10">Penerbitan Surat Perintah Tugas (SPT)</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('pengawasan.k3.admin_bidang.index') }}">Kembali ke Daftar</a></li>
                                    <li class="breadcrumb-item">Form SPT</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Ringkasan Permohonan -->
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header"><h5>Informasi Pengajuan</h5></div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="35%">Perusahaan</th>
                                        <td>: {{ $ajuan->penyedia->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Alat</th>
                                        <td>: {{ $ajuan->nama_alat }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lokasi Alat</th>
                                        <td>: {{ $ajuan->lokasi_alat }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pengawas Ditunjuk</th>
                                        <td>: <span class="badge bg-info">{{ $ajuan->pengawas->name ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Catatan Kasi</th>
                                        <td>: {{ $ajuan->catatan_disposisi_kasi ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Form Pembuatan SPT -->
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header"><h5>Form Input Surat Perintah Tugas (SPT)</h5></div>
                            <div class="card-body">
                                <form id="formSpt">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Nomor SPT <span class="text-danger">*</span></label>
                                        <input type="text" name="nomor_spt" class="form-control" value="{{ optional($ajuan->spt->first())->nomor_spt }}" placeholder="Contoh: SPT/500/.../K3" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Tanggal Tugas <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_tugas" class="form-control" value="{{ optional($ajuan->spt->first())->tanggal_tugas ? optional($ajuan->spt->first())->tanggal_tugas->format('Y-m-d') : date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Lokasi Pelaksanaan Riksa Uji <span class="text-danger">*</span></label>
                                        <input type="text" name="lokasi" class="form-control" value="{{ optional($ajuan->spt->first())->lokasi ?? $ajuan->lokasi_alat }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Uraian Tugas <span class="text-danger">*</span></label>
                                        <textarea name="uraian_tugas" class="form-control" rows="4" required>{{ optional($ajuan->spt->first())->uraian_tugas ?? 'Melakukan pemeriksaan dan pengujian (riksa uji) objek K3 sesuai dengan peraturan perundang-undangan.' }}</textarea>
                                    </div>

                                    <button type="button" id="btnSimpanSpt" class="btn btn-success w-100">
                                        <i class="feather icon-save"></i> Simpan & Terbitkan SPT
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#btnSimpanSpt').on('click', function() {
        var formData = $('#formSpt').serialize();

        Swal.fire({
            title: 'Simpan Surat Perintah Tugas',
            text: "Pastikan data SPT sudah benar sebelum diterbitkan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('pengawasan.k3.admin_bidang.store_spt', $ajuan->id) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                window.location.href = '{{ route('pengawasan.k3.admin_bidang.index') }}';
                            });
                        }
                    },
                    error: function(xhr) {
                        var errMessage = 'Terjadi kesalahan saat menyimpan SPT.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal!', errMessage, 'error');
                    }
                });
            }
        });
    });
</script>
@endpush