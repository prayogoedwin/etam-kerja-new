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
                            <!-- Single Item -->
                            <div class="gallery-item">
                                <div class="gallery-style-three">
                                    <div class="thumb">
                                        <img src="{{ asset('assets') }}/etam_fe/img/800x600.png" alt="Thumb">
                                        <a href="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                            class="item popup-gallery">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="overlay">
                                        <div class="content">
                                            <span>Job Fair</span>
                                            <h4><a href="#">Pembukaan Acara Job Fair Kaltim</a></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                            <!-- Single Item -->
                            <div class="gallery-item">
                                <div class="gallery-style-three">
                                    <div class="thumb">
                                        <img src="{{ asset('assets') }}/etam_fe/img/800x600.png" alt="Thumb">
                                        <a href="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                            class="item popup-gallery">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="overlay">
                                        <div class="content">
                                            <span>Public Speaking</span>
                                            <h4><a href="#">Pelatihan Inteview</a></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                            <!-- Single Item -->
                            <div class="gallery-item">
                                <div class="gallery-style-three">
                                    <div class="thumb">
                                        <img src="{{ asset('assets') }}/etam_fe/img/800x600.png" alt="Thumb">
                                        <a href="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                            class="item popup-gallery">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="overlay">
                                        <div class="content">
                                            <span>Engagement</span>
                                            <h4><a href="#">Mengunjungi Perusahaan Rekomendasi</a></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                        </div>
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
