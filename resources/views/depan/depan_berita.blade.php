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
                <h1>Berita & Informasi</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Berita & Informasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Blog
    ============================================= -->
<div class="home-blog-area default-padding">

    <div class="container">
        <div class="row">
            <!-- Single Item -->
            <div class="col-lg-6 mt-md-30 mt-xs-30">
                <div class="blog-style-one solid mb-30">
                    <div class="thumb">
                        <img src="{{ asset('assets') }}/etam_fe/img/1500x800.png" alt="Image Not Found">
                        <div class="tags"><a href="#">Berita</a></div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li>
                                        <a href="#"><i class="fas fa-user"></i> ADMIN ETAM KERKA</a>
                                    </li>
                                    <li>
                                        20 Oktober, 2024
                                    </li>
                                </ul>
                            </div>
                            <h4>
                                <a href="blog-single-with-sidebar.html">Pelatikan Presiden Prabowo Subianto.</a>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="blog-style-one solid">
                    <div class="thumb">
                        <img src="{{ asset('assets') }}/etam_fe/img/1500x800.png" alt="Image Not Found">
                        <div class="tags"><a href="#">Pengumuman</a></div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li>
                                        <a href="#"><i class="fas fa-user"></i> ADMIN ETAM KERKA</a>
                                    </li>
                                    <li>
                                        01 Oktober, 2024
                                    </li>
                                </ul>
                            </div>
                            <h4>
                                <a href="blog-single-with-sidebar.html">Per Hari Ini Tersedia Lowongan 40 Baru.</a>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Single Item -->
            <!-- Single Item -->
            <div class="col-lg-6 mt-md-30 mt-xs-30">
                <div class="blog-style-one solid mb-30">
                    <div class="thumb">
                        <img src="{{ asset('assets') }}/etam_fe/img/1500x800.png" alt="Image Not Found">
                        <div class="tags"><a href="#">Kegiatan</a></div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li>
                                        <a href="#"><i class="fas fa-user"></i> ADMIN ETAM KERKA</a>
                                    </li>
                                    <li>
                                        27 Oktober, 2024
                                    </li>
                                </ul>
                            </div>
                            <h4>
                                <a href="blog-single-with-sidebar.html">Rapat Rencan Launching ETAM KERJA.</a>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="blog-style-one solid">
                    <div class="thumb">
                        <img src="{{ asset('assets') }}/etam_fe/img/1500x800.png" alt="Image Not Found">
                        <div class="tags"><a href="#">Infografis</a></div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li>
                                        <a href="#"><i class="fas fa-user"></i> ADMIN ETAM KERKA</a>
                                    </li>
                                    <li>
                                        29 Oktober, 2024
                                    </li>
                                </ul>
                            </div>
                            <h4>
                                <a href="blog-single-with-sidebar.html">Statistik Penempatan Kerja Terbaru.</a>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Single Item -->
        </div>
    </div>
</div>
<!-- End Blog  -->

@include('components.footer')
