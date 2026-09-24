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
                                    <h5 class="m-b-10">Detail Ajuan Peraturan Perusahaan</h5>
                                </div>
                                {{-- <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="#!">Hospital</a></li>
                                    <li class="breadcrumb-item"><a href="#!">Department</a></li>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Detail Pengajuan</h5>
                                <a href="{{ route('hi.pp.kasibidang.index') }}" class="btn btn-light btn-sm">
                                    <i class="feather icon-arrow-left"></i> Kembali
                                </a>
                            </div>
                            <div class="card-body">

                                {{-- ========== STATUS ========== --}}
                                <div class="alert alert-info d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Status Admin:</strong>
                                        @if ((int) $ajuan->verifikasi_admin === 1)
                                            <span class="badge bg-success">ACC</span>
                                        @elseif ((int) $ajuan->verifikasi_admin === 2)
                                            <span class="badge bg-warning">Revisi</span>
                                        @else
                                            <span class="badge bg-secondary">Menunggu</span>
                                        @endif

                                        &nbsp;|&nbsp;
                                        <strong>Status Kasi:</strong>
                                        @if ((int) $ajuan->verifikasi_kasi === 1)
                                            <span class="badge bg-success">ACC</span>
                                        @elseif ((int) $ajuan->verifikasi_kasi === 2)
                                            <span class="badge bg-warning">Revisi</span>
                                        @else
                                            <span class="badge bg-secondary">Menunggu</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($ajuan->verifikasi_admin === 2 && $ajuan->keterangan_revisi_admin)
                                    <div class="alert alert-warning">
                                        <strong>Catatan Revisi Admin:</strong><br>
                                        {{ $ajuan->keterangan_revisi_admin }}
                                    </div>
                                @endif

                                @if ($ajuan->verifikasi_kasi === 2 && $ajuan->keterangan_revisi_kasi)
                                    <div class="alert alert-warning">
                                        <strong>Catatan Revisi Kasi:</strong><br>
                                        {{ $ajuan->keterangan_revisi_kasi }}
                                        @if ($ajuan->batas_revisi)
                                            <hr class="my-2">
                                            <strong>Batas Revisi:</strong>
                                            {{ \Carbon\Carbon::parse($ajuan->batas_revisi)->format('d-m-Y') }}
                                        @endif
                                    </div>
                                @endif

                                {{-- ========== INFORMASI PERUSAHAAN ========== --}}
                                <h6 class="text-muted mb-3">Informasi Perusahaan</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="30%">Nama Perusahaan</th>
                                            <td>{{ $ajuan->perusahaan->penyedia->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $ajuan->perusahaan->email ?? '-' }}</td>
                                        </tr>
                                        @if ($ajuan->perusahaan && $ajuan->perusahaan->penyedia)
                                            <tr>
                                                <th>Alamat</th>
                                                <td>{{ $ajuan->perusahaan->penyedia->alamat ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Telepon</th>
                                                <td>{{ $ajuan->perusahaan->penyedia->telpon ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status Perusahaan</th>
                                                <td>{{ getFullCompanyType($ajuan->perusahaan->penyedia->jenis_perusahaan) ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Sektor Bidang Usaha</th>
                                                <td>{{ getSektorById($ajuan->perusahaan->penyedia->id_sektor)->name ?? '-' }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="2" class="text-muted">
                                                    <em>Profil perusahaan belum dilengkapi.</em>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>

                                <hr>

                                {{-- ========== DATA PENGAJUAN ========== --}}
                                <h6 class="text-muted mb-3">Data Pengajuan</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="30%">Jenis Ajuan</th>
                                            <td>{{ $ajuan->jenisAjuan->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Surat Keputusan Izin Usaha</th>
                                            <td>{{ $ajuan->surat_keputusan_izin_usaha ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor</th>
                                            <td>{{ $ajuan->nomor ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>{{ $ajuan->tanggal ? \Carbon\Carbon::parse($ajuan->tanggal)->format('d-m-Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Serikat Pekerja</th>
                                            <td>{{ $ajuan->nama_serikat_pekerja ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Peserta BPJS</th>
                                            <td>{{ $ajuan->nomor_peserta_bpjs ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Pekerja Pusat</th>
                                            <td>{{ $ajuan->jumlah_pekerja_pusat ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Pekerja Cabang</th>
                                            <td>{{ $ajuan->jumlah_pekerja_cabang ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <th>Upah Bulanan Min</th>
                                            <td>Rp {{ number_format($ajuan->upah_pekerja_bulanan_min ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Upah Bulanan Max</th>
                                            <td>Rp {{ number_format($ajuan->upah_pekerja_bulanan_max ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Upah Harian Min</th>
                                            <td>Rp {{ number_format($ajuan->upah_pekerja_harian_min ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Upah Harian Max</th>
                                            <td>Rp {{ number_format($ajuan->upah_pekerja_harian_max ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sistem Kerja Waktu Tertentu</th>
                                            <td>{{ $ajuan->sistem_hub_kerja_tertentu ?? 0 }} orang</td>
                                        </tr>
                                        <tr>
                                            <th>Sistem Kerja Waktu Tidak Tertentu</th>
                                            <td>{{ $ajuan->sistem_hub_kerja_tidak_tertentu ?? 0 }} orang</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                Link googledrive dokumen
                                            </th>
                                            <td>
                                                {{ $ajuan->link_gdrive_dokumen8 }}
                                                <br>
                                                <span class="badge badge-light-info"><small>Konsep Peraturan Perusahaan sebanyak 3 rangkap (tiap halaman wajib diparaf oleh manajemen perusahaan)</small></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                {{-- ========== DOKUMEN UNGGAHAN ========== --}}
                                <h6 class="text-muted mb-3">Dokumen Unggahan</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Syarat Dokumen</th>
                                                <th width="25%">File</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($syaratDokumen as $i => $dok)
                                                @php $existing = $uploadedDokumen[$dok->id] ?? null; @endphp
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $dok->nama }}</td>
                                                    <td>
                                                        @if ($existing)
                                                            <a href="{{ asset('storage/' . $existing) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary">
                                                                <i class="feather icon-file"></i> Lihat
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Belum diunggah</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Tidak ada syarat dokumen.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- ========== TOMBOL VERIFIKASI ========== --}}
                                <div class="d-flex justify-content-end mt-4">
                                    @if ((int) $ajuan->verifikasi_kasi === 0)
                                        <a href="{{ route('hi.pp.kasibidang.verifikasi', $ajuan->id) }}"
                                        class="btn btn-success">
                                            <i class="feather icon-check"></i> Verifikasi
                                        </a>
                                    @else
                                        <a href="{{ route('hi.pp.kasibidang.verifikasi', $ajuan->id) }}"
                                        class="btn btn-warning">
                                            <i class="feather icon-refresh-cw"></i> Ubah Verifikasi
                                        </a>
                                    @endif
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

