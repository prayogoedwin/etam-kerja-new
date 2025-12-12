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
                                    <h5 class="m-b-10">Perusahaan Magang Pemerintah: {{ $magang->nama_magang }}</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('magang_dn.index') }}"><i class="feather icon-home"></i> Magang
                                            Pemerintah</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="javascript:">Perusahaan</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Sukses!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Validation Error!</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- [ Main Content ] start -->
                <div class="row">
                    <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">
                                        <a href="{{ route('magang_dn.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="feather icon-arrow-left"></i> Kembali
                                        </a>
                                    </div>

                                    @if (Auth::user()->roles[0]['name'] != 'penyedia-kerja' && Auth::user()->roles[0]['name'] != 'pencari-kerja')
                                        <div class="col-sm-6 text-end">
                                            <button class="btn btn-success btn-sm btn-round has-ripple"
                                                data-bs-toggle="modal" data-bs-target="#modal-add"><i
                                                    class="feather icon-plus"></i> Tambah Perusahaan</button>
                                        </div>
                                    @endif

                                    @if (Auth::user()->roles[0]['name'] == 'penyedia-kerja')
                                        <div class="col-sm-6 text-end">
                                            <a href="{{ route('magang_dn.join', $magang->id) }}"
                                                class="btn btn-success btn-sm btn-round has-ripple"
                                                onclick="event.preventDefault(); document.getElementById('join-magang-form').submit();">
                                                <i class="feather icon-plus"></i> Ikuti Magang Sebagai Penyedia
                                            </a>

                                            <form id="join-magang-form" action="{{ route('magang_dn.join', $magang->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </div>
                                    @endif

                                </div>
                                <div class="table-responsive mt-3">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Perusahaan</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Tanggal Daftar</th>
                                                <th>Options</th>
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

        <!-- Modal Add -->
        <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('magang_dn.perusahaan.store', $magang->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Perusahaan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="magang_id" value="{{ $magang->id }}">

                            <div class="form-group mb-3">
                                <label for="user_id">Perusahaan (Penyedia Kerja) <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required></select>
                                <small class="text-muted">Ketik minimal 4 huruf untuk mencari perusahaan dengan role
                                    "penyedia-kerja"</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Perusahaan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editPerusahaanId" name="id">

                            <div class="form-group mb-3">
                                <label for="editUserId">Perusahaan (Penyedia Kerja) <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="editUserId" name="user_id" required></select>
                                <small class="text-muted">Ketik minimal 4 huruf untuk mencari perusahaan</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="editStatus">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="editStatus" name="status" required>
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>

@endsection

@push('css')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Fix z-index untuk Select2 di dalam modal */
        .select2-container {
            z-index: 9999 !important;
            width: 100% !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            color: #495057;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--open .select2-selection--single {
            border-color: #80bdff !important;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Make sure dropdown is clickable */
        .select2-container--default .select2-selection--single {
            cursor: pointer;
        }

        .select2-search--dropdown .select2-search__field {
            padding: 6px;
            border: 1px solid #ced4da;
        }
    </style>
@endpush

@push('js')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        console.log('=== SELECT2 DEBUG START ===');
        console.log('jQuery:', typeof jQuery !== 'undefined' ? 'LOADED ✓' : 'NOT LOADED ✗');
        console.log('Select2:', typeof $.fn.select2 !== 'undefined' ? 'LOADED ✓' : 'NOT LOADED ✗');

        $(document).ready(function() {
            console.log('Document Ready');

            // DataTable
            $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('magang_dn.perusahaan', $magang->id) }}',
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.log('DataTable Error:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'user_email',
                        name: 'user.email'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'options',
                        name: 'options',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Auto hide alert
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Initialize Select2 SETELAH modal fully shown
            $('#modal-add').on('shown.bs.modal', function() {
                console.log('Modal Add Shown - Init Select2 #user_id');

                // Tambah delay kecil untuk memastikan modal fully rendered
                setTimeout(function() {
                    initSelect2('#user_id');
                }, 100);
            });

            // Cleanup saat modal ditutup
            $('#modal-add').on('hidden.bs.modal', function() {
                destroySelect2('#user_id');
            });

            $('#modal-edit').on('hidden.bs.modal', function() {
                destroySelect2('#editUserId');
            });
        });

        /**
         * Initialize Select2 dengan AJAX
         */
        function initSelect2(selector) {
            console.log('→ initSelect2(' + selector + ')');

            // Check if element exists
            if ($(selector).length === 0) {
                console.error('✗ Element not found:', selector);
                return;
            }
            console.log('✓ Element found');

            // Check if Select2 library loaded
            if (typeof $.fn.select2 === 'undefined') {
                console.error('✗ Select2 library not loaded');
                alert('Error: Select2 tidak ter-load. Refresh halaman.');
                return;
            }
            console.log('✓ Select2 library loaded');

            // Destroy existing instance
            destroySelect2(selector);

            try {
                // Initialize Select2
                $(selector).select2({
                    dropdownParent: $(selector).closest('.modal'),
                    placeholder: 'Ketik minimal 4 huruf untuk mencari...',
                    allowClear: true,
                    minimumInputLength: 4,
                    maximumInputLength: 50,
                    width: '100%',
                    ajax: {
                        url: '{{ route('api.penyedia-kerja') }}',
                        dataType: 'json',
                        delay: 500,
                        data: function(params) {
                            console.log('→ AJAX Search:', params.term);
                            return {
                                search: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            console.log('← AJAX Results:', data.results.length + ' items');
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        error: function(xhr, status, error) {
                            console.error('✗ AJAX Error:', status, error);
                            alert('Error loading data: ' + error);
                        },
                        cache: true
                    },
                    language: {
                        inputTooShort: function(args) {
                            var remainingChars = args.minimum - args.input.length;
                            return 'Ketik ' + remainingChars + ' karakter lagi...';
                        },
                        inputTooLong: function(args) {
                            return 'Terlalu panjang! Maksimal 50 karakter.';
                        },
                        searching: function() {
                            return 'Mencari...';
                        },
                        noResults: function() {
                            return 'Tidak ada hasil';
                        },
                        errorLoading: function() {
                            return 'Error loading data';
                        }
                    }
                });

                console.log('✓ Select2 initialized successfully');

                // Test click
                $(selector).on('select2:opening', function(e) {
                    console.log('✓ Select2 opening (dropdown akan muncul)');
                });

                $(selector).on('select2:open', function(e) {
                    console.log('✓ Select2 opened (dropdown terbuka)');
                    // Focus ke search field
                    $('.select2-search__field').focus();
                });

                $(selector).on('select2:select', function(e) {
                    console.log('✓ Selected:', e.params.data);
                });

            } catch (error) {
                console.error('✗ Error init Select2:', error);
                alert('Error: ' + error.message);
            }
        }

        /**
         * Destroy Select2 instance
         */
        function destroySelect2(selector) {
            if ($(selector).data('select2')) {
                console.log('→ Destroying Select2:', selector);
                $(selector).select2('destroy');
            }
        }

        /**
         * Show Edit Modal
         */
        function showEditPerusahaanModal(id) {
            var showUrl = "{{ route('magang_dn.perusahaan.show', [$magang->id, ':id']) }}".replace(':id', id);
            var updateUrl = "{{ route('magang_dn.perusahaan.update', [$magang->id, ':id']) }}";

            $.ajax({
                url: showUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;

                        $('#editForm').attr('action', updateUrl.replace(':id', data.id));
                        $('#editPerusahaanId').val(data.id);
                        $('#editStatus').val(data.status);

                        // Show modal
                        $('#modal-edit').modal('show');

                        // Init Select2 after modal shown
                        $('#modal-edit').on('shown.bs.modal', function handler() {
                            setTimeout(function() {
                                initSelect2('#editUserId');

                                // Set selected value
                                if (data.user) {
                                    var newOption = new Option(
                                        data.user.name + ' (' + data.user.email + ')',
                                        data.user_id,
                                        true,
                                        true
                                    );
                                    $('#editUserId').append(newOption).trigger('change');
                                }
                            }, 100);

                            // Remove handler after first execution
                            $('#modal-edit').off('shown.bs.modal', handler);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Error loading data');
                }
            });
        }

        /**
         * Change Status
         */
        function changeStatus(id, status) {
            var statusText = status == 1 ? 'approve' : 'reject';
            if (!confirm('Yakin ingin ' + statusText + ' perusahaan ini?')) {
                return;
            }

            var url = "{{ route('magang_dn.perusahaan.change-status', [$magang->id, ':id']) }}".replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#simpletable').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }

        console.log('=== SELECT2 DEBUG END ===');
    </script>
@endpush
