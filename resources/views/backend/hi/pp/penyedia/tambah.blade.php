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
                                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="{{route('hi.pp.penyedia.index')}}">Peraturan Perusahaan</a></li>
                                    <li class="breadcrumb-item"><a href="#!">/ Tambah</a></li>
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
                                <div class="col-12">
                                    <h5 class="mb-3 mt-4">Tambah Pengajuan Peraturan Perusahaan</h5>
                                    <div class="bt-wizard" id="verticalwizard">
                                        <form id="form-pengajuan" enctype="multipart/form-data">
                                            @csrf

                                            <div class="row align-items-stretch mb-4">
                                                {{-- ================= Sidebar Tabs ================= --}}
                                                <div class="col-12 col-md-auto col-sm-12">
                                                    <div class="card h-100 mb-0">
                                                        <div class="card-body">
                                                            <ul class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                                                                <li class="nav-item">
                                                                    <a href="#tab-input" class="nav-link active" data-bs-toggle="tab">
                                                                        <i class="feather icon-edit-2 me-1"></i> Input
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a href="#tab-unggah" class="nav-link" data-bs-toggle="tab">
                                                                        <i class="feather icon-upload-cloud me-1"></i> Unggah
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a href="#tab-konfirmasi" class="nav-link" data-bs-toggle="tab">
                                                                        <i class="feather icon-check-circle me-1"></i> Konfirmasi
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ================= Tab Content ================= --}}
                                                <div class="col">
                                                    <div class="tab-content card mb-0" id="v-pills-tabContent">

                                                        {{-- ============ TAB 1: INPUT ============ --}}
                                                        <div class="tab-pane card-body show active" id="tab-input">
                                                            <h6 class="mb-3 text-muted">Data Pengajuan</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Jenis Ajuan <span class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <select name="jenis_ajuan" class="form-control" required>
                                                                        <option value="">-- Pilih Jenis Ajuan --</option>
                                                                        @foreach ($jenisAjuan as $j)
                                                                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Surat Keputusan Izin Usaha</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="surat_keputusan_izin_usaha" class="form-control" placeholder="Masukkan nomor SK izin usaha">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Nomor</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nomor" class="form-control" placeholder="Masukkan nomor pengajuan">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Tanggal</label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" name="tanggal" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Nama Serikat Pekerja</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nama_serikat_pekerja" class="form-control" placeholder="Contoh: SP FSPMI">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Nomor Peserta BPJS</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nomor_peserta_bpjs" class="form-control" placeholder="Masukkan nomor peserta BPJS">
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <h6 class="mb-3 text-muted">Jumlah Pekerja</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Jumlah Pekerja Pusat</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="jumlah_pekerja_pusat" class="form-control" min="0" value="0">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Jumlah Pekerja Cabang</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="jumlah_pekerja_cabang" class="form-control" min="0" value="0">
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <h6 class="mb-3 text-muted">Upah Pekerja (Rupiah)</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Bulanan Minimum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_bulanan_min" class="form-control" min="0" placeholder="0">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Bulanan Maximum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_bulanan_max" class="form-control" min="0" placeholder="0">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Harian Minimum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_harian_min" class="form-control" min="0" placeholder="0">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Harian Maximum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_harian_max" class="form-control" min="0" placeholder="0">
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <h6 class="mb-3 text-muted">Sistem Hubungan Kerja</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Waktu Tertentu (orang)</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="sistem_hub_kerja_tertentu" class="form-control" min="0" value="0">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Waktu Tidak Tertentu (orang)</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="sistem_hub_kerja_tidak_tertentu" class="form-control" min="0" value="0">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label"> <span class="badge badge-light-info">Link googledrive dokumen</span> Konsep Peraturan Perusahaan sebanyak 3 rangkap (tiap halaman wajib diparaf oleh manajemen perusahaan)</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="link_gdrive_dokumen8" class="form-control">
                                                                </div>
                                                            </div>

                                                           <div class="text-end mt-4">
                                                                <button type="button" class="btn btn-primary btn-next" data-target="#tab-unggah">
                                                                    Selanjutnya <i class="feather icon-arrow-right"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- ============ TAB 2: UNGGAH ============ --}}
                                                        <div class="tab-pane card-body" id="tab-unggah">
                                                            <h6 class="mb-3 text-muted">Unggah Syarat Dokumen</h6>

                                                            @forelse ($syaratDokumen as $i => $dok)
                                                                <div class="form-group row align-items-center">
                                                                    <label class="col-sm-6 col-form-label">
                                                                        {{ $i + 1 }}. {{ $dok->nama }}
                                                                    </label>
                                                                    <div class="col-sm-6">
                                                                        <input type="file"
                                                                            name="dokumen[{{ $dok->id }}]"
                                                                            class="form-control"
                                                                            accept=".pdf,application/pdf">
                                                                        <small class="text-muted">Format: PDF. Maksimal 1 MB.</small>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="alert alert-warning">Belum ada syarat dokumen yang terdaftar.</div>
                                                            @endforelse

                                                            <div class="d-flex justify-content-between mt-4">
                                                                <button type="button" class="btn btn-secondary btn-prev" data-target="#tab-input">
                                                                    <i class="feather icon-arrow-left"></i> Sebelumnya
                                                                </button>
                                                                <button type="button" class="btn btn-primary btn-next" data-target="#tab-konfirmasi">
                                                                    Selanjutnya <i class="feather icon-arrow-right"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- ============ TAB 3: KONFIRMASI ============ --}}
                                                        <div class="tab-pane card-body" id="tab-konfirmasi">
                                                            <div class="text-center mb-4">
                                                                <i class="feather icon-check-circle display-3 text-success"></i>
                                                                <h5 class="mt-3">Konfirmasi Pengajuan</h5>
                                                                <p class="text-muted">Pastikan semua data yang diisi sudah benar sebelum dikirim.</p>
                                                            </div>

                                                            <div class="alert alert-info">
                                                                <i class="feather icon-info"></i>
                                                                Dengan menekan tombol <strong>Kirim Pengajuan</strong>, Anda menyatakan
                                                                data yang diisi adalah benar dan dapat dipertanggungjawabkan.
                                                            </div>

                                                            <div class="d-flex justify-content-between mt-4">
                                                                <button type="button" class="btn btn-secondary btn-prev" data-target="#tab-unggah">
                                                                    <i class="feather icon-arrow-left"></i> Sebelumnya
                                                                </button>
                                                                <div>
                                                                    <a href="{{ route('hi.pp.penyedia.index') }}" class="btn btn-warning">Batal</a>
                                                                    <button type="submit" class="btn btn-success btn-submit">
                                                                        <i class="feather icon-send"></i> Kirim Pengajuan
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
    // $('#form-pengajuan').on('submit', function (e) {
    //     e.preventDefault();

    //     let formData = new FormData(this);
    //     let submitBtn = $('.btn-submit');
    //     let originalText = submitBtn.html();

    //     submitBtn.prop('disabled', true).html('<i class="feather icon-loader"></i> Mengirim...');

    //     $.ajax({
    //         url: "{{ route('hi.pp.penyedia.store') }}",
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function (res) {
    //             submitBtn.prop('disabled', false).html(originalText);
    //             if (res.status) {
    //                 Swal.fire('Berhasil', res.message, 'success').then(() => {
    //                     window.location.href = "{{ route('hi.pp.penyedia.index') }}";
    //                 });
    //             } else {
    //                 Swal.fire('Gagal', res.message, 'error');
    //             }
    //         },
    //         error: function (xhr) {
    //             submitBtn.prop('disabled', false).html(originalText);
    //             let msg = 'Terjadi kesalahan saat mengirim data.';
    //             if (xhr.status === 422 && xhr.responseJSON.errors) {
    //                 msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
    //             }
    //             Swal.fire('Validasi Gagal', msg, 'error');
    //         }
    //     });
    // });

    // Auto pindah ke tab berikutnya saat klik tombol next/prev
    // $('.btn-next, .btn-prev').on('click', function () {
    //     let target = $(this).attr('href');
    //     $('.nav-pills .nav-link').removeClass('active');
    //     $('.nav-pills .nav-link[href="' + target + '"]').addClass('active');
    // });

    // ============ NAVIGASI TAB ============
    $(document).on('click', '.btn-next, .btn-prev', function (e) {
        e.preventDefault();
        let target = $(this).data('target');
        let trigger = document.querySelector('.nav-pills .nav-link[href="' + target + '"]');
        if (trigger) {
            new bootstrap.Tab(trigger).show();
        }
    });

    // ============ VALIDASI FILE CLIENT-SIDE ============
    function validateFile(input) {
        let file = input.files[0];
        if (!file) return true;

        let maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            Swal.fire('File Terlalu Besar', 'Ukuran file maksimal 1 MB.', 'error');
            input.value = '';
            return false;
        }

        if (file.type !== 'application/pdf') {
            Swal.fire('Format Salah', 'File harus berformat PDF.', 'error');
            input.value = '';
            return false;
        }
        return true;
    }

    $(document).on('change', '.input-dokumen', function () {
        validateFile(this);
    });

    $('#form-pengajuan').on('submit', function (e) {
        e.preventDefault();

        // Validasi semua file dulu
        let valid = true;
        $('.input-dokumen').each(function () {
            if (!validateFile(this)) valid = false;
        });
        if (!valid) return;

        let formData = new FormData(this);
        let submitBtn = $('.btn-submit');
        let originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="feather icon-loader"></i> Mengirim...');

        $.ajax({
            url: $(this).attr('action') || "{{ route('hi.pp.penyedia.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                submitBtn.prop('disabled', false).html(originalText);
                if (res.status) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => {
                        window.location.href = res.redirect || "{{ route('hi.pp.penyedia.index') }}";
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                let msg = 'Terjadi kesalahan saat mengirim data.';
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

