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
                                    <h5 class="m-b-10">TTE dalam pemeliharaan</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    Fitur Tanda Tangan Elektronik sedang dalam pemeliharaan untuk menangani TTE yang gagal ditandatangani.
                                    Status TTD peserta belum diubah.
                                </div>
                                <p class="mb-3">Peserta yang dipilih ({{ $peserta->count() }}):</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>NIK</th>
                                                <th>Pelatihan</th>
                                                <th>UPTD / BLK</th>
                                                <th>Status TTD</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($peserta as $i => $row)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $row->name }}</td>
                                                    <td>{{ $row->ktp ?? '-' }}</td>
                                                    <td>{{ $row->pelatihan->nama_pelatihan ?? '-' }}</td>
                                                    <td>{{ $row->pelatihan->blk->nama_lembaga ?? '-' }}</td>
                                                    <td>{!! \App\Models\BLK\EtamBlkPelatihanPeserta::statusTtdBadge((int) $row->status_ttd) !!}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada peserta terpilih.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('blk.ttd.menunggu') }}" class="btn btn-primary">Kembali ke antrean TTD</a>
                                    <a href="{{ route('blk.ttd.sudah') }}" class="btn btn-secondary">Sudah ditandatangani</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection
