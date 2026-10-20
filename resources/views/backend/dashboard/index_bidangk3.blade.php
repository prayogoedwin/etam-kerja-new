@extends('backend.template.backend')

@section('content')

    <body class="box-layout container background-green">
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Dashboard Bidang K3c</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                                <img src="{{ asset('assets/etam_be/images/underconstruct.png') }}"
                                    alt="Under Construction"
                                    class="img-fluid mb-3"
                                    style="max-width: 320px; width: 100%; height: auto;">
                                <h5 class="mb-2">🚧 Sedang Dalam Pengembangan</h5>
                                <p class="text-muted small mb-0">
                                    Halaman ini sedang dalam tahap pengerjaan. Silakan kembali lagi nanti.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
@endsection
