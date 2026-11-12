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
                                <h5 class="m-b-10">Detail Ajuan Peraturan Perusahaan</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Detail Pengajuan</h5>
                            <a href="{{ route('hi.pp.admbidang.index') }}" class="btn btn-light btn-sm">
                                <i class="feather icon-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">

                            {{-- ========== STATUS VERIFIKASI ========== --}}
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Status Admin:</strong>
                                    @if ((int) $ajuan->verifikasi_admin === 8)
                                        <span class="badge bg-primary">Selesai</span>
                                    @elseif ((int) $ajuan->verifikasi_admin === 1)
                                        <span class="badge bg-success">ACC</span>
                                    @elseif ((int) $ajuan->verifikasi_admin === 2)
                                        <span class="badge bg-warning">Revisi</span>
                                    @else
                                        <span class="badge bg-secondary">Menunggu</span>
                                    @endif

                                    &nbsp;|&nbsp;
                                    <strong>Status Kasi:</strong>
                                    @if ((int) $ajuan->verifikasi_kasi === 8)
                                        <span class="badge bg-primary">Selesai</span>
                                    @elseif ((int) $ajuan->verifikasi_kasi === 1)
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

                                    @if ($ajuan->dok_revisi_admin)
                                        <hr class="my-2">
                                        <strong>Dokumen Revisi:</strong>
                                        <a href="{{ asset('storage/' . $ajuan->dok_revisi_admin) }}" target="_blank" class="text-primary">
                                            <i class="feather icon-file"></i> Lihat dokumen
                                        </a>
                                    @endif

                                    @if ($ajuan->batas_revisi)
                                        <hr class="my-2">
                                        <strong class="badge badge-light-danger">Batas Revisi:</strong>
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
                                        <th>Nomor Kepesertaan BPJS</th>
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
                                        <th>Link googledrive dokumen</th>
                                        <td>
                                            {{ $ajuan->link_gdrive_dokumen8 }}
                                            <br>
                                            <span class="badge badge-light-info">
                                                <small>Konsep Peraturan Perusahaan sebanyak 3 rangkap (tiap halaman wajib diparaf oleh manajemen perusahaan)</small>
                                            </span>
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

                            @if ($ajuan->dok_final_konsep_pp)
                                <div class="alert alert-primary">
                                    <strong>Dokumen Final Konsep PP:</strong>
                                    <a href="{{ asset('storage/' . $ajuan->dok_final_konsep_pp) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="feather icon-file"></i> Lihat Dokumen
                                    </a>
                                </div>
                            @endif

                            {{-- ========== RIWAYAT AKTIVITAS ========== --}}
                            <h6 class="text-muted mb-3 mt-4">Riwayat Aktivitas</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="18%">Waktu</th>
                                            <th width="15%">Oleh</th>
                                            <th>Aktivitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ajuan->logs as $i => $log)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    <small>{{ $log->created_at?->format('d-m-Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <strong>{{ $log->user->name ?? 'System' }}</strong><br>
                                                        <span class="badge bg-secondary">{{ $log->role ?? '-' }}</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    {{ $log->description ?? $log->action }}

                                                    @if (!empty($log->meta))
                                                        <details class="mt-1">
                                                            <summary class="text-muted small" style="cursor:pointer;">Detail</summary>
                                                            <pre class="small bg-light p-2 mt-1 mb-0" style="white-space: pre-wrap;">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                        </details>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">
                                                    <i class="feather icon-clock"></i> Belum ada aktivitas.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- ========== TOMBOL AKSI ========== --}}
                            <div class="d-flex justify-content-end mt-4 flex-wrap gap-2">
                                @php
                                    $accAdmin = (int) $ajuan->verifikasi_admin === 1;
                                    $accKasi  = (int) $ajuan->verifikasi_kasi === 1;
                                    $accFull  = $accAdmin && $accKasi;
                                    $hasSk    = !empty($ajuan->nomor_sk) && !empty($ajuan->tanggal_berlaku_pp_baru);
                                    $isDone   = (int) $ajuan->verifikasi_admin === 8;
                                @endphp

                                {{-- Tombol Final Konsep PP --}}
                                @if ($accFull && !$isDone)
                                    @if (!$ajuan->dok_final_konsep_pp)
                                        <a href="{{ route('hi.pp.admbidang.formFinalKonsep', $ajuan->id) }}"
                                           class="btn btn-dark">
                                            <i class="feather icon-file-plus"></i> Final Konsep PP
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $ajuan->dok_final_konsep_pp) }}"
                                           target="_blank"
                                           class="btn btn-outline-dark">
                                            <i class="feather icon-file"></i> Lihat Final Konsep PP
                                        </a>
                                    @endif
                                @endif

                                {{-- Tombol Verifikasi --}}
                                @if ((int) $ajuan->verifikasi_admin === 0)
                                    <a href="{{ route('hi.pp.admbidang.verifikasi', $ajuan->id) }}"
                                       class="btn btn-success">
                                        <i class="feather icon-check"></i> Aksi
                                    </a>
                                @elseif (!$isDone)
                                    <a href="{{ route('hi.pp.admbidang.verifikasi', $ajuan->id) }}"
                                       class="btn btn-warning">
                                        <i class="feather icon-refresh-cw"></i> Ubah Verifikasi
                                    </a>
                                @endif

                                {{-- Tombol SK --}}
                                @if ($accFull && !$isDone)
                                    @if (!$hasSk)
                                        <a href="{{ route('hi.pp.admbidang.formSk', $ajuan->id) }}"
                                           class="btn btn-primary">
                                            <i class="feather icon-edit"></i> Input Nomor SK
                                        </a>
                                    @else
                                        <a href="{{ route('hi.pp.admbidang.formSk', $ajuan->id) }}"
                                           class="btn btn-outline-primary">
                                            <i class="feather icon-edit"></i> Ubah Nomor SK
                                        </a>
                                        {{-- <a href="{{ route('hi.pp.admbidang.cetakSk', $ajuan->id) }}"
                                           target="_blank"
                                           class="btn btn-info">
                                            <i class="feather icon-printer"></i> Cetak SK
                                        </a> --}}
                                    @endif

                                @endif

                                @if ($ajuan->tteDokumenFinal?->token_verifikasi)
                                    <a href="{{ route('tte.berkas', ['token' => $ajuan->tteDokumenFinal->token_verifikasi, 'unduh' => 1]) }}"
                                       class="btn btn-outline-success">
                                        <i class="feather icon-download"></i> Unduh SK TTE
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
@endsection

@push('js')
<script>
// Kosong — script khusus bisa ditambahkan di sini kalau perlu
</script>
@endpush
