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
                                    <h5 class="m-b-10">Detail Permohonan & Disposisi Kasi</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('pengawasan.k3.kasi.index') }}">Kembali ke Daftar</a></li>
                                    <li class="breadcrumb-item">Detail Ajuan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Informasi Permohonan & Catatan Kabid -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h5>Informasi & Catatan Kabid</h5></div>
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
                                        <th>Catatan Kabid</th>
                                        <td>: <span class="text-primary font-weight-bold">{{ $ajuan->catatan_disposisi_kabid ?? 'Tidak ada catatan' }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Dokumen Pemohon</th>
                                        <td>:
                                            @if($ajuan->dok_unggah_penyedia)
                                                <a href="{{ asset('storage/' . $ajuan->dok_unggah_penyedia) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="feather icon-download"></i> Unduh Dokumen
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak ada dokumen</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Disposisi Kasi -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h5>Tindakan Kepala Seksi (Kasi)</h5></div>
                            <div class="card-body">
                                <div class="alert alert-primary" role="alert">
                                    Berikan instruksi penugasan dan teruskan permohonan ini kepada Pengawas Ketenagakerjaan.
                                </div>

                                <form id="formDisposisiKasi">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Catatan / Arahan Kasi</label>
                                        <textarea name="catatan_disposisi" class="form-control" rows="4" placeholder="Masukkan instruksi untuk Pengawas...">{{ $ajuan->catatan_disposisi_kasi }}</textarea>
                                    </div>

                                    @if($ajuan->is_kasi_dispo == 0)
                                        <button type="button" id="btnDisposisiKasi" class="btn btn-success w-100">
                                            <i class="feather icon-send"></i> Teruskan ke Pengawas Ketenagakerjaan
                                        </button>
                                    @else
                                        <div class="alert alert-success text-center mb-0">
                                            <i class="feather icon-check-circle"></i> Sudah didisposisi pada {{ $ajuan->kasi_dispo_at }}
                                        </div>
                                    @endif
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
    $('#btnDisposisiKasi').on('click', function() {
        var formData = $('#formDisposisiKasi').serialize();

        Swal.fire({
            title: 'Konfirmasi Disposisi Kasi',
            text: "Teruskan penugasan ini ke Pengawas Ketenagakerjaan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Teruskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('pengawasan.k3.kasi.disposisi', $ajuan->id) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat memproses disposisi.', 'error');
                    }
                });
            }
        });
    });
</script>
@endpush
