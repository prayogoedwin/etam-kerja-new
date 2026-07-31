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
                                                <h4 class="text-c-yellow">{{ $lowonganPending }}</h4>
                                                <h6 class="text-muted m-b-0">Lowongan</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-bar-chart-2 f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-yellow">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">Lowongan Belum Di ACC</p>
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
                                                <h4 class="text-c-green">{{ $lamaranBelumProses }}</h4>
                                                <h6 class="text-muted m-b-0">Lamaran</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-file-text f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-green">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">Lamaran Belum Di Proses</p>
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
                                                <h4 class="text-c-green">57</h4>
                                                <h6 class="text-muted m-b-0">AK1</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-file-text f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-red">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">AK1 Cetak Hari Ini</p>
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
                                                <h4 class="text-c-yellow">{{ $pencariKerja }}</h4>
                                                <h6 class="text-muted m-b-0">Pencari Kerja Baru</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-bar-chart-2 f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-yellow">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">Pencari Kerja Baru Hari Ini</p>
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
                                                <h4 class="text-c-green">{{ $penyedia }}</h4>
                                                <h6 class="text-muted m-b-0">Perusahaan Baru</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-file-text f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-green">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">Perusahaan Baru Hari Ini</p>
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
                                                <h4 class="text-c-green">{{ $bkk }}</h4>
                                                <h6 class="text-muted m-b-0">BKK Baru</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <i class="feather icon-file-text f-28"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-c-red">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">BKK Baru Hari Ini</p>
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
                            $encExtbg = short_encode_url('1');
                            $allurl = '/depan/daftar?rl=' . $url_role . '&ex_tbg=' . $encExtbg;

                            // $url_role_perusahaan = encode_url('penyedia-kerja');
                            // $allurl_perusahaan = '/depan/daftar?rl=' . $url_role_perusahaan . '&bkk=' . $encUserId;
                        @endphp
                        <div class="card">
                            <div class="card-body">
                                <div class="row col-12">
                                    <div class="col-6">
                                        <span>URL Register Pencari Kerja Ex Tambang</span>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary"
                                            onclick="copyToClipboard('{{ url($allurl) }}', this)">
                                            <i class="feather icon-clipboard"></i>
                                        </a>
                                    </div>
                                    {{-- <div class="col-6">
                                        <span>URL Register Perusahaan</span>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-warning"
                                            onclick="copyToClipboard('{{ url($allurl_perusahaan) }}', this)">
                                            <i class="feather icon-clipboard"></i>
                                        </a>
                                    </div> --}}
                                </div>

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

@push('js')
    <script>
        function copyToClipboard(text, el) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    const originalIcon = el.innerHTML;
                    const originalClass = el.className;

                    el.innerHTML = '<i class="feather icon-check"></i>';
                    el.classList.remove('btn-primary', 'btn-warning');
                    el.classList.add('btn-success');

                    setTimeout(() => {
                        el.innerHTML = originalIcon;
                        el.className = originalClass;
                    }, 1500);
                })
                .catch(() => {
                    alert('Gagal menyalin URL');
                });
        }
    </script>
@endpush
