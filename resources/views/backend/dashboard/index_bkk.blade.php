@extends('backend.template.backend')

@section('content')

    <body class="box-layout container background-green">
        <!-- [ Main Content ] start -->
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Dashboard</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->
                <!-- [ Main Content ] start -->
                <div class="row">


                    <div class="col-lg-12 col-md-12">
                        <!-- page statustic card start -->
                        <div class="row">

                            <div class="col-sm-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="text-c-yellow">0</h4>
                                                <h6 class="text-muted m-b-0">Underconstruction</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-bar-chart-2 f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-yellow">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">Underconstruction!</p>
                                            </div>
                                            <div class="col-3 text-end">
                                                <i class="feather icon-trending-up text-white f-16"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="text-c-green">0</h4>
                                                <h6 class="text-muted m-b-0">Underconstruction</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-file-text f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-green">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">Underconstruction!</p>
                                            </div>
                                            <div class="col-3 text-end">
                                                <i class="feather icon-trending-up text-white f-16"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="text-c-green">0</h4>
                                                <h6 class="text-muted m-b-0">Underconstruction</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-file-text f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-red">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">Underconstruction!</p>
                                            </div>
                                            <div class="col-3 text-end">
                                                <i class="feather icon-trending-up text-white f-16"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        @php
                            $url_role = encode_url('pencari-kerja');
                            $userId = Auth::user()->id;
                            $encUserId = short_encode_url($userId);
                            $allurl = '/depan/daftar?rl=' . $url_role . '&bkk=' . $encUserId;
                        @endphp
                        <div class="card">
                            <div class="card-body">
                                <a href="{{ url($allurl) }}" target="_blank" class="btn btn-sm btn-primary">URL Register
                                    Alumni
                                    {{ Auth::user()->id }}</a>
                            </div>
                        </div>
                        <!-- page statustic card end -->
                    </div>


                </div>
                <!-- [ Main Content ] end -->
            </div>
        </div>
        <!-- Button trigger modal -->
    </body>
@endsection
