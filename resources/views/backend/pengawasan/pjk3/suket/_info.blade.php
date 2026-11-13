@php
    $fileRoute = $fileRouteName ?? 'pengawasan.suket.admin.file';
@endphp

<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Data pengajuan</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>PJK3:</strong> {{ $suket->profilPjk3->name ?? '-' }}</p>
                <p><strong>Perusahaan diuji:</strong> {{ $suket->profilPerusahaan->name ?? '-' }}</p>
                <p><strong>Alamat:</strong> {{ $suket->profilPerusahaan->alamat ?? '-' }}</p>
                <p><strong>Kategori:</strong> {{ $suket->kategori->nama ?? '-' }}
                    @if ($suket->kategori?->kode)
                        ({{ $suket->kategori->kode }})
                    @endif
                </p>
                <p><strong>Jenis alat:</strong> {{ $suket->jenis->nama ?? ($suket->perangkat->first()?->jenis->nama ?? '-') }}</p>
                <p><strong>Jumlah objek:</strong> {{ $suket->perangkat->isNotEmpty() ? $suket->perangkat->count() : 1 }} alat
                    ({{ $suket->jumlah_unit }} unit)</p>
            </div>
            <div class="col-md-6">
                <p><strong>Nama alat:</strong> {{ $suket->nama_alat }}</p>
                <p><strong>Lokasi:</strong> {{ $suket->lokasi_alat }}</p>
                <p><strong>Status:</strong> {{ $suket->statusLabel((bool) ($timelineAdmin ?? false)) }}</p>
                <p><strong>Nomor suket:</strong> {{ $suket->nomor_suket ?? '-' }}</p>
                <p><strong>Hasil:</strong>
                    @if ($suket->is_layak === null)
                        Belum dinilai
                    @elseif ((int) $suket->is_layak === 1)
                        <span class="badge bg-success">Layak</span>
                    @else
                        <span class="badge bg-danger">Tidak layak</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="mt-3 pt-3 border-top">
            @include('backend.pengawasan.pjk3.suket._timeline', [
                'suket' => $suket,
                'admin' => $timelineAdmin ?? false,
                'compact' => false,
            ])
        </div>
    </div>
    @if ($suket->perangkat->isNotEmpty())
        <div class="table-responsive px-3 pb-3">
            <table class="table table-bordered table-sm mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis</th>
                        <th>Nama objek</th>
                        <th>Merk</th>
                        <th>Jumlah</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suket->perangkat as $i => $alat)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $alat->jenis->nama ?? '-' }}</td>
                            <td>{{ $alat->nama }}</td>
                            <td>{{ $alat->merk ?: '-' }}</td>
                            <td>{{ $alat->jumlah ?: '-' }}</td>
                            <td>{{ $alat->lokasi ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Dokumen unggahan PJK3</h5>
    </div>
    <div class="card-body">
        <ul class="list-unstyled mb-0">
            @foreach ($dokumenKeys as $key => $label)
                @php $item = $suket->dokumen[$key] ?? null; @endphp
                <li class="mb-2">
                    <strong>{{ $label }}</strong><br>
                    @if (($item['tipe'] ?? null) === 'link' && !empty($item['value']))
                        <a href="{{ $item['value'] }}" target="_blank" rel="noopener">{{ $item['value'] }}</a>
                    @elseif (($item['tipe'] ?? null) === 'file' && !empty($item['value']))
                        <a href="{{ route($fileRoute, [$suket->id, $key]) }}" target="_blank">Lihat file</a>
                    @else
                        <span class="text-muted">Tidak ada</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

@if ($suket->pengawas_id || $suket->hasil_analisis)
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Hasil pemeriksaan</h5>
        </div>
        <div class="card-body">
            <p><strong>Pengawas:</strong> {{ $suket->pengawas->name ?? '-' }}</p>
            <p><strong>Kasi:</strong> {{ $suket->kasi->name ?? '-' }}</p>
            <p><strong>Catatan disposisi:</strong> {{ $suket->catatan_disposisi ?: '-' }}</p>
            <p><strong>Tanggal pemeriksaan:</strong>
                {{ optional($suket->tanggal_pemeriksaan)->format('d-m-Y') ?? '-' }}</p>
            <p><strong>Bidang Ahli K3:</strong> {{ $suket->namaBidangAhliK3() }}</p>
            <p><strong>Nama pengurus:</strong> {{ $suket->nilaiLhpp('nama_pengurus') ?: '-' }}</p>
            <p><strong>Nomor LHPP PJK3:</strong> {{ $suket->nilaiLhpp('nomor_lhpp_pjk3') ?: '-' }}</p>
            <p><strong>Hasil analisis:</strong><br>{!! nl2br(e($suket->hasil_analisis ?: '-')) !!}</p>
            <p><strong>Rekomendasi:</strong><br>{!! nl2br(e($suket->rekomendasi ?: '-')) !!}</p>
            <p><strong>TTD pengawas:</strong>
                {{ optional($suket->ttd_pengawas_at)->format('d-m-Y H:i') ?? 'Belum' }}</p>
            <p><strong>TTD Kepala Dinas:</strong>
                {{ optional($suket->ttd_kadis_at)->format('d-m-Y H:i') ?? 'Belum' }}</p>
        </div>
    </div>
@endif
