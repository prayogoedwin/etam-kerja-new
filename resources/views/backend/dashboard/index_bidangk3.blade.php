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
                                    <h5 class="m-b-10">Dashboard Bidang K3</h5>
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
                    {{-- Total --}}
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-file-text text-white" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Ajuan</p>
                                    <h4 class="mb-0">44</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Menunggu --}}
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-secondary bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-clock text-secondary" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Menunggu Verifikasi</p>
                                    <h4 class="mb-0">33</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACC --}}
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-check-circle" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Di-ACC</p>
                                    <h4 class="mb-0">22</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Revisi --}}
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-refresh-cw" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Revisi</p>
                                    <h4 class="mb-0">11</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </body>
@endsection
