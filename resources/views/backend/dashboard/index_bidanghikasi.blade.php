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
                                    <h5 class="m-b-10">Dashboard Bidang HI</h5>
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
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-file-text text-white" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Siap Diverifikasi</p>
                                    <h4 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-secondary bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-clock text-secondary" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Menunggu Verifikasi</p>
                                    <h4 class="mb-0">{{ number_format($stats['menunggu'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-check-circle" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Di-ACC</p>
                                    <h4 class="mb-0">{{ number_format($stats['acc'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                    <i class="feather icon-refresh-cw" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Revisi</p>
                                    <h4 class="mb-0">{{ number_format($stats['revisi'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== BANNER + AKSI CEPAT ===== --}}
                <div class="row mb-3">
                    <div class="col-lg-8 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-1">Ajuan Bulan Ini</h5>
                                    <p class="text-muted mb-0 small">
                                        Total pengajuan yang masuk pada bulan {{ now()->translatedFormat('F Y') }}
                                    </p>
                                </div>
                                <h2 class="mb-0 text-info">{{ number_format($stats['bulan_ini'] ?? 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <a href="{{ route('hi.pp.kasibidang.index') }}"
                                class="btn btn-info btn-block mb-2 text-white">
                                    <i class="feather icon-list"></i> Lihat Semua Ajuan
                                </a>
                                <a href="{{ route('hi.pp.kasibidang.index') }}"
                                class="btn btn-outline-secondary btn-block">
                                    <i class="feather icon-clock"></i> Perlu Diverifikasi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== TABEL AJUAN TERBARU ===== --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Ajuan Terbaru (Sudah ACC Admin)</h5>
                                <a href="{{ route('hi.pp.kasibidang.index') }}" class="btn btn-sm btn-link">
                                    Lihat semua <i class="feather icon-arrow-right"></i>
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis Ajuan</th>
                                                <th>Nomor</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($terbaru as $i => $row)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $row->jenisAjuan->nama ?? '-' }}</td>
                                                    <td>{{ $row->nomor ?? '-' }}</td>
                                                    <td>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}</td>
                                                    <td>
                                                        @if ((int) $row->verifikasi_kasi === 1)
                                                            <span class="badge bg-success">ACC</span>
                                                        @elseif ((int) $row->verifikasi_kasi === 2)
                                                            <span class="badge bg-warning">Revisi</span>
                                                        @else
                                                            <span class="badge bg-secondary">Menunggu</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('hi.pp.kasibidang.detail', $row->id) }}"
                                                        class="btn btn-sm btn-outline-info">
                                                            Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <i class="feather icon-inbox" style="font-size: 32px;"></i>
                                                        <p class="mb-0 mt-2">Belum ada ajuan yang siap diverifikasi.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
@endsection
