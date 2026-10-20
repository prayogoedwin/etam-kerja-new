@extends('backend.template.backend')

@section('content')
    <body class="box-layout container background-green">
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Detail Permohonan & Disposisi K3</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('pengawasan.k3.kadis.index') }}">Kembali ke Daftar</a></li>
                                    <li class="breadcrumb-item">Detail Ajuan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->

                <!-- [ Main Content ] start -->
                <div class="row">
                    <!-- Informasi Permohonan -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h5>Informasi Permohonan</h5></div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="35%">Perusahaan</th>
                                        <td>: {{ $ajuan->penyedia->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori K3</th>
                                        <td>: {{ $ajuan->kategori->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Alat</th>
                                        <td>: {{ $ajuan->jenis->nama ?? '-' }}</td>
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
                                        <th>Kapasitas / Unit</th>
                                        <td>: {{ $ajuan->kapasitas_alat }} ({{ $ajuan->jumlah_unit }} Unit)</td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td>: {{ $ajuan->keterangan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dokumen Pemohon</th>
                                        <td>:
                                            @if($ajuan->dok_unggah_penyedia)
                                                <a href="{{ asset('storage/' . $ajuan->dok_unggah_penyedia) }}" target="_blank" class="btn btn-sm btn-info">
                                                    Lihat Dokumen
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

                    <!-- Panel Disposisi & Aksi Kadis -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h5>Tindakan Kepala Dinas</h5></div>
                            <div class="card-body">
                                <div class="alert alert-primary" role="alert">
                                    Sesuai SOP, Kepala Dinas memberikan disposisi kepada Kabid untuk diteruskan ke Kasi dan Pengawas.
                                </div>

                                <form id="formDisposisi">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Catatan Disposisi / Arahan</label>
                                        <textarea name="catatan_disposisi_kadis" class="form-control" rows="4" placeholder="Masukkan instruksi disposisi kepada Kabid..."></textarea>
                                    </div>

                                    @if($ajuan->is_kadis_dispo == 0)
                                        <button type="button" id="btnDisposisi" class="btn btn-success w-100">
                                            <i class="feather icon-send"></i> Berikan Disposisi ke Kabid
                                        </button>
                                    @else
                                        <div class="alert alert-success text-center mb-0">
                                            <i class="feather icon-check-circle"></i> Permohonan sudah didisposisi pada {{ $ajuan->kadis_dispo_at }}
                                        </div>
                                    @endif
                                </form>

                                <!-- Bagian Persetujuan Akhir / TTE (Tahap Akhir SOP) -->
                                {{-- <hr class="my-4">
                                <h6>Persetujuan Akhir Surat Keterangan</h6>
                                <p class="text-muted small">Fitur ini digunakan pada tahap akhir apabila laporan pengujian (LHPP) dan verifikasi berjenjang telah selesai.</p>
                                <button type="button" id="btnSetujuSK" class="btn btn-primary w-100">
                                    <i class="feather icon-check"></i> Setujui & Tanda Tangan Elektronik (TTE) SK
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Main Content ] end -->
            </div>
        </div>
    </body>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Aksi Disposisi
    $('#btnDisposisi').on('click', function() {
        var formData = $('#formDisposisi').serialize();

        Swal.fire({
            title: 'Konfirmasi Disposisi',
            text: "Apakah Anda yakin ingin memberikan disposisi permohonan ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Disposisi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('pengawasan.k3.kadis.disposisi', $ajuan->id) }}',
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

    // Aksi Persetujuan Akhir SK / TTE
    $('#btnSetujuSK').on('click', function() {
        Swal.fire({
            title: 'Persetujuan Akhir & TTE',
            text: "Apakah Anda yakin ingin menyetujui dan menerbitkan Surat Keterangan Layak K3?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui & TTE!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('pengawasan.k3.kadis.setujui_sk', $ajuan->id) }}',
                    type: 'POST',
                    data: { "_token": "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menyetujui surat.', 'error');
                    }
                });
            }
        });
    });
</script>
@endpush
