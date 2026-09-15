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
                                    <h5 class="m-b-10">Peraturan Perusahaan</h5>
                                </div>
                                {{-- <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="{{route('hi.pp.penyedia.index')}}">Peraturan Perusahaan</a></li>
                                    <li class="breadcrumb-item"><a href="#!">/ Tambah</a></li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->


                <!-- [ Main Content ] start -->
                <div class="row">


                   <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-12">
                                    <h5 class="mb-3 mt-4">Tambah</h5>
                                    <div class="bt-wizard" id="verticalwizard">
                                        <div class="row align-items-stratched mb-4">
                                            <div class="col-12 col-md-auto col-sm-12">
                                                <div class="card h-100 mb-0">
                                                    <div class="card-body">
                                                        <ul class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                                                            <li class="nav-item"><a href="#v-tabs-t-tab1" class="nav-link has-ripple active" data-bs-toggle="tab">Input<span class="ripple ripple-animate" style="height: 84.9453px; width: 84.9453px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(0, 0, 238); opacity: 0.4; top: -25.5547px; left: 5.45312px;"></span></a></li>
                                                            <li class="nav-item"><a href="#v-tabs-t-tab2" class="nav-link has-ripple" data-bs-toggle="tab">Unggah<span class="ripple ripple-animate" style="height: 84.9453px; width: 84.9453px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(0, 0, 238); opacity: 0.4; top: -17.5547px; left: 27.4531px;"></span></a></li>
                                                            <li class="nav-item"><a href="#v-tabs-t-tab3" class="nav-link has-ripple" data-bs-toggle="tab">Konfirmasi<span class="ripple ripple-animate" style="height: 84.9453px; width: 84.9453px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(0, 0, 238); opacity: 0.4; top: -25.5547px; left: 17.4531px;"></span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="tab-content card mb-0" id="v-pills-tabContent">
                                                    <div class="tab-pane card-body show active" id="v-tabs-t-tab1">
                                                        <form>
                                                            <div class="form-group row">
                                                                <label for="v-tabs-t-name" class="col-sm-3 col-form-label">Name</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control" id="v-tabs-t-name" placeholder="Password">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label for="v-tabs-t-email" class="col-sm-3 col-form-label">Email</label>
                                                                <div class="col-sm-9">
                                                                    <input type="email" class="form-control" id="v-tabs-t-email" placeholder="Email">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label for="v-tabs-t-pwd" class="col-sm-3 col-form-label">Password</label>
                                                                <div class="col-sm-9">
                                                                    <input type="password" class="form-control" id="v-tabs-t-pwd" placeholder="Password">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="tab-pane card-body" id="v-tabs-t-tab2">
                                                        <form>
                                                            <div class="form-group row">
                                                                <label for="v-tabs-t-sate" class="col-sm-3 col-form-label">State</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control" id="v-tabs-t-sate">
                                                                        <option>Select State</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label for="v-tabs-t-address" class="col-sm-3 col-form-label">Address</label>
                                                                <div class="col-sm-9">
                                                                    <textarea class="form-control" id="v-tabs-t-address" rows="3" spellcheck="false"></textarea>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="tab-pane card-body" id="v-tabs-t-tab3">
                                                        <form class="text-center">
                                                            <i class="feather icon-check-circle display-3 text-success"></i>
                                                            <h5 class="mt-3">Registration Done! . .</h5>
                                                            <p>Lorem Ipsum is simply dummy text of the printing</p>
                                                            <div class="form-check mb-3">
                                                                <input type="checkbox" class="form-check-input" id="customCheck2">
                                                                <label class="form-check-label" for="customCheck2">Subscribe Newslatter</label>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    {{-- <div class="row justify-content-between card-footer mx-0 btn-page">
                                                        <div class="col-sm-6 ps-0">
                                                            <a href="#!" class="btn btn-primary button-previous disabled">Previous</a>
                                                        </div>
                                                        <div class="col-sm-6 text-md-right pe-0">
                                                            <a href="#!" class="btn btn-primary button-next disabled">Next</a>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- customar project  end -->


                </div>
                <!-- [ Main Content ] end -->


            </div>
        </div>

    </body>
@endsection


@push('js')
<script>

</script>

@endpush

