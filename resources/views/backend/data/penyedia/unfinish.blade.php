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
                                    <h5 class="m-b-10">Pemberi Kerja</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->


                <!-- [ Main Content ] start -->
                <div class="row">

                    <div class="row align-items-center m-l-0" hidden>
                        <div class="col-sm-6">
                            <!-- Tombol untuk mengunduh CSV -->
                            {{-- <a href="{{ route('datapencari.exportCsv') }}" class="btn btn-success">Unduh CSV</a> --}}
                            {{-- <a href="javascript:void(0);" id="downloadCsv" class="btn btn-info">Unduh CSV</a> --}}

                        </div>
                        <div class="col-sm-6 text-end">
                        </div>
                    </div>


                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">

                                    </div>
                                    <div class="col-sm-6 text-end">
                                        {{-- <a href="javascript:void(0);" id="downloadCsv" class="btn btn-success">Unduh CSV</a> --}}
                                        <button id="bulk-update-btn" class="btn btn-sm btn-danger"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Email</th>
                                                <th>Whatsapp</th>
                                                <th>Tanggal Daftar</th>
                                                <th>Options<input type="checkbox" id="select-all" style="margin-left: 4px">
                                                </th>
                                            </tr>
                                        </thead>

                                    </table>
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
        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('datapenyediaunfinish.index') }}',
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'whatsapp'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // // Tombol untuk mengunduh CSV
            // $('#downloadCsv').on('click', function() {
            //     // Pastikan table sudah terinisialisasi sebelum digunakan
            //     var searchTerm = table.search(); // Ambil term pencarian dari DataTable

            //     // Buat URL dengan parameter pencarian
            //     var url = '{{ route('datapencari.exportCsv') }}?search=' + searchTerm;

            //     // Redirect ke URL ekspor CSV
            //     window.location.href = url;
            // });

            // Handle select all checkboxes
            $('#select-all').on('change', function() {
                $('.pelamar-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Bulk update
            $('#bulk-update-btn').on('click', function() {

                let selectedIds = $('.pelamar-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    alert('Pilih minimal satu data!');
                    return;
                }

                // 🔴 Konfirmasi
                if (!confirm('Apakah Anda yakin ingin menghapus data yang dipilih?')) {
                    return;
                }

                $.ajax({
                    url: '{{ route('bulkdelete.penyediaunfinish') }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload();
                        // location.reload();
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat memproses data.');
                    }
                });
            });
        });
    </script>
@endpush
