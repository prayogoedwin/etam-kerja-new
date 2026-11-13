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
                                    <h5 class="m-b-10">Kode Suket K3</h5>
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
                                    Generate kode lalu berikan ke PJK3 yang akan mengajukan Suket Layak K3 untuk perusahaan Anda.
                                    Satu kode berlaku 30 hari dan bisa dipakai untuk pencarian selama masih aktif.
                                    Anda boleh membuat lebih dari satu kode.
                                </div>
                                <div class="row align-items-center m-l-0 mb-3">
                                    <div class="col-sm-6">
                                        <h5 class="mb-0">Kode perusahaan Anda</h5>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button type="button" class="btn btn-success btn-sm" id="btnGenerateKode">
                                            <i class="feather icon-plus"></i> Generate kode
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Berlaku hingga</th>
                                                <th>Status</th>
                                                <th>Dibuat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($daftar as $row)
                                                <tr>
                                                    <td>
                                                        <code class="fs-6">{{ $row->kode }}</code>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2 js-salin"
                                                            data-kode="{{ $row->kode }}">Salin</button>
                                                    </td>
                                                    <td>{{ $row->berlaku_hingga?->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if ($row->masihBerlaku())
                                                            <span class="badge bg-success">Aktif</span>
                                                        @else
                                                            <span class="badge bg-secondary">Kedaluwarsa</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $row->created_at?->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Belum ada kode. Generate
                                                        kode untuk dibagikan ke PJK3.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
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
            $('#btnGenerateKode').on('click', function() {
                $.ajax({
                    url: '{{ route('pengawasan.suket.penyedia.kode.store') }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Kode dibuat', response.kode + ' (berlaku s.d. ' +
                                response.berlaku_hingga + ')', 'success').then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message || 'Gagal membuat kode', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Gagal membuat kode', 'error');
                    }
                });
            });

            $('.js-salin').on('click', function() {
                var kode = $(this).data('kode');
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(kode);
                }
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Kode disalin', timer: 1500, showConfirmButton: false });
            });
        });
    </script>
@endpush
