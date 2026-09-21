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
                                    <h5 class="m-b-10">Dashboard BLK</h5>
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
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="text-c-yellow">{{ $jumlahPelatihan }}</h4>
                                        <h6 class="text-muted m-b-0">Pelatihan</h6>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="feather icon-book f-28"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-c-yellow">
                                <p class="text-white m-b-0">Total pelatihan BLK</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="text-c-green">{{ $jumlahPeserta }}</h4>
                                        <h6 class="text-muted m-b-0">Peserta Pencari Kerja</h6>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="feather icon-users f-28"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-c-green">
                                <p class="text-white m-b-0">Pendaftar pencari kerja</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="text-c-blue">{{ $jumlahPesertaPerusahaan }}</h4>
                                        <h6 class="text-muted m-b-0">Peserta Pemberi Kerja</h6>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="feather icon-briefcase f-28"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-c-blue">
                                <p class="text-white m-b-0">Pendaftar pemberi kerja</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection
