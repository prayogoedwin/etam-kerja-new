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
                <h1>BKK</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Beranda</a></li>
                    <li>BKK</li>
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
                        <h4 class="widget-title">Daftar BKK Terfadaftar Seluruh Provinsi Kalimantan Timur</h4>
                        <div class="content">
                            <table class="table">
                                <tr>
                                    <th>No</th>
                                    <th>Nama BKK</th>
                                    <th>Alamat</th>
                                    <th>Jurusan</th>
                                </tr>
                                @foreach ($data_bkk as $bkk)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bkk->name }}</td>
                                        <td>{{ $bkk->alamat }}</td>
                                        <td>bentar</td>
                                    </tr>
                                @endforeach
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
