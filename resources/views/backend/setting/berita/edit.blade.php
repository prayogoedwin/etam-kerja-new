@extends('backend.template.backend')

@section('content')
<body class="box-layout container background-green">
        <div class="layout-container">
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
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
                        </div>
                    </div>
                    <!-- / Content -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <script>
        $('#description').summernote({
            placeholder: 'description...',
            tabsize: 2,
            height: 300
        });
    </script>
@endsection
