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
                <h1>Lowongan Kerja Disabilitas</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Lowongan Kerja Disabilitas</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Blog
    ============================================= -->
<div class="blog-area blog-grid default-padding">
    <div class="container">
        <div class="esitmate-form2 mt-40">
            <form action="#">


                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="name">Judul Lowongan</label>
                            <input class="form-control" id="name" name="name"
                                placeholder="Cari Judul Lowongan Kerja" type="text">
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="subject">Pendidikan</label>
                            <select id="subject">
                                <option value="1">SD</option>
                                <option value="2">SMP</option>
                                <option value="4">SMA / SMK</option>
                                <option value="5">D1</option>
                                <option value="6">D2</option>
                                <option value="6">D3</option>
                                <option value="6">D4</option>
                                <option value="6">S1</option>
                                <option value="6">S2</option>
                                <option value="6">S3</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="subject">Lokasi Perusahaan</label>
                            <select id="subject">
                                <option value="1">Kabupaten Berau</option>
                                <option value="2">Kabupaten Kutai Barat</option>
                                <option value="4">Kabupaten Kutai Kartanegara</option>
                                <option value="5">Kabupaten Kutai Timur</option>
                                <option value="6">Kabupaten Mahakam Ulu</option>
                                <option value="6">Kabupaten Paser</option>
                                <option value="6">Kabupaten Penajam Paser Utara</option>
                                <option value="6">Kota Balikpapan</option>
                                <option value="6">Kota Bontang</option>
                                <option value="6">Kota Samarinda</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <button type="submit" name="submit" id="submit" class="btn btn-success">
                            Cari Lowongan Kerja
                        </button>
                    </div>


                </div>

            </form>
        </div>

        <div class="blog-item-box">
            <div class="row">
                <!-- Single Item -->
                <div class="col-xl-4 col-md-6 single-item">
                    <div class="blog-style-one">
                        <div class="thumb">
                            <a href="#"><img src="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                    alt="Thumb"></a>
                        </div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li class="sub-title">
                                        PT. Berlian Mas
                                    </li>

                                </ul>
                                <ul>

                                    <li>
                                        Expire in: 12 August, 2023
                                    </li>
                                </ul>
                            </div>
                            <h3>
                                <a href="blog-single-with-sidebar.html">Admin Toko.</a>
                            </h3>
                            <a href="#" class="btn-simple"><i class="fas fa-angle-right"></i> Read more</a>
                        </div>
                    </div>
                </div>
                <!-- Single Item -->
                <!-- Single Item -->
                <div class="col-xl-4 col-md-6 single-item">
                    <div class="blog-style-one">
                        <div class="thumb">
                            <a href="#"><img src="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                    alt="Thumb"></a>
                        </div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li class="sub-title">
                                        PT. Air Minum Gunung
                                    </li>

                                </ul>
                                <ul>

                                    <li>
                                        Expire in: 12 August, 2023
                                    </li>
                                </ul>
                            </div>
                            <h3>
                                <a href="blog-single-with-sidebar.html">Quality Control.</a>
                            </h3>
                            <a href="#" class="btn-simple"><i class="fas fa-angle-right"></i> Read more</a>
                        </div>
                    </div>
                </div>
                <!-- Single Item -->
                <!-- Single Item -->
                <div class="col-xl-4 col-md-6 single-item">
                    <div class="blog-style-one">
                        <div class="thumb">
                            <a href="#"><img src="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                    alt="Thumb"></a>
                        </div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li class="sub-title">
                                        PT. Elektronika Perkakas
                                    </li>

                                </ul>
                                <ul>

                                    <li>
                                        Expire in: 12 August, 2023
                                    </li>
                                </ul>
                            </div>
                            <h3>
                                <a href="blog-single-with-sidebar.html">Operator Mesin.</a>
                            </h3>
                            <a href="#" class="btn-simple"><i class="fas fa-angle-right"></i> Read more</a>
                        </div>
                    </div>
                </div>
                <!-- Single Item -->
                <!-- Single Item -->
                <div class="col-xl-4 col-md-6 single-item">
                    <div class="blog-style-one">
                        <div class="thumb">
                            <a href="#"><img src="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                    alt="Thumb"></a>
                        </div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li class="sub-title">
                                        PT. Batu Alam Permai
                                    </li>

                                </ul>
                                <ul>

                                    <li>
                                        Expire in: 12 August, 2023
                                    </li>
                                </ul>
                            </div>
                            <h3>
                                <a href="blog-single-with-sidebar.html">Carpenter.</a>
                            </h3>
                            <a href="#" class="btn-simple"><i class="fas fa-angle-right"></i> Read more</a>
                        </div>
                    </div>
                </div>
                <!-- Single Item -->
                <!-- Single Item -->
                <div class="col-xl-4 col-md-6 single-item">
                    <div class="blog-style-one">
                        <div class="thumb">
                            <a href="#"><img src="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                    alt="Thumb"></a>
                        </div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li class="sub-title">
                                        PT. Stone Bara
                                    </li>

                                </ul>
                                <ul>

                                    <li>
                                        Expire in: 12 August, 2023
                                    </li>
                                </ul>
                            </div>
                            <h3>
                                <a href="blog-single-with-sidebar.html">Operator Dump Truck.</a>
                            </h3>
                            <a href="#" class="btn-simple"><i class="fas fa-angle-right"></i> Read more</a>
                        </div>
                    </div>
                </div>
                <!-- Single Item -->
                <!-- Single Item -->
                <div class="col-xl-4 col-md-6 single-item">
                    <div class="blog-style-one">
                        <div class="thumb">
                            <a href="#"><img src="{{ asset('assets') }}/etam_fe/img/800x600.png"
                                    alt="Thumb"></a>
                        </div>
                        <div class="info">
                            <div class="blog-meta">
                                <ul>
                                    <li class="sub-title">
                                        PT. Sawit Hasil Bumi
                                    </li>

                                </ul>
                                <ul>

                                    <li>
                                        Expire in: 12 August, 2023
                                    </li>
                                </ul>
                            </div>
                            <h3>
                                <a href="blog-single-with-sidebar.html">Supervisor Lapangan.</a>
                            </h3>
                            <a href="#" class="btn-simple"><i class="fas fa-angle-right"></i> Read more</a>
                        </div>
                    </div>
                </div>
                <!-- Single Item -->
            </div>
        </div>
        <!-- Pagination -->
        <div class="row">
            <div class="col-md-12 pagi-area text-center">
                <nav aria-label="navigation">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#"><i
                                    class="fas fa-angle-double-left"></i></a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#"><i
                                    class="fas fa-angle-double-right"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- End Pagination -->
    </div>
</div>
<!-- End Blog -->
@include('components.footer')
