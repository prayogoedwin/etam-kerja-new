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
                                    <h5 class="m-b-10">Admin</h5>
                                </div>
                                {{-- <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="#!">Hospital</a></li>
                                    <li class="breadcrumb-item"><a href="#!">Department</a></li>
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
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">

                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal"
                                            data-bs-target="#modal-report"><i class="feather icon-plus"></i> Add
                                            Data</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Whatsapp</th>
                                                <th>Role</th>
                                                <th>Kabkota</th>
                                                <th>Kecamatan</th>
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


        <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="registerForm">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="floating-label" for="Name">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="floating-label" for="email">Email</label>
                                        <input type="text" class="form-control" id="email" name="email"
                                            placeholder="">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="floating-label" for="whatsapp">Whatsapp</label>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp"
                                            placeholder="">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="userRole" class="form-label">Role</label>
                                        <select class="form-control" id="userRole" name="role_id" required>
                                            <option value="">Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12" id="kabkotaDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="kabkota_id" class="form-label">Kabupaten/Kota</label>
                                        <select id="kabkota_id" name="kabkota_id" class="form-control" required>
                                            <option value="">-- Pilih Lokasi --</option>
                                            @foreach (getKabkota() as $lokasi)
                                                <option value="{{ $lokasi->id }}">{{ $lokasi->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12" id="kecamatanDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="kecamatan_id" class="form-label">Kecamatan</label>
                                        <select id="kecamatan_id" name="kecamatan_id" class="form-control" required>
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    {{-- <div class="form-group">
                                        <label class="floating-label" for="Description">Description</label>
                                        <textarea class="form-control" id="Description" rows="3"></textarea>
                                    </div> --}}
                                    <button class="btn btn-primary">Submit</button>
                                    <button class="btn btn-danger">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAdminForm">
                            <input type="hidden" id="editAdminId">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="editWhatsapp" class="form-label">Whatsapp</label>
                                <input type="text" class="form-control" id="editWhatsapp" name="whatsapp" required>
                            </div>
                            <div class="mb-3">
                                <label for="editRole" class="form-label">Role</label>
                                <select class="form-control" id="editRole" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-12" id="kabkotaDivE" style="display: none;">
                                <div class="form-group">
                                    <label for="editkabkota_id" class="form-label">Kabupaten/Kota</label>
                                    <select id="editkabkota_id" name="kabkota_id" class="form-control" required>
                                        <option value="">-- Pilih Lokasi --</option>
                                        @foreach (getKabkota() as $lokasi)
                                            <option value="{{ $lokasi->id }}">{{ $lokasi->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" onclick="updateFaq()">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </body>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            $('#simpletable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.index') }}',
                autoWidth: false, // Menonaktifkan auto-width
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'whatsapp'
                    },
                    {
                        data: 'roles'
                    },
                    {
                        data: 'kabkota'
                    },
                    {
                        data: 'kecamatan'
                    },

                    
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>

    <script>
        // jQuery to handle the role change event
        $(document).ready(function() {
            // Trigger the role change handler initially to show/hide based on the selected role
            toggleKabkotaField();

            // Event listener for role change
            $('#userRole').change(function() {
                toggleKabkotaField();
            });

            function toggleKabkotaField() {
                var selectedRole =  $('#userRole option:selected').text();
                // var selectedRoleText = $('#userRole option:selected').text();
                if (selectedRole == 'admin-kabkota' || selectedRole == 'admin-kabkota-officer'  || selectedRole == 'eksekutif-kabkota') {
                    // Show the kabkota field and make it required
                    $('#kabkotaDiv').show();
                    $('#kabkota_id').prop('required', true);

                    // Additional logic for admin-kabkota-officer
                    if (selectedRole === 'admin-kabkota-officer') {
                        $('#kecamatanDiv').show(); // Show kecamatan field
                        $('#kecamatan_id').prop('required', true); // Make kecamatan required
                    } else {
                        $('#kecamatanDiv').hide(); // Hide kecamatan field
                        $('#kecamatan_id').prop('required', false); // Remove required attribute
                    }

                } else {
                    // Hide the kabkota field and remove the required attribute
                    $('#kabkotaDiv').hide();
                    $('#kabkota_id').prop('required', false);
                }
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            // Event listener for kabkota_id change
            $('#kabkota_id').change(function () {
                var kabkotaId = $(this).val(); // Get the selected kabkota_id

                if (kabkotaId) {
                    // Make an AJAX GET request to fetch kecamatan
                    $.ajax({
                        url: '/get-kecamatan', // Endpoint for the server-side function
                        type: 'GET',
                        data: { kabkota_id: kabkotaId },
                        success: function (response) {
                            // Clear the kecamatan dropdown
                            $('#kecamatan_id').empty();

                            // Populate the kecamatan dropdown with options
                            if (response.data && response.data.length > 0) {
                                $('#kecamatan_id').append('<option value="">Pilih Kecamatan</option>'); // Default option
                                response.data.forEach(function (kecamatan) {
                                    $('#kecamatan_id').append(
                                        `<option value="${kecamatan.id}">${kecamatan.name}</option>`
                                    );
                                });
                            } else {
                                $('#kecamatan_id').append('<option value="">Tidak ada data kecamatan</option>');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching kecamatan:', error);
                            $('#kecamatan_id').append('<option value="">Error memuat kecamatan</option>');
                        }
                    });
                } else {
                    // Reset the kecamatan dropdown if no kabkota is selected
                    $('#kecamatan_id').empty();
                    $('#kecamatan_id').append('<option value="">Pilih Kecamatan</option>');
                }
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            $('#registerForm').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally

                // Clear previous error messages
                $('#errorMessages').html('').addClass('d-none');

                var formData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    whatsapp: $('#whatsapp').val(),
                    role_id: $('#userRole').val(),
                    kabkota_id: $('#kabkota_id').val(),
                    kecamatan_id: $('#kecamatan_id').val(),
                    
                    _token: '{{ csrf_token() }}' // Add CSRF token for security
                };

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.add') }}', // Ganti dengan rute yang sesuai
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert('User berhasil ditambahkan');
                            $('#modal-report').modal('hide');
                            location.reload(); // Refresh halaman
                        } else {
                            // If validation errors are found, display them in an alert
                            if (response.errors) {
                                let errorMessages = '';
                                $.each(response.errors, function(key, value) {
                                    $.each(value, function(index, errorMessage) {
                                        errorMessages += errorMessage +
                                            '\n'; // Gabungkan pesan error
                                    });
                                });
                                alert('Terjadi kesalahan:\n' + errorMessages);
                            } else {
                                alert('Gagal menambahkan user');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });
        });
    </script>

    <script>
        // Define the toggleKabkotaFieldE function globally
        function toggleKabkotaFieldE(selectedRole) {
            if (selectedRole == 'admin-kabkota' || selectedRole == 'admin-kabkota-officer') {
                // Show the kabkota field and make it required
                $('#kabkotaDivE').show();
                $('#editkabkota_id').prop('required', true);
            } else {
                // Hide the kabkota field and remove the required attribute
                $('#kabkotaDivE').hide();
                $('#editkabkota_id').prop('required', false);
            }
        }

        $(document).ready(function () {
            // Event listener for role change inside the modal
            $('#editRole').change(function () {
                var selectedRoleText = $('#editRole option:selected').text();
                toggleKabkotaFieldE(selectedRoleText);
            });
        });

        function showEditModal(adminId) {
            var detailUrl = "{{ route('admin.detail', ':id') }}".replace(':id', adminId);
            $.ajax({
                url: detailUrl,
                type: 'GET',
                success: function (response) {
                    let admin = response.data;

                    // Populate modal fields with retrieved data
                    $('#editAdminId').val(admin.id);
                    $('#editName').val(admin.user.name);
                    $('#editEmail').val(admin.user.email);
                    $('#editWhatsapp').val(admin.user.whatsapp);
                    $('#editRole').val(admin.user.roles[0].id).prop('selected', true);
                    $('#editkabkota_id').val(admin.kabkota_id || '').prop('selected', true);

                    // Show or hide the kabkota field based on the role
                    toggleKabkotaFieldE(admin.user.roles[0].id);

                    // Show the edit modal
                    $('#modal-edit').modal('show');
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    </script>


    <script>
        $('#editAdminForm').submit(function(e) {
        e.preventDefault(); // Prevent form from submitting normally
            // Get data from the modal form
            var id = $('#editAdminId').val();
            var name = $('#editName').val();
            var email = $('#editEmail').val();
            var whatsapp = $('#editWhatsapp').val();
            var role_id = $('#editRole').val();
            var kabkota_id = $('#editkabkota_id').val();
            




            // Send the data to the update route
            $.ajax({
                url: "{{ route('admin.update', ':id') }}".replace(':id', id),
                type: 'PUT',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token for security
                    name: name,
                    email: email,
                    whatsapp: whatsapp,
                    role_id: role_id,
                    kabkota_id: kabkota_id
                },
                success: function(response) {
                    if (response.success) {
                        // Display success message
                        alert(response.message);
                        // Close modal
                        $('#modal-edit').modal('hide');
                        // Optionally, reload the table or page to reflect the update
                        location.reload();
                    } else {
                        // Display error message
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });
    </script>


    <script>
        function confirmDelete(adminId) {
            // Konfirmasi penghapusan
            var deleteUrl = "{{ route('admin.softdelete', ':id') }}".replace(':id', adminId);
            if (confirm("Are you sure you want to delete this admin?")) {
                // Kirim request ke server untuk menghapus data
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // Menyertakan CSRF token
                    },
                    success: function(response) {
                        // Jika berhasil, reload DataTable
                        alert(response.message); // Menampilkan pesan
                        $('#simpletable').DataTable().ajax.reload(); // Reload data tabel
                    },
                    error: function(xhr, status, error) {
                        // Tampilkan error jika ada masalah
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>


    <script>
        function confirmReset(id) {
            // Konfirmasi penghapusan
            var deleteUrl = "{{ route('admin.reset', ':id') }}".replace(':id', id);
            if (confirm("Yakin  Reset Password (Password akan direset sesuai nama email/username)?")) {
                // Kirim request ke server untuk menghapus data
                $.ajax({
                    url: deleteUrl,
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // Menyertakan CSRF token
                    },
                    success: function(response) {
                        // Jika berhasil, reload DataTable
                        alert(response.message); // Menampilkan pesan
                        $('#simpletable').DataTable().ajax.reload(); // Reload data tabel
                    },
                    error: function(xhr, status, error) {
                        // Tampilkan error jika ada masalah
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>
@endpush
