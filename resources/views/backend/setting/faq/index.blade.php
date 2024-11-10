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
                                        <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal" data-bs-target="#modal-report"><i class="feather icon-plus"></i> Add Data</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Pertanyaan</th>
                                                <th>Jawaban</th>
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
       

        <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
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
                                        <label class="floating-label" for="pertanyaan">Pertanyaan</label>
                                        <textarea class="form-control" id="pertanyaan"  name="pertanyaan" rows="3"></textarea> 
                                    </div>
                                </div>
                               
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="floating-label" for="jawaban">Jawaban</label>
                                        <textarea class="form-control" id="jawaban" name="jawaban" rows="3"></textarea>
                                    </div>
                                    <button class="btn btn-primary">Submit</button>
                                    <button class="btn btn-danger">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAdminForm">
                            <input type="hidden" id="editId">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="pertanyaan">Pertanyaan</label>
                                    <textarea class="form-control" id="editPertanyaan"  name="pertanyaan" rows="3"></textarea> 
                                </div>
                            </div>
                           
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="jawaban">Jawaban</label>
                                    <textarea class="form-control" id="editJawaban" name="jawaban" rows="3"></textarea>
                                </div>
                                <button class="btn btn-primary" onclick="updateFaq()" type="button">Submit</button>
                                
                            </div>

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
            ajax: '{{ route('faq.index') }}',
            autoWidth: false, // Menonaktifkan auto-width
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name' },
                { data: 'description' },
                { data: 'options', orderable: false, searchable: false },
            ]
        });
    });
</script>

<script>
   $(document).ready(function () {
        $('#registerForm').submit(function (e) {
            e.preventDefault(); // Prevent form from submitting normally

            // Clear previous error messages
            $('#errorMessages').html('').addClass('d-none');

            var formData = {
                pertanyaan: $('#pertanyaan').val(),
                jawaban: $('#jawaban').val(),
                _token: '{{ csrf_token() }}' // Add CSRF token for security
            };

            $.ajax({
                type: 'POST',
                url: '{{ route('faq.add') }}', // Ganti dengan rute yang sesuai
                data: formData,
                success: function (response) {
                    if (response.success) {
                        alert('Berhasil menambahkan data');
                        $('#modal-report').modal('hide');
                        location.reload(); // Refresh halaman
                    } else {
                        // If validation errors are found, display them in an alert
                        if (response.errors) {
                            let errorMessages = '';
                            $.each(response.errors, function (key, value) {
                                $.each(value, function (index, errorMessage) {
                                    errorMessages += errorMessage + '\n'; // Gabungkan pesan error
                                });
                            });
                            alert('Terjadi kesalahan:\n' + errorMessages);
                        } else {
                            alert('Gagal menambahkan data');
                        }
                    }
                },
                error: function (xhr, status, error) {
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        });
    });
</script>

<script>
    function showEditModal(id) {
        var detailUrl = "{{ route('faq.detail', ':id') }}".replace(':id', id);
        $.ajax({
            url: detailUrl,
            type: 'GET',
            success: function(response) {
                let dt = response.data;

                // Isi data modal dengan data yang diperoleh
                $('#editId').val(dt.id);
                $('#editPertanyaan').val(dt.name);
                $('#editJawaban').val(dt.description);

                // Tampilkan modal edit
                $('#modal-edit').modal('show');
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    }
</script>


<script>

    function updateFaq() {
        // Get data from the modal form
        var id = $('#editId').val();
        var name = $('#editPertanyaan').val();
        var description = $('#editJawaban').val();

        // Send the data to the update route
        $.ajax({
            url: "{{ route('faq.update', ':id') }}".replace(':id', id),
            type: 'PUT',
            data: {
                _token: "{{ csrf_token() }}",  // CSRF token for security
                name: name,
                description: description
            },
            success: function(response) {
                if(response.success) {
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
    }

</script>




<script>
    function confirmDelete(id) {
        // Konfirmasi penghapusan
        var deleteUrl = "{{ route('faq.softdelete', ':id') }}".replace(':id', id);
        if (confirm("Yakin hapus data?")) {
            // Kirim request ke server untuk menghapus data
            $.ajax({
                url: deleteUrl, 
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),  // Menyertakan CSRF token
                },
                success: function(response) {
                    // Jika berhasil, reload DataTable
                    alert(response.message);  // Menampilkan pesan
                    $('#simpletable').DataTable().ajax.reload();  // Reload data tabel
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

