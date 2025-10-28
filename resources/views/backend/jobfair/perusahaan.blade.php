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
                                <h5 class="m-b-10">Perusahaan Job Fair: {{ $jobFair->nama_job_fair }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('jobfair.index') }}"><i class="feather icon-home"></i> Job Fair</a>
                                </li>
                                <li class="breadcrumb-item"><a href="javascript:">Perusahaan</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- Alert Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Error!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
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
                                    <a href="{{ route('jobfair.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="feather icon-arrow-left"></i> Kembali
                                    </a>
                                </div>
                                <div class="col-sm-6 text-end">
                                    <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal"
                                        data-bs-target="#modal-add"><i class="feather icon-plus"></i> Tambah Perusahaan</button>
                                </div>
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
                <form action="{{ route('jobfair.perusahaan.store', $jobFair->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="jobfair_id" value="{{ $jobFair->id }}">
                        
                        <div class="form-group">
                            <label for="user_id">Perusahaan (Penyedia Kerja) <span class="text-danger">*</span></label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Pilih Perusahaan</option>
                            </select>
                            <small class="text-muted">Hanya user dengan role "penyedia-kerja"</small>
                        </div>

                        <div class="form-group">
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editPerusahaanId" name="id">
                        
                        <div class="form-group">
                            <label for="editUserId">Perusahaan (Penyedia Kerja) <span class="text-danger">*</span></label>
                            <select class="form-control" id="editUserId" name="user_id" required>
                                <option value="">Pilih Perusahaan</option>
                            </select>
                        </div>

                        <div class="form-group">
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

@push('js')
<script>
$(document).ready(function() {
    // Load penyedia kerja untuk dropdown
    loadPenyediaKerja();

    // DataTable
    $('#simpletable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('jobfair.perusahaan', $jobFair->id) }}',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.log('DataTable Error:', xhr.responseText);
                alert('Error loading data: ' + xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user_name', name: 'user.name' },
            { data: 'user_email', name: 'user.email' },
            { data: 'status_badge', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'options', name: 'options', orderable: false, searchable: false }
        ]
    });

    // Auto hide alert after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

function loadPenyediaKerja() {
    $.ajax({
        url: '{{ route('jobfair.penyedia-kerja') }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Pilih Perusahaan</option>';
                response.data.forEach(function(user) {
                    options += '<option value="' + user.id + '">' + user.name + ' (' + user.email + ')</option>';
                });
                $('#user_id').html(options);
                $('#editUserId').html(options);
            }
        },
        error: function(xhr) {
            console.log('Error loading penyedia kerja:', xhr.responseText);
        }
    });
}

function showEditPerusahaanModal(id) {
    var showUrl = "{{ route('jobfair.perusahaan.show', [$jobFair->id, ':id']) }}".replace(':id', id);
    var updateUrl = "{{ route('jobfair.perusahaan.update', [$jobFair->id, ':id']) }}";
    
    $.ajax({
        url: showUrl,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                
                // Set form action
                $('#editForm').attr('action', updateUrl.replace(':id', data.id));
                
                // Fill form
                $('#editPerusahaanId').val(data.id);
                $('#editUserId').val(data.user_id);
                $('#editStatus').val(data.status);
                
                $('#modal-edit').modal('show');
            }
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseText);
        }
    });
}

function changeStatus(id, status) {
    var statusText = status == 1 ? 'approve' : 'reject';
    if (!confirm('Yakin ingin ' + statusText + ' perusahaan ini?')) {
        return;
    }

    var url = "{{ route('jobfair.perusahaan.change-status', [$jobFair->id, ':id']) }}".replace(':id', id);
    
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
</script>
@endpush