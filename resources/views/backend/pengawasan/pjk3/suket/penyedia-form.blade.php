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
                                    <h5 class="m-b-10">Pengajuan Suket Layak K3 oleh PJK3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    Akun perusahaan punya dua peran.
                                    <b>PJK3</b> (setelah pendaftaran ACC) mengajukan Suket Layak K3.
                                    <b>Perusahaan yang diperiksa</b> cukup punya akun; mereka menerima draft LHPP dan Suket.
                                </div>
                                <form id="formSuket" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Perusahaan yang diperiksa <span
                                                    class="text-danger">*</span></label>
                                            <select name="perusahaan_id" class="form-control" required>
                                                <option value="">Pilih perusahaan yang sudah punya akun</option>
                                                @foreach ($perusahaanList as $p)
                                                    <option value="{{ $p->user_id }}">{{ $p->name }}
                                                        @if ($p->nib)
                                                            (NIB {{ $p->nib }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Daftar ini semua perusahaan berakun, bukan hanya PJK3.
                                                Mereka yang akan menerima draft Suket Layak K3.</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Kategori K3 <span
                                                    class="text-danger">*</span></label>
                                            <select name="kategori_id" id="kategori_id" class="form-control" required>
                                                <option value="">Pilih kategori</option>
                                                @foreach ($kategoriList as $kat)
                                                    <option value="{{ $kat->id }}">{{ $kat->nama }}
                                                        @if ($kat->kode)
                                                            ({{ $kat->kode }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Jenis alat</label>
                                            <select name="jenis_id" id="jenis_id" class="form-control">
                                                <option value="">Pilih kategori terlebih dahulu</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Nama alat / objek <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="nama_alat" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Lokasi alat <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="lokasi_alat" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kapasitas alat</label>
                                            <input type="text" name="kapasitas_alat" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Jumlah unit</label>
                                            <input type="number" name="jumlah_unit" class="form-control" value="1"
                                                min="1">
                                        </div>
                                    </div>

                                    @foreach ($dokumenKeys as $key => $label)
                                        <div class="border rounded p-3 mb-3">
                                            <label class="form-label fw-bold">{{ $label }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="mb-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input js-tipe" type="radio"
                                                        name="tipe_{{ $key }}" id="tipe_file_{{ $key }}"
                                                        value="file" data-key="{{ $key }}" checked>
                                                    <label class="form-check-label"
                                                        for="tipe_file_{{ $key }}">Upload file</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input js-tipe" type="radio"
                                                        name="tipe_{{ $key }}" id="tipe_link_{{ $key }}"
                                                        value="link" data-key="{{ $key }}">
                                                    <label class="form-check-label"
                                                        for="tipe_link_{{ $key }}">Kirim tautan</label>
                                                </div>
                                            </div>
                                            <div class="js-file-wrap" data-key="{{ $key }}">
                                                <input type="file" class="form-control" name="file_{{ $key }}"
                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                <small class="text-muted">PDF / JPG / PNG, maks 2 MB.</small>
                                            </div>
                                            <div class="js-link-wrap" data-key="{{ $key }}" style="display:none">
                                                <input type="url" class="form-control" name="link_{{ $key }}"
                                                    placeholder="https://drive.google.com/...">
                                            </div>
                                        </div>
                                    @endforeach

                                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                                    <a href="{{ route('pengawasan.suket.penyedia.pengajuan.index') }}"
                                        class="btn btn-secondary">Batal</a>
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
        $(document).ready(function() {
            $('.js-tipe').on('change', function() {
                var key = $(this).data('key');
                var tipe = $(this).val();
                $('.js-file-wrap[data-key="' + key + '"]').toggle(tipe === 'file');
                $('.js-link-wrap[data-key="' + key + '"]').toggle(tipe === 'link');
            });

            $('#kategori_id').on('change', function() {
                var id = $(this).val();
                var $jenis = $('#jenis_id');
                $jenis.html('<option value="">Memuat...</option>');
                if (!id) {
                    $jenis.html('<option value="">Pilih kategori terlebih dahulu</option>');
                    return;
                }
                var url = '{{ route('pengawasan.suket.penyedia.pengajuan.jenis', ['kategoriId' => '__ID__']) }}';
                $.get(url.replace('__ID__', id), function(data) {
                    var html = '<option value="">Pilih jenis</option>';
                    $.each(data, function(_, item) {
                        html += '<option value="' + item.id + '">' + item.nama + '</option>';
                    });
                    $jenis.html(html);
                });
            });

            $('#formSuket').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Konfirmasi pengajuan',
                    text: 'Kirim pengajuan Suket Layak K3 beserta dokumen yang diunggah?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kirim',
                    cancelButtonText: 'Periksa kembali'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }
                    $.ajax({
                        url: '{{ route('pengawasan.suket.penyedia.pengajuan.store') }}',
                        type: 'POST',
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil', response.message, 'success').then(
                                    function() {
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
                                Swal.fire('Gagal', response.message || 'Gagal menyimpan',
                                    'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON
                                .message) || 'Gagal menyimpan', 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush
