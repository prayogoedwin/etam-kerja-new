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
                                    <h5 class="m-b-10">Detail Ajuan Peraturan Perusahaan</h5>
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
                                <h5 class="mb-0">Verifikasi Kasi</h5>
                                <a href="{{ route('hi.pp.kasibidang.detail', $ajuan->id) }}" class="btn btn-light btn-sm">
                                    <i class="feather icon-arrow-left"></i> Kembali ke Detail
                                </a>
                            </div>
                            <div class="card-body">

                                <div class="alert alert-info">
                                    <strong>Nomor:</strong> {{ $ajuan->nomor ?? '-' }} &nbsp;|&nbsp;
                                    <strong>Jenis:</strong> {{ $ajuan->jenisAjuan->nama ?? '-' }} &nbsp;|&nbsp;
                                    <strong>Tanggal:</strong> {{ $ajuan->tanggal ? \Carbon\Carbon::parse($ajuan->tanggal)->format('d-m-Y') : '-' }}
                                </div>

                                <form id="form-verifikasi" action="{{ route('hi.pp.kasibidang.verifikasi.submit', $ajuan->id) }}" method="POST">
                                    @csrf

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Keputusan <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="type" id="type-acc" value="acc"
                                                    {{ (int) $ajuan->verifikasi_kasi === 1 ? 'checked' : '' }} required>
                                                <label class="form-check-label text-success" for="type-acc">
                                                    <strong>Setujui (ACC)</strong>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="type" id="type-revisi" value="revisi"
                                                    {{ (int) $ajuan->verifikasi_kasi === 2 ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="type-revisi">
                                                    <strong>Tolak / Revisi</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="row-revisi" style="display: none;">

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">
                                                Keterangan Revisi <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-9">
                                                <textarea name="keterangan" id="keterangan" class="form-control" rows="4"
                                                        maxlength="255"
                                                        placeholder="Jelaskan alasan revisi...">{{ old('keterangan', $ajuan->keterangan_revisi_kasi) }}</textarea>
                                                <small class="text-muted">Maksimal 255 karakter.</small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">
                                                Batas Revisi <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-9">
                                                <input type="date" name="batas_revisi" id="batas_revisi" class="form-control"
                                                    value="{{ old('batas_revisi', $ajuan->batas_revisi ? \Carbon\Carbon::parse($ajuan->batas_revisi)->format('Y-m-d') : '') }}"
                                                    min="{{ date('Y-m-d') }}">
                                                <small class="text-muted">Tanggal batas akhir revisi (tidak boleh sebelum hari ini).</small>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="d-flex justify-content-end mt-4">
                                        <a href="{{ route('hi.pp.kasibidang.detail', $ajuan->id) }}" class="btn btn-light">
                                            Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary ms-2 btn-submit">
                                            <i class="feather icon-save"></i> Simpan Keputusan
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
    function toggleRevisi() {
        let val = $('input[name="type"]:checked').val();
        if (val === 'revisi') {
            $('#row-revisi').show();
            $('#keterangan').prop('required', true);
            $('#batas_revisi').prop('required', true);
        } else {
            $('#row-revisi').hide();
            $('#keterangan').prop('required', false);
            $('#batas_revisi').prop('required', false);
        }
    }

    $('input[name="type"]').on('change', toggleRevisi);
    toggleRevisi();

    $('#form-verifikasi').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let btn = $('.btn-submit');
        let originalText = btn.html();

        btn.prop('disabled', true).html('<i class="feather icon-loader"></i> Memproses...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                btn.prop('disabled', false).html(originalText);
                if (res.status) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => {
                        window.location.href = res.redirect;
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html(originalText);
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

