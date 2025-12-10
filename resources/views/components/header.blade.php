<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========== Meta Tags ========== -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Consua - Consulting Business Template">

    <!-- ========== Page Title ========== -->
    <title>ETAM KERJA - Disnakertrans Prov Kaltim</title>

    <!-- ========== Favicon Icon ========== -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/etam_fe/images/logo/icon_etam.png" type="image/x-icon">

    <!-- ========== Start Stylesheet ========== -->
    <link href="{{ asset('assets') }}/etam_fe/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/themify-icons.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/elegant-icons.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/flaticon-set.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/magnific-popup.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/swiper-bundle.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/animate.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/validnavs.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/helper.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/unit-test.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/css/style.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/style.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/etam_fe/custom_style.css" rel="stylesheet">
    <!-- ========== End Stylesheet ========== -->

</head>

<body>

    <!--[if lte IE 9]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->

    <!-- Start Preloader
    ============================================= -->
    <div id="preloader">
        <div id="consua-preloader" class="consua-preloader">
            <div class="animation-preloader">
                <div class="spinner"></div>
                <div class="txt-loading">
                    <span data-text-preloader="E" class="letters-loading">
                        E
                    </span>
                    <span data-text-preloader="T" class="letters-loading">
                        T
                    </span>
                    <span data-text-preloader="A" class="letters-loading">
                        A
                    </span>
                    <span data-text-preloader="M" class="letters-loading">
                        M
                    </span>
                    <span data-text-preloader="" class="letters-loading">

                    </span>
                    <span data-text-preloader="K" class="letters-loading">
                        K
                    </span>
                    <span data-text-preloader="E" class="letters-loading">
                        E
                    </span>
                    <span data-text-preloader="R" class="letters-loading">
                        R
                    </span>
                    <span data-text-preloader="J" class="letters-loading">
                        J
                    </span>
                    <span data-text-preloader="A" class="letters-loading">
                        A
                    </span>
                </div>
            </div>
            <div class="loader">
                <div class="row">
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Preloader -->


    <!-- Header
    ============================================= -->
    <header>
        <!-- Start Navigation -->
        {{-- <nav class="navbar mobile-sidenav navbar-sticky navbar-default validnavs navbar-fixed dark  no-background"> --}}
        <nav class="navbar mobile-sidenav navbar-common navbar-sticky navbar-default validnavs on menu-center no-full">

            <div class="container d-flex justify-content-between align-items-center">


                <!-- Start Header Navigation -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ asset('assets') }}/etam_fe/images/logo/LOGO_ETAM_KERJA.png" class="logo"
                            alt="Logo">
                    </a>
                </div>
                <!-- End Header Navigation -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu">

                    <div class="collapse-header">
                        <!-- <img src="{{ asset('assets') }}/etam_fe/img/logo.png" alt="Logo"> -->
                        <img src="{{ asset('assets') }}/etam_fe/images/logo/LOGO_ETAM_KERJA.png" alt="Logo">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <ul class="nav navbar-nav navbar-center" data-in="fadeInDown" data-out="fadeOutUp">

                        <li><a href="{{ url('/') }}">Beranda</a></li>


                        <!-- <li class="dropdown">
                            <a href="#" class="dropdown-toggle active" data-toggle="dropdown" >Home</a>
                            <ul class="dropdown-menu">
                                <li><a href="index.html">Consulting Business</a></li>
                                <li><li><a href="index-2.html">Corporate Business</a></li>
                                <li><li><a href="marketing-agency.html">Marketing Agency</a></li>
                                <li><li><a href="insurance.html">Insurance</a></li>
                                <li><li><a href="solar-energy.html">Solar Energy</a></li>
                                <li><li><a href="software-landing.html">Software Landing</a></li>
                            </ul>
                        </li> -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Lowongan</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ url('/depan/lowongan-kerja') }}">Lowongan Kerja</a></li>
                                <li><a href="{{ url('/depan/lowongan-kerja-disabilitas') }}">Lowongan Disabilitas</a>
                                </li>
                                <li><a href="{{ url('/depan/lowongan-magang') }}">Magang</a></li>
                            </ul>
                        </li>

                        <li><a href="{{ url('/depan/bkk') }}">BKK</a></li>

                        <li><a href="{{ url('/depan/jobfair') }}">JobFair</a></li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Informasi</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ url('/depan/infografis') }}">Infografis</a></li>
                                <li><a href="{{ url('/depan/galeri') }}">Galeri</a></li>
                                <li><a href="{{ url('/depan/berita') }}">Berita</a></li>
                                <!-- <li><a href="project.html">Kontak</a></li> -->
                            </ul>
                        </li>

                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li id="register-menu"><a href="#" onclick="askRoleRegister()">Register</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->

                <div class="attr-right">
                    <!-- Start Atribute Navigation -->
                    <div class="attr-nav">
                        <ul>
                            <li class="contact">
                                <div class="call">
                                    <div class="icon">
                                        <i class="fas fa-sign-in"></i>
                                    </div>
                                    <div class="info">
                                        <p>Belum memiliki akun?</p>
                                        {{-- <h5><a href="{{ url('/depan/register') }}">Daftar Sekarang</a></h5> --}}
                                        <h5><a href="#" onclick="askRoleRegister()">Daftar Sekarang</a></h5>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- End Atribute Navigation -->
                </div>

            </div>
            <!-- Overlay screen for menu -->
            <div class="overlay-screen"></div>
            <!-- End Overlay screen for menu -->
        </nav>
        <!-- End Navigation -->
    </header>
    <!-- End Header -->
