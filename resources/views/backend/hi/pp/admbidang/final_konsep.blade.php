@extends('backend.template.backend')

@section('title', 'Final Konsep PP')

@section('content')
<body class="box-layout container background-green">
    <div class="pcoded-main-container">
        <div class="pcoded-content">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Unggah Dokumen Final Konsep PP</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Unggah Dokumen Final Konsep Peraturan Perusahaan</h5>
                            <a href="{{ route('hi.pp.admbidang.detail', $ajuan->id) }}" class="btn btn-light btn-sm">
                                <i class="feather icon-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">

                            <div class="alert alert-info">
                                <strong>Nomor:</strong> {{ $ajuan->nomor ?? '-' }} &nbsp;|&nbsp;
                                <strong>Jenis:</strong> {{ $ajuan->jenisAjuan->nama ?? '-' }}
                            </div>

                            <div class="alert alert-warning">
                                <i class="feather icon-alert-triangle"></i>
                                Setelah dokumen final konsep PP diunggah, status ajuan akan berubah menjadi
                                <strong>Selesai</strong> dan tidak dapat diverifikasi ulang.
                            </div>

                            @if ($ajuan->dok_final_konsep_pp)
                                <div class="alert alert-success">
                                    <i class="feather icon-file"></i>
                                    Dokumen final konsep PP saat ini:
                                    <a href="{{ asset('storage/' . $ajuan->dok_final_konsep_pp) }}" target="_blank">
                                        Lihat dokumen
                                    </a>
                                </div>
                            @endif

                            <form id="form-final-konsep"
                                  action="{{ route('hi.pp.admbidang.submitFinalKonsep', $ajuan->id) }}"
                                  method="POST"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">
                                        Dokumen Final Konsep PP <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="file" name="dok_final_konsep_pp" id="dok_final_konsep_pp"
                                               class="form-control input-dokumen"
                                               accept=".pdf,application/pdf" required>
                                        <small class="text-muted">Format: PDF. Maksimal 10 MB.</small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('hi.pp.admbidang.detail', $ajuan->id) }}" class="btn btn-light">Batal</a>
                                    <button type="submit" class="btn btn-dark ms-2 btn-submit">
                                        <i class="feather icon-upload-cloud"></i> Unggah & Selesaikan
                                    </button>
                                </div>
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
<script>
console.log('✅ final_konsep script loaded');

$(function () {
    console.log('✅ jQuery ready — binding form...');

    // Validasi file client-side
    function validateFile(input) {
        let file = input.files[0];
        if (!file) return true;

        if (file.size > 10 * 1024 * 1024) {
            alert('File terlalu besar. Maksimal 10 MB.');
            input.value = '';
            return false;
        }
        if (file.type !== 'application/pdf') {
            alert('Format salah. File harus PDF.');
            input.value = '';
            return false;
        }
        return true;
    }

    $(document).on('change', '.input-dokumen', function () {
        validateFile(this);
    });

    // Submit via AJAX
    $(document).on('submit', '#form-final-konsep', function (e) {
        e.preventDefault();
        console.log('📤 Form submit triggered');

        let form = this;
        let fileInput = form.querySelector('.input-dokumen');

        if (!validateFile(fileInput)) return;

        let formData = new FormData(form); // ← pakai `form`, bukan `this`
        let btn = $(form).find('.btn-submit');
        let original = btn.html();

        btn.prop('disabled', true).html('<i class="feather icon-loader"></i> Mengunggah...');

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
                console.log('✅ Response:', res);
                btn.prop('disabled', false).html(original);

                if (res.status) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil', res.message, 'success').then(function () {
                            window.location.href = res.redirect;
                        });
                    } else {
                        alert(res.message);
                        window.location.href = res.redirect;
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', res.message, 'error');
                    } else {
                        alert('Gagal: ' + res.message);
                    }
                }
            },
            error: function (xhr) {
                console.error('❌ AJAX error:', xhr.status, xhr.responseText);
                btn.prop('disabled', false).html(original);

                let msg = 'Terjadi kesalahan (HTTP ' + xhr.status + ').';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal', msg, 'error');
                } else {
                    alert(msg);
                }
            }
        });
    });
});
</script>
@endpush
