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
                                    <h5 class="m-b-10">Log TTE</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-8">
                                        <p class="mb-0 text-muted">Catatan setiap proses TTE. Kegagalan wajib tercatat, termasuk passphrase salah dan koneksi BSrE putus.</p>
                                    </div>
                                    <div class="col-sm-4 text-end">
                                        <a href="{{ route('tte.menunggu') }}" class="btn btn-secondary btn-sm">Kembali ke antrean</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="ttelog" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Waktu</th>
                                                <th>Petugas</th>
                                                <th>Modul</th>
                                                <th>Dokumen</th>
                                                <th>Kode</th>
                                                <th>Status</th>
                                                <th>Pesan</th>
                                            </tr>
                                        </thead>
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
            $('#ttelog').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('tte.log') }}',
                order: [[1, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'waktu' },
                    { data: 'petugas', orderable: false, searchable: false },
                    { data: 'nama_modul' },
                    { data: 'judul_dokumen' },
                    { data: 'kode' },
                    { data: 'status_label', orderable: false, searchable: false },
                    { data: 'pesan' }
                ]
            });
        });
    </script>
@endpush
