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
                                    <h5 class="m-b-10">Detail Ajuan PJK3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('backend.pengawasan.pjk3._detail', [
                    'canVerify' => false,
                    'fileRouteName' => 'pengawasan.pjk3.penyedia.file',
                ])

                <div class="mb-4">
                    @if ((int) $ajuan->status_verifikasi === \App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan::STATUS_REVISI)
                        <a href="{{ route('pengawasan.pjk3.penyedia.revisi', $ajuan->id) }}"
                            class="btn btn-warning">Upload ulang dokumen</a>
                    @endif
                    <a href="{{ route('pengawasan.pjk3.penyedia.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </body>
@endsection
