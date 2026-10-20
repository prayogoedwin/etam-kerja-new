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
                                    <h5 class="m-b-10">Detail Ajuan PJK3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('backend.pengawasan.pjk3._detail')

                @if ($canVerify)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Verifikasi</h5>
                        </div>
                        <div class="card-body">
                            <form id="formVerifikasi">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status-acc"
                                                value="acc">
                                            <label class="form-check-label text-success" for="status-acc">ACC</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status"
                                                id="status-revisi" value="revisi">
                                            <label class="form-check-label text-warning"
                                                for="status-revisi">Revisi</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status-tolak"
                                                value="tolak">
                                            <label class="form-check-label text-danger" for="status-tolak">Tolak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3" id="row-masa" style="display:none;">
                                    <label class="form-label">Masa berlaku <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="masa_berlaku">
                                </div>
                                <div class="mb-3" id="row-alasan">
                                    <label class="form-label">Alasan</label>
                                    <textarea class="form-control" name="alasan" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                            </form>
                        </div>
                    </div>
                @endif

                <a href="{{ route('pengawasan.pjk3.admin.index') }}" class="btn btn-secondary mb-4">Kembali</a>
            </div>
        </div>
    </body>
@endsection

@push('js')
    @if ($canVerify)
        <script>
            $(document).ready(function() {
                $('input[name="status"]').on('change', function() {
                    $('#row-masa').toggle($(this).val() === 'acc');
                });

                $('#formVerifikasi').on('submit', function(e) {
                    e.preventDefault();
                    var form = this;
                    var status = $('input[name="status"]:checked').val();
                    if (!status) {
                        Swal.fire('Belum lengkap', 'Pilih keputusan verifikasi terlebih dahulu.',
                            'warning');
                        return;
                    }

                    var teks = {
                        acc: 'Apakah Anda yakin menyetujui (ACC) pengajuan PJK3 ini?',
                        revisi: 'Apakah Anda yakin mengembalikan pengajuan ini untuk direvisi?',
                        tolak: 'Apakah Anda yakin menolak pengajuan PJK3 ini?'
                    };

                    Swal.fire({
                        title: 'Konfirmasi verifikasi',
                        text: teks[status] + ' Pastikan keputusan yang dipilih sudah benar.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, simpan',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (!result.isConfirmed) {
                            return;
                        }

                        $.ajax({
                            url: '{{ route('pengawasan.pjk3.admin.verifikasi', $ajuan->id) }}',
                            type: 'POST',
                            data: $(form).serialize(),
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil', response.message, 'success')
                                        .then(function() {
                                            window.location.href = response.redirect;
                                        });
                                } else if (response.errors) {
                                    var msg = '';
                                    $.each(response.errors, function(_, value) {
                                        $.each(value, function(_, errorMessage) {
                                            msg += errorMessage + '\n';
                                        });
                                    });
                                    Swal.fire('Periksa kembali', msg, 'warning');
                                } else {
                                    Swal.fire('Gagal', response.message ||
                                        'Gagal memproses', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON
                                    .message) || 'Gagal memproses', 'error');
                            }
                        });
                    });
                });
            });
        </script>
    @endif
@endpush
