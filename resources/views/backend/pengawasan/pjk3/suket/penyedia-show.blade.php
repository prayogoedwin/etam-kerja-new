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
                                    <h5 class="m-b-10">
                                        {{ ($sebagai ?? 'pjk3') === 'uji' ? 'Suket Layak K3' : 'Detail Pengajuan Suket' }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ((int) $suket->status >= \App\Models\Pengawasan\Pjk3\EtamPjk3Suket::STATUS_DRAFT && $suket->isDraft())
                    <div class="alert alert-warning">Dokumen masih berstatus draft sampai ditandatangani Kepala Dinas.</div>
                @endif

                @include('backend.pengawasan.pjk3.suket._info', [
                    'fileRouteName' => 'pengawasan.suket.penyedia.file',
                ])

                @if ((int) $suket->status >= \App\Models\Pengawasan\Pjk3\EtamPjk3Suket::STATUS_DRAFT)
                    <a href="{{ route('pengawasan.suket.penyedia.cetak-lhpp', $suket->id) }}" target="_blank"
                        class="btn btn-primary mb-2">Cetak LHPP</a>
                    <a href="{{ route('pengawasan.suket.penyedia.cetak-suket', $suket->id) }}" target="_blank"
                        class="btn btn-success mb-2">Cetak Suket</a>
                @endif

                <a href="{{ ($sebagai ?? 'pjk3') === 'uji' ? route('pengawasan.suket.penyedia.terbit.index') : route('pengawasan.suket.penyedia.pengajuan.index') }}"
                    class="btn btn-secondary mb-4">Kembali</a>
            </div>
        </div>
    </body>
@endsection
