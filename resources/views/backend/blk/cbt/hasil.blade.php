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
                                    <h5 class="m-b-10">Hasil CBT - {{ $pelatihan->nama_pelatihan }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="mb-1">Template: {{ $pelatihan->cbtForm->nama ?? '-' }}</p>
                                <p class="mb-0">Status tes:
                                    @if ((int) $pelatihan->cbt_buka === 1)
                                        <span class="badge bg-success">Dibuka</span>
                                    @else
                                        <span class="badge bg-secondary">Ditutup</span>
                                    @endif
                                    &nbsp; Kode: <strong>{{ $pelatihan->cbt_kode ?: '-' }}</strong>
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('blk.pelatihan.peserta', $pelatihan->id) }}"
                                    class="btn btn-info btn-sm">Peserta</a>
                                <a href="{{ route('blk.pelatihan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Nama</th>
                                        <th>{{ $isPerusahaan ? 'NIB' : 'NIK' }}</th>
                                        <th>Benar</th>
                                        <th>Salah</th>
                                        <th>Tidak diisi</th>
                                        <th>Nilai</th>
                                        <th>Waktu kirim</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $peringkat = 0; @endphp
                                    @forelse ($hasil as $item)
                                        <tr class="{{ $item['submitted'] ? '' : 'table-secondary' }}">
                                            <td>
                                                @if ($item['submitted'])
                                                    @php $peringkat++; @endphp
                                                    {{ $peringkat }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $item['peserta']->name }}</td>
                                            <td>{{ $isPerusahaan ? ($item['peserta']->nib ?? '-') : ($item['peserta']->ktp ?? '-') }}
                                            </td>
                                            @if ($item['submitted'])
                                                <td>{{ $item['score']['benar'] }}</td>
                                                <td>{{ $item['score']['salah'] }}</td>
                                                <td>{{ $item['score']['kosong'] }}</td>
                                                <td><strong>{{ $item['score']['nilai'] }}</strong> /
                                                    {{ $item['score']['total'] }}</td>
                                                <td>{{ $item['submitted_at'] ? \Illuminate\Support\Carbon::parse($item['submitted_at'])->format('d-m-Y H:i') : '-' }}</td>
                                                <td>
                                                    <a href="{{ route('blk.pelatihan.peserta.cbt', [$pelatihan->id, $item['peserta']->id]) }}"
                                                        class="btn btn-dark btn-sm">Lihat jawaban</a>
                                                </td>
                                            @else
                                                <td colspan="5" class="text-muted">Belum mengikuti tes</td>
                                                <td>
                                                    <a href="{{ route('blk.pelatihan.peserta.cbt', [$pelatihan->id, $item['peserta']->id]) }}"
                                                        class="btn btn-outline-secondary btn-sm">Detail</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Belum ada peserta.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection
