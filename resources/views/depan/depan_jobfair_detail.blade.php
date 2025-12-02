@include('components.header')
<!-- Start Breadcrumb
    ============================================= -->
<div class="breadcrumb-area text-left">
    <div class="breadcrum-shape">
        <!-- <img src="assets/img/shape/50.png" alt="Image Not Found"> -->
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1>Jobfair</h1>
                <ul class="breadcrumb">
                    <li><a href=""><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Jobfair</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Blog
    ============================================= -->
<div class="home-blog-area default-padding-bottom">
    <!-- tampilan detail berita -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="single-post">
                    <div class="thumb">
                        <img src="{{ asset('storage/' . $berita->poster) }}"  onerror="this.onerror=null; this.src='{{ asset('assets/etam_fe/images/default/grey.avif') }}'">
                    </div>
                    <div class="info" hidden>
                        <h4>{{ $berita->name }}</h4>
                        <p class="meta">
                            <span><i class="fas fa-user"></i> ADMIN NAKERBISA</span>
                            <span>{{ $berita->created_at->format('d F, Y') }}</span>
                        </p>
                        <div class="content">
                            <!-- Menampilkan deskripsi berita -->
                            <p>{!! $berita->description !!}</p>
                        </div>
                    </div>

                    <!-- Tabel Lowongan -->
                    <div class="mt-4">
                        <h5 class="mb-3"><i class="fas fa-briefcase"></i> Daftar Lowongan ({{ $lowongan->count() }})</h5>
                        <div class="table-responsive">
                            <table id="tbl-lowongan" class="table table-bordered table-striped" style="width:100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Judul Lowongan</th>
                                        <th>Perusahaan</th>
                                        <th style="width: 12%">Kebutuhan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowongan as $index => $loker)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $loker->judul_lowongan ?? '-' }}</td>
                                        <td>{{ $loker->postedBy->penyedia->name ?? '-' }}</td>
                                        <td class="text-center">{{ ($loker->jumlah_pria ?? 0) + ($loker->jumlah_wanita ?? 0) }} orang</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Blog  -->

@include('components.footer')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#tbl-lowongan').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'asc']]
    });
});
</script>
@endpush