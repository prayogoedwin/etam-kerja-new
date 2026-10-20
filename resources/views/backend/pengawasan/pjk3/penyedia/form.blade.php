@extends('backend.template.backend')

@section('content')
    @php
        $isRevisi = (bool) $ajuan;
    @endphp

    <body class="box-layout container background-green">
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">{{ $isRevisi ? 'Revisi Ajuan PJK3' : 'Ajukan PJK3' }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($isRevisi && $ajuan->alasan)
                    <div class="alert alert-warning">Alasan revisi: {{ $ajuan->alasan }}</div>
                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="formPjk3" enctype="multipart/form-data">
                                    @csrf
                                    @foreach ($dokumenKeys as $key => $label)
                                        @php
                                            $existing = $ajuan?->dokumen[$key] ?? null;
                                            $tipe = $existing['tipe'] ?? 'file';
                                        @endphp
                                        <div class="border rounded p-3 mb-3">
                                            <label class="form-label fw-bold">{{ $label }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="mb-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input js-tipe" type="radio"
                                                        name="tipe_{{ $key }}" id="tipe_file_{{ $key }}"
                                                        value="file" data-key="{{ $key }}"
                                                        @checked($tipe === 'file')>
                                                    <label class="form-check-label"
                                                        for="tipe_file_{{ $key }}">Upload file</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input js-tipe" type="radio"
                                                        name="tipe_{{ $key }}" id="tipe_link_{{ $key }}"
                                                        value="link" data-key="{{ $key }}"
                                                        @checked($tipe === 'link')>
                                                    <label class="form-check-label"
                                                        for="tipe_link_{{ $key }}">Kirim tautan</label>
                                                </div>
                                            </div>
                                            @if (!empty($existing['value']))
                                                <p class="mb-2 small">
                                                    Dokumen saat ini:
                                                    @if (($existing['tipe'] ?? null) === 'link')
                                                        <a href="{{ $existing['value'] }}" target="_blank"
                                                            rel="noopener">{{ $existing['value'] }}</a>
                                                    @else
                                                        <a href="{{ route('pengawasan.pjk3.penyedia.file', [$ajuan->id, $key]) }}"
                                                            target="_blank">Lihat file</a>
                                                    @endif
                                                    — unggah file baru atau ganti tautan untuk menggantinya.
                                                </p>
                                            @endif
                                            <div class="js-file-wrap" data-key="{{ $key }}"
                                                style="{{ $tipe === 'link' ? 'display:none' : '' }}">
                                                <input type="file" class="form-control" name="file_{{ $key }}"
                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                <small class="text-muted">PDF / JPG / PNG, maks 2 MB. Pilih file baru
                                                    untuk mengganti file/tautan sebelumnya.</small>
                                            </div>
                                            <div class="js-link-wrap" data-key="{{ $key }}"
                                                style="{{ $tipe === 'file' ? 'display:none' : '' }}">
                                                <input type="url" class="form-control" name="link_{{ $key }}"
                                                    placeholder="https://drive.google.com/..."
                                                    value="{{ ($existing['tipe'] ?? null) === 'link' ? $existing['value'] : '' }}">
                                                <small class="text-muted">Gunakan tautan Google Drive yang bisa diakses
                                                    (bagikan: Anyone with the link / siapa saja yang memiliki tautan).</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($isRevisi)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Keterangan <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="keterangan" class="form-control" rows="3"
                                                placeholder="Jelaskan perbaikan atau dokumen yang diunggah ulang">{{ old('keterangan') }}</textarea>
                                        </div>
                                    @endif
                                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                                    <a href="{{ route('pengawasan.pjk3.penyedia.index') }}"
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

            $('#formPjk3').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var isRevisi = {{ $isRevisi ? 'true' : 'false' }};

                Swal.fire({
                    title: isRevisi ? 'Konfirmasi ajuan ulang' : 'Konfirmasi pengajuan',
                    text: isRevisi
                        ? 'Apakah data dan dokumen revisi yang Anda isi sudah benar? Pengajuan akan dikirim ulang untuk diverifikasi.'
                        : 'Apakah data dan dokumen yang Anda isi sudah benar? Pengajuan akan dikirim untuk diverifikasi.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kirim',
                    cancelButtonText: 'Periksa kembali'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: '{{ $isRevisi ? route('pengawasan.pjk3.penyedia.update', $ajuan->id) : route('pengawasan.pjk3.penyedia.store') }}',
                        type: 'POST',
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil', response.message, 'success').then(function() {
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
                                Swal.fire('Gagal', response.message || 'Gagal menyimpan', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                                'Gagal menyimpan', 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush
