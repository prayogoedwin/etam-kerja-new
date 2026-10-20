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
                                    <h5 class="m-b-10">Detail Permohonan & Disposisi Kabid</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('pengawasan.k3.kabid.index') }}">Kembali ke Daftar</a></li>
                                    <li class="breadcrumb-item">Detail Ajuan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Informasi Permohonan & Catatan Kadis -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h5>Informasi & Catatan Kadis</h5></div>
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
                                        <th>Catatan Kadis</th>
                                        <td>: <span class="badge bg-warning">{{ $ajuan->catatan_disposisi_kadis ?? 'Tidak ada catatan' }}</span></td>
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

                    <!-- Panel Disposisi Kabid -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h5>Tindakan Kepala Bidang (Kabid)</h5></div>
                            <div class="card-body">
                                <div class="alert alert-primary" role="alert">
                                    Berikan catatan disposisi dan teruskan permohonan ini kepada Kepala Seksi (Kasi).
                                </div>

                                <form id="formDisposisiKabid">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Catatan Disposisi Kabid</label>
                                        <textarea name="catatan_disposisi" class="form-control" rows="4" placeholder="Masukkan instruksi ke Kasi...">{{ $ajuan->catatan_disposisi_kabid }}</textarea>
                                    </div>

                                    @if($ajuan->is_kabid_dispo == 0)
                                        <button type="button" id="btnDisposisiKabid" class="btn btn-success w-100">
                                            <i class="feather icon-send"></i> Teruskan Disposisi ke Kasi
                                        </button>
                                    @else
                                        <div class="alert alert-success text-center mb-0">
                                            <i class="feather icon-check-circle"></i> Sudah didisposisi pada {{ $ajuan->kabid_dispo_at }}
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
    $('#btnDisposisiKabid').on('click', function() {
        var formData = $('#formDisposisiKabid').serialize();

        Swal.fire({
            title: 'Konfirmasi Disposisi Kabid',
            text: "Teruskan disposisi ini ke Kepala Seksi (Kasi)?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Teruskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('pengawasan.k3.kabid.disposisi', $ajuan->id) }}',
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
