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
                                    <h5 class="m-b-10">Pendaftaran Pelatihan</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>{{ $pelatihan->nama_pelatihan }}</h5>
                                <p class="text-muted mb-1">BLK: {{ $pelatihan->blk->nama_lembaga ?? '-' }}</p>
                                <p class="mb-1">Pendaftaran:
                                    {{ optional($pelatihan->tanggal_pendaftaran)->format('d-m-Y') }} s/d
                                    {{ optional($pelatihan->tanggal_pendaftaran_selesai)->format('d-m-Y') }}</p>
                                <p class="mb-1">Pelaksanaan:
                                    {{ optional($pelatihan->tanggal_pelaksanaan)->format('d-m-Y') }} s/d
                                    {{ optional($pelatihan->tanggal_pelaksanaan_selesai)->format('d-m-Y') }}</p>
                                <p class="mb-3">Lokasi: {{ $pelatihan->info_lokasi ?: '-' }}</p>

                                @if ($pelatihan->deskripsi)
                                    <p>{{ $pelatihan->deskripsi }}</p>
                                @endif

                                @if ($pelatihan->syarat->count())
                                    <h6>Persyaratan</h6>
                                    <ul>
                                        @foreach ($pelatihan->syarat as $item)
                                            <li>{{ $item->persyaratan }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($pelatihan->fasilitas->count())
                                    <h6>Fasilitas</h6>
                                    <ul>
                                        @foreach ($pelatihan->fasilitas as $item)
                                            <li>{{ $item->fasilitas }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($already)
                                    <div class="alert alert-info">
                                        Anda sudah terdaftar. Status:
                                        {{ \App\Models\BLK\EtamBlkPelatihanPeserta::statusLabels()[(int) $already->status_pendaftaran] ?? '-' }}
                                    </div>
                                @elseif ($pelatihan->isOpenForRegistration())
                                    <form action="{{ route('blk.pelatihan.daftar.store', $pelatihan->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Daftar Pelatihan</button>
                                        <a href="{{ route('blk.pelatihan.index') }}" class="btn btn-secondary">Kembali</a>
                                    </form>
                                @else
                                    <div class="alert alert-warning">Pendaftaran pelatihan ini sedang ditutup.</div>
                                    <a href="{{ route('blk.pelatihan.index') }}" class="btn btn-secondary">Kembali</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection
