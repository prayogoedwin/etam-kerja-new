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
                <h1>Berita & Informasi</h1>
                <ul class="breadcrumb">
                    <li><a href=""><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Berita & Informasi</li>
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
                        <img src="{{ asset('storage/' . $berita->cover) }}"  onerror="this.onerror=null; this.src='{{ asset('assets/etam_fe/images/default/grey.avif') }}'">
                    </div>
                    <div class="info">
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
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Blog  -->

@include('components.footer')
