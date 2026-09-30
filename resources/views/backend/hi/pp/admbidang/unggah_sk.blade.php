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
                                    <h5 class="m-b-10">Ajuan Peraturan Perusahaan</h5>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Unggah Dokumen SK Final</h5>
                                <a href="{{ route('hi.pp.admbidang.detail', $ajuan->id) }}" class="btn btn-light btn-sm">
                                    <i class="feather icon-arrow-left"></i> Kembali
                                </a>
                            </div>
                            <div class="card-body">

                                <div class="alert alert-info">
                                    <strong>Nomor SK:</strong> {{ $ajuan->nomor_sk ?? '-' }}<br>
                                    <strong>Tanggal Berlaku:</strong>
                                    {{ $ajuan->tanggal_berlaku_pp_baru ? \Carbon\Carbon::parse($ajuan->tanggal_berlaku_pp_baru)->format('d-m-Y') : '-' }}
                                </div>

                                @if ($ajuan->dok_produk_akhir)
                                    <div class="alert alert-success">
                                        <i class="feather icon-file"></i>
                                        Dokumen SK saat ini:
                                        <a href="{{ asset('storage/' . $ajuan->dok_produk_akhir) }}" target="_blank">
                                            Lihat dokumen
                                        </a>
                                    </div>
                                @endif

                                <form id="form-unggah-sk" action="{{ route('hi.pp.admbidang.submitUnggahSk', $ajuan->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Dokumen SK <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="file" name="dok_produk_akhir" class="form-control input-dokumen"
                                                accept=".pdf,application/pdf" required>
                                            <small class="text-muted">Format: PDF. Maksimal 5 MB.</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-4">
                                        <a href="{{ route('hi.pp.admbidang.detail', $ajuan->id) }}" class="btn btn-light">Batal</a>
                                        <button type="submit" class="btn btn-success ms-2 btn-submit">
                                            <i class="feather icon-upload-cloud"></i> Unggah
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- customar project  end -->


                </div>
                <!-- [ Main Content ] end -->


            </div>
        </div>

    </body>
@endsection


@push('js')
<script>
    $(function () {
    // Validasi file client-side
    function validateFile(input) {
        let file = input.files[0];
        if (!file) return true;

        if (file.size > 5 * 1024 * 1024) {
            Swal.fire('File Terlalu Besar', 'Ukuran maksimal 5 MB.', 'error');
            input.value = '';
            return false;
        }
        if (file.type !== 'application/pdf') {
            Swal.fire('Format Salah', 'File harus PDF.', 'error');
            input.value = '';
            return false;
        }
        return true;
    }

    $('.input-dokumen').on('change', function () { validateFile(this); });

    $('#form-unggah-sk').on('submit', function (e) {
        e.preventDefault();

        if (!validateFile($('.input-dokumen')[0])) return;

        let btn = $('.btn-submit');
        let original = btn.html();
        btn.prop('disabled', true).html('<i class="feather icon-loader"></i> Mengunggah...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                btn.prop('disabled', false).html(original);
                if (res.status) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => {
                        window.location.href = res.redirect;
                    });
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
});
</script>
@endpush

