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
                                    <h5 class="mb-3 mt-4">Edit Pengajuan Peraturan Perusahaan</h5>
                                    <div class="bt-wizard" id="verticalwizard">
                                        <form id="form-pengajuan" enctype="multipart/form-data"
                                            action="{{ route('hi.pp.penyedia.update', $ajuan->id) }}" method="POST">
                                            @csrf

                                            <div class="row align-items-stretch mb-4">
                                                {{-- ============ Sidebar Tabs ============ --}}
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

                                                {{-- ============ Tab Content ============ --}}
                                                <div class="col">
                                                    <div class="tab-content card mb-0" id="v-pills-tabContent">

                                                        {{-- ======== TAB 1: INPUT ======== --}}
                                                        <div class="tab-pane card-body show active" id="tab-input">
                                                            <h6 class="mb-3 text-muted">Data Pengajuan</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Jenis Ajuan <span class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <select name="jenis_ajuan" class="form-control" required>
                                                                        <option value="">-- Pilih Jenis Ajuan --</option>
                                                                        @foreach ($jenisAjuan as $j)
                                                                            <option value="{{ $j->id }}"
                                                                                {{ $ajuan->jenis_ajuan == $j->id ? 'selected' : '' }}>
                                                                                {{ $j->nama }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Surat Keputusan Izin Usaha</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="surat_keputusan_izin_usaha" class="form-control"
                                                                        value="{{ old('surat_keputusan_izin_usaha', $ajuan->surat_keputusan_izin_usaha) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Nomor</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nomor" class="form-control"
                                                                        value="{{ old('nomor', $ajuan->nomor) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Tanggal</label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" name="tanggal" class="form-control"
                                                                        value="{{ old('tanggal', $ajuan->tanggal ? \Carbon\Carbon::parse($ajuan->tanggal)->format('Y-m-d') : '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Nama Serikat Pekerja</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nama_serikat_pekerja" class="form-control"
                                                                        value="{{ old('nama_serikat_pekerja', $ajuan->nama_serikat_pekerja) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Nomor Peserta BPJS</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nomor_peserta_bpjs" class="form-control"
                                                                        value="{{ old('nomor_peserta_bpjs', $ajuan->nomor_peserta_bpjs) }}">
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <h6 class="mb-3 text-muted">Jumlah Pekerja</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Jumlah Pekerja Pusat</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="jumlah_pekerja_pusat" class="form-control" min="0"
                                                                        value="{{ old('jumlah_pekerja_pusat', $ajuan->jumlah_pekerja_pusat ?? 0) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Jumlah Pekerja Cabang</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="jumlah_pekerja_cabang" class="form-control" min="0"
                                                                        value="{{ old('jumlah_pekerja_cabang', $ajuan->jumlah_pekerja_cabang ?? 0) }}">
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <h6 class="mb-3 text-muted">Upah Pekerja (Rupiah)</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Bulanan Minimum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_bulanan_min" class="form-control" min="0"
                                                                        value="{{ old('upah_pekerja_bulanan_min', $ajuan->upah_pekerja_bulanan_min) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Bulanan Maximum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_bulanan_max" class="form-control" min="0"
                                                                        value="{{ old('upah_pekerja_bulanan_max', $ajuan->upah_pekerja_bulanan_max) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Harian Minimum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_harian_min" class="form-control" min="0"
                                                                        value="{{ old('upah_pekerja_harian_min', $ajuan->upah_pekerja_harian_min) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Upah Harian Maximum</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="upah_pekerja_harian_max" class="form-control" min="0"
                                                                        value="{{ old('upah_pekerja_harian_max', $ajuan->upah_pekerja_harian_max) }}">
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <h6 class="mb-3 text-muted">Sistem Hubungan Kerja</h6>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Waktu Tertentu (orang)</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="sistem_hub_kerja_tertentu" class="form-control" min="0"
                                                                        value="{{ old('sistem_hub_kerja_tertentu', $ajuan->sistem_hub_kerja_tertentu ?? 0) }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label">Waktu Tidak Tertentu (orang)</label>
                                                                <div class="col-sm-9">
                                                                    <input type="number" name="sistem_hub_kerja_tidak_tertentu" class="form-control" min="0"
                                                                        value="{{ old('sistem_hub_kerja_tidak_tertentu', $ajuan->sistem_hub_kerja_tidak_tertentu ?? 0) }}">
                                                                </div>
                                                            </div>

                                                            <div class="text-end mt-4">
                                                                <a href="#tab-unggah" class="btn btn-primary btn-next" data-bs-toggle="tab">
                                                                    Selanjutnya <i class="feather icon-arrow-right"></i>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        {{-- ======== TAB 2: UNGGAH ======== --}}
                                                        <div class="tab-pane card-body" id="tab-unggah">
                                                            <h6 class="mb-3 text-muted">Unggah Syarat Dokumen</h6>

                                                            @forelse ($syaratDokumen as $i => $dok)
                                                                @php
                                                                    $existing = $uploadedDokumen[$dok->id] ?? null;
                                                                @endphp
                                                                <div class="form-group row align-items-center">
                                                                    <label class="col-sm-6 col-form-label">
                                                                        {{ $i + 1 }}. {{ $dok->nama }}
                                                                    </label>
                                                                    <div class="col-sm-6">
                                                                        @if ($existing)
                                                                            <div class="mb-1">
                                                                                <a href="{{ asset('storage/' . $existing) }}" target="_blank" class="text-primary">
                                                                                    <i class="feather icon-file"></i> Lihat file saat ini
                                                                                </a>
                                                                            </div>
                                                                        @else
                                                                            <div class="mb-1 text-muted">Belum diunggah</div>
                                                                        @endif
                                                                        <input type="file"
                                                                            name="dokumen[{{ $dok->id }}]"
                                                                            class="form-control"
                                                                            accept=".pdf,application/pdf">
                                                                        <small class="text-muted">Kosongkan jika tidak ingin mengubah file.</small>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="alert alert-warning">Belum ada syarat dokumen yang terdaftar.</div>
                                                            @endforelse

                                                            <div class="d-flex justify-content-between mt-4">
                                                                <a href="#tab-input" class="btn btn-secondary btn-prev" data-bs-toggle="tab">
                                                                    <i class="feather icon-arrow-left"></i> Sebelumnya
                                                                </a>
                                                                <a href="#tab-konfirmasi" class="btn btn-primary btn-next" data-bs-toggle="tab">
                                                                    Selanjutnya <i class="feather icon-arrow-right"></i>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        {{-- ======== TAB 3: KONFIRMASI ======== --}}
                                                        <div class="tab-pane card-body" id="tab-konfirmasi">
                                                            <div class="text-center mb-4">
                                                                <i class="feather icon-check-circle display-3 text-success"></i>
                                                                <h5 class="mt-3">Konfirmasi Perubahan</h5>
                                                                <p class="text-muted">Pastikan semua data yang diubah sudah benar.</p>
                                                            </div>

                                                            <div class="alert alert-info">
                                                                <i class="feather icon-info"></i>
                                                                Tekan <strong>Simpan Perubahan</strong> untuk memperbarui data.
                                                            </div>

                                                            <div class="d-flex justify-content-between mt-4">
                                                                <a href="#tab-unggah" class="btn btn-secondary btn-prev" data-bs-toggle="tab">
                                                                    <i class="feather icon-arrow-left"></i> Sebelumnya
                                                                </a>
                                                                <div>
                                                                    <a href="{{ route('hi.pp.penyedia.index') }}" class="btn btn-light">
                                                                        Batal
                                                                    </a>
                                                                    <button type="submit" class="btn btn-success btn-submit">
                                                                        <i class="feather icon-save"></i> Simpan Perubahan
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
        $('#form-pengajuan').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            let submitBtn = $('.btn-submit');
            let originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="feather icon-loader"></i> Menyimpan...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    submitBtn.prop('disabled', false).html(originalText);
                    if (res.status) {
                        Swal.fire('Berhasil', res.message, 'success').then(() => {
                            window.location.href = "{{ route('hi.pp.penyedia.index') }}";
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalText);
                    let msg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire('Validasi Gagal', msg, 'error');
                }
            });
        });

        $('.btn-next, .btn-prev').on('click', function () {
            let target = $(this).attr('href');
            $('.nav-pills .nav-link').removeClass('active');
            $('.nav-pills .nav-link[href="' + target + '"]').addClass('active');
        });
    });
</script>
@endpush
