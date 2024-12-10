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
                <h1>Lowongan</h1>
                <ul class="breadcrumb">
                    <li><a href="{{ url('/') }}"><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Detail Lowongan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Star Services Details Area
    ============================================= -->
<div class="services-details-area overflow-hidden default-padding">
    <div class="container">
        <div class="services-details-items">
            <div class="row">



                <div class="col-xl-12 col-lg-5 mt-md-120 mt-xs-50 services-sidebar">
                    <!-- Single Widget -->
                    <div class="single-widget services-list-widget">
                        <h4 class="widget-title">Detail Lowongan</h4>
                        <div class="content">
                            <table class="table">
                                <tr>
                                    <th>Perusahaan</th>
                                    <th>{{ $userPenyedia->name }}</th>
                                </tr>
                                <tr>
                                    <th>Alamat Perusahaan</th>
                                    <th>{{ $userPenyedia->alamat }}</th>
                                </tr>
                                <tr>
                                    <th>Judul Lowongan</th>
                                    <th>{{ $lowongan->judul_lowongan }}</th>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <th>{{ $lowongan->deskripsi }}</th>
                                </tr>
                                <tr>
                                    <th>Kisaran Gaji</th>
                                    <th>Rp. {{ number_format($lowongan->kisaran_gaji, 0, ',', '.') }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- End Single Widget -->

                </div>

            </div>
        </div>
    </div>
</div>
<!-- End Services Details Area -->

@include('components.footer')
