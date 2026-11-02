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
                                    <h5 class="m-b-10">Detail Pengajuan Suket Layak K3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @include('backend.pengawasan.pjk3.suket._info')

                @if ((int) $suket->status >= \App\Models\Pengawasan\Pjk3\EtamPjk3Suket::STATUS_DRAFT)
                    <div class="mb-3">
                        <a href="{{ route('pengawasan.suket.admin.cetak-lhpp', $suket->id) }}" target="_blank"
                            class="btn btn-primary">Cetak LHPP</a>
                        <a href="{{ route('pengawasan.suket.admin.cetak-suket', $suket->id) }}" target="_blank"
                            class="btn btn-success">Cetak Suket</a>
                    </div>
                @endif

                @if ($canDispo)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Disposisi Kasi ke Pengawas</h5>
                        </div>
                        <div class="card-body">
                            <form id="formDispo">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Pengawas (Bidang Pembinaan dan Pengawasan / seksi
                                        turunan) <span class="text-danger">*</span></label>
                                    <select name="pengawas_id" class="form-control" required>
                                        <option value="">Pilih pengawas</option>
                                        @foreach ($daftarPengawas as $pw)
                                            <option value="{{ $pw->id }}">{{ $pw->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan disposisi</label>
                                    <textarea name="catatan_disposisi" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Teruskan ke Pengawas</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($canAnalisis)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Hasil analisis pengawas</h5>
                        </div>
                        <div class="card-body">
                            <form id="formAnalisis">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Tanggal pemeriksaan <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pemeriksaan" class="form-control"
                                        value="{{ optional($suket->tanggal_pemeriksaan)->format('Y-m-d') ?? now()->toDateString() }}"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kesimpulan <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_layak"
                                                id="layak" value="1" @checked((int) $suket->is_layak === 1)>
                                            <label class="form-check-label text-success" for="layak">Layak</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_layak"
                                                id="tidak-layak" value="0" @checked($suket->is_layak !== null && (int) $suket->is_layak === 0)>
                                            <label class="form-check-label text-danger" for="tidak-layak">Tidak
                                                layak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hasil analisis <span class="text-danger">*</span></label>
                                    <textarea name="hasil_analisis" class="form-control" rows="5" required>{{ $suket->hasil_analisis }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rekomendasi</label>
                                    <textarea name="rekomendasi" class="form-control" rows="3">{{ $suket->rekomendasi }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan hasil &amp; terbitkan draft</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($canTtePengawas)
                    <form action="{{ route('pengawasan.suket.admin.tte-pengawas', $suket->id) }}" method="POST"
                        class="d-inline mb-3">
                        @csrf
                        <button type="submit" class="btn btn-info"
                            onclick="return confirm('Tandatangani LHPP/Suket sebagai pengawas (demo alur)?')">TTD
                            Pengawas (demo)</button>
                    </form>
                @endif

                @if ($canTteKadis)
                    <form action="{{ route('pengawasan.suket.admin.tte-kadis', $suket->id) }}" method="POST"
                        class="d-inline mb-3">
                        @csrf
                        <button type="submit" class="btn btn-success"
                            onclick="return confirm('Tandatangani Suket sebagai Kepala Dinas (demo alur)?')">TTD Kepala
                            Dinas (demo)</button>
                    </form>
                @endif

                <a href="{{ route('pengawasan.suket.admin.pengajuan') }}" class="btn btn-secondary mb-4">Kembali</a>
            </div>
        </div>
    </body>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#formDispo').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('pengawasan.suket.admin.disposisi', $suket->id) }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(function() {
                                location.reload();
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
                            Swal.fire('Gagal', response.message || 'Gagal memproses', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Gagal memproses', 'error');
                    }
                });
            });

            $('#formAnalisis').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('pengawasan.suket.admin.analisis', $suket->id) }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(function() {
                                location.reload();
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
                            Swal.fire('Gagal', response.message || 'Gagal memproses', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Gagal memproses', 'error');
                    }
                });
            });
        });
    </script>
@endpush
