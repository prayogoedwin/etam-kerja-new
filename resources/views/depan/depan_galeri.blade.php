@include('components.header')
<!-- Start Breadcrumb
    ============================================= -->
<div class="breadcrumb-area bg-theme shadow dark text-center text-light">
    <div class="breadcrum-shape">
        <!-- <img src="assets/img/shape/50.png" alt="Image Not Found"> -->
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1>Galeri Kegiatan</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Galeri Kegiatan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->


<!-- Start Project
    ============================================= -->
<div class="project-style-three-area default-padding">

    <div class="container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 gallery-content">
                    <div class="magnific-mix-gallery gallery-masonary">
                        <div id="gallery-masonary" class="gallery-items colums-3">
                           
                            @foreach ($galeris as $item)
                            <!-- Single Item -->
                            <div class="gallery-item">
                                <div class="gallery-style-three">
                                    <div class="thumb">
                                        <!-- Menampilkan gambar dari path_file -->
                                        <img src="{{ asset('storage/' . $item->path_file) }}"
                                            alt="{{ $item->name }}">
                                        <a href="{{ asset('storage/' . $item->path_file) }}"
                                            class="item popup-gallery">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="overlay">
                                        <div class="content">
                                            <!-- Menampilkan nama dan deskripsi -->
                                            <span>{{ $item->category ?? 'Info' }}</span>
                                            <h4><a href="#">{{ $item->name }}</a></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                        @endforeach
                          
                    </div>

                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <div class="load-more-info secondary text-center mt-60">
                                <p>
                                    Are you interested to show more portfolios? <a href="#">Load More</a>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Project -->
@include('components.footer')
