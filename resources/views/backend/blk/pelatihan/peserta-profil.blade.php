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
                                    <h5 class="m-b-10">Profil Peserta - {{ $peserta->name }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $status = (int) $peserta->status_pendaftaran;
                    $statusMap = [0 => 'warning', 1 => 'info', 2 => 'success', 3 => 'danger', 4 => 'secondary', 5 => 'primary'];
                    $foto = $profil->foto ?? null;
                @endphp

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-body text-center">
                                @if ($foto)
                                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto peserta"
                                        class="img-radius mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('assets/etam_be/images/user/avatar-x.png') }}" alt="Foto peserta"
                                        class="img-radius mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                @endif
                                <h5 class="mb-1">{{ $peserta->name }}</h5>
                                <p class="text-muted mb-2">{{ $isPerusahaan ? 'Pemberi Kerja' : 'Pencari Kerja' }}</p>
                                <span class="badge bg-{{ $statusMap[$status] ?? 'secondary' }}">
                                    {{ \App\Models\BLK\EtamBlkPelatihanPeserta::statusLabels()[$status] ?? '-' }}
                                </span>
                                @if ($peserta->alasan_status)
                                    <p class="mt-2 mb-0"><small>Catatan: {{ $peserta->alasan_status }}</small></p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Data Profil</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if ($isPerusahaan)
                                        <div class="col-md-6 mb-2"><strong>NIB</strong><br>{{ $peserta->nib ?: ($profil->nib ?? '-') }}</div>
                                        <div class="col-md-6 mb-2"><strong>Email</strong><br>{{ $peserta->email ?: ($profil->user->email ?? '-') }}</div>
                                        <div class="col-md-6 mb-2"><strong>HP</strong><br>{{ $peserta->hp ?: ($profil->user->whatsapp ?? '-') }}</div>
                                        <div class="col-md-6 mb-2"><strong>Sektor</strong><br>{{ $profil->sektor->name ?? '-' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Provinsi</strong><br>{{ $profil->provinsi->name ?? '-' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Kab/Kota</strong><br>{{ $profil->kabkota->name ?? '-' }}</div>
                                        <div class="col-md-12 mb-2"><strong>Alamat</strong><br>{{ $peserta->alamat ?: ($profil->alamat ?? '-') }}</div>
                                    @else
                                        <div class="col-md-6 mb-2"><strong>NIK</strong><br>{{ $peserta->ktp ?: ($profil->ktp ?? '-') }}</div>
                                        <div class="col-md-6 mb-2"><strong>Email</strong><br>{{ $peserta->email ?: ($profil->user->email ?? '-') }}</div>
                                        <div class="col-md-6 mb-2"><strong>HP</strong><br>{{ $peserta->hp ?: ($profil->user->whatsapp ?? '-') }}</div>
                                        <div class="col-md-6 mb-2"><strong>Jenis Kelamin</strong><br>
                                            {{ ($peserta->gender ?? $profil->gender ?? '') === 'P' ? 'Perempuan' : ((($peserta->gender ?? $profil->gender ?? '') === 'L') ? 'Laki-laki' : '-') }}
                                        </div>
                                        <div class="col-md-6 mb-2"><strong>Tempat, Tanggal Lahir</strong><br>
                                            {{ $peserta->tempat_lahir ?: ($profil->tempat_lahir ?? '-') }},
                                            {{ optional($peserta->tanggal_lahir)->format('d-m-Y') ?: '-' }}
                                        </div>
                                        <div class="col-md-6 mb-2"><strong>Agama</strong><br>{{ $profil->agama->name ?? '-' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Status Perkawinan</strong><br>{{ $maritalName ?: '-' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Pendidikan</strong><br>{{ $profil->pendidikan->name ?? '-' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Jurusan</strong><br>{{ $profil->jurusan->nama ?? '-' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Provinsi</strong><br>{{ $profil->provinsi->name ?? '-' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Kab/Kota</strong><br>{{ $profil->kabkota->name ?? '-' }}</div>
                                        <div class="col-md-12 mb-2"><strong>Alamat</strong><br>{{ $peserta->alamat ?: ($profil->alamat ?? '-') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Riwayat Pelatihan BLK</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pelatihan</th>
                                                <th>BLK</th>
                                                <th>Pelaksanaan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($riwayat as $item)
                                                <tr @class(['table-success' => (int) $item->blk_pelatihan_id === (int) $pelatihan->id])>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $item->pelatihan->nama_pelatihan ?? '-' }}
                                                        @if ((int) $item->blk_pelatihan_id === (int) $pelatihan->id)
                                                            <span class="badge bg-success">Saat ini</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->pelatihan->blk->nama_lembaga ?? '-' }}</td>
                                                    <td>
                                                        {{ optional(optional($item->pelatihan)->tanggal_pelaksanaan)->format('d-m-Y') ?? '-' }}
                                                        s/d
                                                        {{ optional(optional($item->pelatihan)->tanggal_pelaksanaan_selesai)->format('d-m-Y') ?? '-' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $statusMap[(int) $item->status_pendaftaran] ?? 'secondary' }}">
                                                            {{ \App\Models\BLK\EtamBlkPelatihanPeserta::statusLabels()[(int) $item->status_pendaftaran] ?? '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Belum ada riwayat pelatihan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (! $isPerusahaan)
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Riwayat Melamar Kerja</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Lowongan</th>
                                                <th>Perusahaan</th>
                                                <th>Tipe</th>
                                                <th>Tanggal Lamar</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $tipeLowongan = \App\Http\Controllers\BLK\PesertaController::tipeLowonganLabels();
                                                $lamaranWarna = [1 => 'warning', 2 => 'info', 3 => 'success', 4 => 'warning', 5 => 'danger'];
                                            @endphp
                                            @forelse ($riwayatLamaran as $lamaran)
                                                @php
                                                    $progresId = (int) $lamaran->progres_id;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $lamaran->lowongan->judul_lowongan ?? '-' }}</td>
                                                    <td>{{ $lamaran->lowongan->nama_perusahaan_bybkk ?: ($lamaran->lowongan->userPenyedia->name ?? '-') }}</td>
                                                    <td>{{ $tipeLowongan[(int) ($lamaran->lowongan->tipe_lowongan ?? 0)] ?? '-' }}</td>
                                                    <td>{{ optional($lamaran->created_at)->format('d-m-Y H:i') ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $lamaranWarna[$progresId] ?? 'secondary' }}">
                                                            {{ $progresLamaran[$progresId] ?? ($progresLamaran[(string) $progresId] ?? '-') }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $lamaran->keterangan ?: '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Belum ada riwayat lamaran kerja.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-xl-12 mb-4">
                        <a href="{{ route('blk.pelatihan.peserta', $pelatihan->id) }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection
