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
                                    <h5 class="m-b-10">Galeri</h5>
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
                                
                                <form action="{{ route('berita.update', $berita->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <label for="name">Name:</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ $berita->name }}">

                                    <label for="cover">Cover Image:</label>
                                    <div class="mb-3">
                                        <input type="file" name="cover" class="form-control">
                                        @if ($berita->cover)
                                            <img src="{{ asset('storage/' . $berita->cover) }}" alt="Cover Image"
                                                class="img-fluid mt-2" style="max-width: 200px;">
                                        @endif
                                    </div>

                                    <label for="description">Description:</label>
                                    <textarea name="description" id="description" class="form-control">{!! $berita->description !!}</textarea>

                                    <label for="status">Status:</label>
                                    <select class="form-control" id="edit-status" name="status">

                                        <option value="1" {{ $berita->status == '1' ? 'selected' : '' }}>
                                            Aktif</option>
                                        <option value="0" {{ $berita->status == '0' ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>
                                    </select>

                                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- customar project  end -->


                </div>
                <!-- [ Main Content ] end -->


            </div>
        </div>
    

@endsection

@push('js')
<script>
     $(document).ready(function() {
        $('#description').summernote({
            height: 300, // Tinggi editor
            callbacks: {
                onImageUpload: function(files) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#description').summernote('insertImage', e.target.result);
                    };
                    reader.readAsDataURL(files[0]);
                }
            }
        });
     });
</script>
@endpush
