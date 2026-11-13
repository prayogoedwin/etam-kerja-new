@php
    $perusahaan = $suket->profilPerusahaan;
    $pjk3 = $suket->profilPjk3;
    $untukDs = $untukDs ?? false;
    $tampilDraft = $untukDs ? false : (bool) ($tampilDraft ?? $suket->isDraft());
    $tampilFooter = $untukDs || ! $tampilDraft;
    $logoBsre = $logoBsre ?? null;
    $bsreTeks = $bsreTeks ?? '';
    $hasil = (int) $suket->is_layak === 1 ? 'LAYAK' : 'TIDAK LAYAK';
    $perangkat = $suket->perangkat;
    $teknis = $perangkat->isNotEmpty() ? $perangkat : $suket->nilaiLhpp('data_teknis', []);
    $ujiJson = $suket->nilaiLhpp('item_uji', []);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>LHPP {{ $suket->nomor_suket }}</title>
    @include('backend.pengawasan.pjk3.suket._cetak-k3-style')
</head>
<body>
    @if ($tampilDraft)
        <div class="watermark">DRAFT</div>
    @endif
    @unless ($untukDs)
        <p class="no-print"><button onclick="window.print()">Cetak</button></p>
    @endunless
    <div class="kop">
        <h3>PEMERINTAH PROVINSI KALIMANTAN TIMUR</h3>
        <h4>DINAS TENAGA KERJA DAN TRANSMIGRASI</h4>
        <p>Bidang Pembinaan dan Pengawasan Ketenagakerjaan</p>
        <p>Laporan Hasil Pemeriksaan dan Pengujian (LHPP) K3</p>
        <p>Ahli K3 Bidang {{ $suket->namaBidangAhliK3() }}</p>
    </div>

    <h4>1. Dasar pelaksanaan</h4>
    <p>{!! nl2br(e($suket->nilaiLhpp('dasar_hukum') ?: '-')) !!}</p>

    <h4>2. Waktu dan tempat pengujian</h4>
    <table class="data">
        <tr>
            <td width="32%">Tanggal pemeriksaan</td>
            <td>: {{ optional($suket->tanggal_pemeriksaan)->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tempat pengujian</td>
            <td>: {{ $suket->nilaiLhpp('tempat_pengujian') ?: ($suket->lokasi_alat ?: '-') }}</td>
        </tr>
        <tr>
            <td>PJK3 pemeriksa</td>
            <td>: {{ $pjk3->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor LHPP PJK3</td>
            <td>: {{ $suket->nilaiLhpp('nomor_lhpp_pjk3') ?: '-' }}</td>
        </tr>
    </table>

    <h4>3. Data perusahaan</h4>
    <table class="data">
        <tr><td width="32%">Nama perusahaan</td><td>: {{ $perusahaan->name ?? '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $perusahaan->alamat ?? '-' }}</td></tr>
        <tr><td>Nama pengurus</td><td>: {{ $suket->nilaiLhpp('nama_pengurus') ?: '-' }}</td></tr>
        <tr><td>Jenis usaha</td><td>: {{ $suket->nilaiLhpp('jenis_usaha') ?: '-' }}</td></tr>
        <tr><td>Jumlah pekerja</td><td>: {{ $suket->nilaiLhpp('jumlah_pekerja') !== '' && $suket->nilaiLhpp('jumlah_pekerja') !== null ? $suket->nilaiLhpp('jumlah_pekerja') : '-' }}</td></tr>
        <tr><td>WLKP</td><td>: {{ $suket->nilaiLhpp('nomor_wlkp') ?: '-' }}</td></tr>
        <tr><td>NIB</td><td>: {{ $perusahaan->nib ?? '-' }}</td></tr>
    </table>

    <h4>4. Objek K3</h4>
    <table class="data">
        <tr><td width="32%">Objek / alat</td><td>: {{ $suket->nama_alat }}</td></tr>
        <tr><td>Jenis / kategori</td><td>: {{ $suket->jenis->nama ?? '-' }} / {{ $suket->kategori->nama ?? '-' }}</td></tr>
        <tr><td>Lokasi alat</td><td>: {{ $suket->lokasi_alat }}</td></tr>
        <tr><td>Nomor unit</td><td>: {{ $suket->nilaiLhpp('nomor_unit') ?: '-' }}</td></tr>
        <tr><td>Nomor seri</td><td>: {{ $suket->nilaiLhpp('nomor_seri') ?: '-' }}</td></tr>
        <tr><td>Pabrik</td><td>: {{ $suket->nilaiLhpp('pabrik') ?: '-' }}</td></tr>
        <tr><td>Model</td><td>: {{ $suket->nilaiLhpp('model') ?: '-' }}</td></tr>
        <tr><td>Negara pembuat</td><td>: {{ $suket->nilaiLhpp('negara_pembuat') ?: '-' }}</td></tr>
        <tr><td>Tahun pasang</td><td>: {{ $suket->nilaiLhpp('tahun_pasang') ?: '-' }}</td></tr>
        <tr><td>Kapasitas / jumlah</td><td>: {{ $suket->kapasitas_alat ?: '-' }} / {{ $suket->jumlah_unit }} unit</td></tr>
    </table>

    <h4>5. Data teknis objek pengujian</h4>
    @if ($perangkat->isNotEmpty())
        <table class="data">
            @foreach ($perangkat as $i => $row)
                <tr>
                    <td width="32%" valign="top">{{ $i < 26 ? chr(97 + $i) : $i + 1 }}. {{ $row->nama ?: ($row->jenis->nama ?? '-') }}</td>
                    <td>
                        Merk : {{ $row->merk ?: '-' }}<br>
                        Jumlah : {{ $row->jumlah ?: '-' }}<br>
                        Lokasi : {{ $row->lokasi ?: '-' }}
                    </td>
                </tr>
            @endforeach
        </table>
    @elseif (is_array($teknis) && count($teknis))
        <table class="data">
            @foreach ($teknis as $i => $row)
                <tr>
                    <td width="32%" valign="top">{{ $i < 26 ? chr(97 + $i) : $i + 1 }}. {{ $row['nama'] ?? '-' }}</td>
                    <td>
                        Merk : {{ $row['merk'] ?? '-' }}<br>
                        Jumlah : {{ $row['jumlah'] ?? '-' }}<br>
                        Lokasi : {{ $row['lokasi'] ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p>-</p>
    @endif

    <h4>6. Analisis pengujian</h4>
    @if ($perangkat->isNotEmpty())
        <table class="grid">
            <thead>
                <tr>
                    <th>Perangkat</th>
                    <th>Item uji</th>
                    <th>Status</th>
                    <th>Hasil</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perangkat as $alat)
                    @forelse ($alat->uji as $row)
                        <tr>
                            <td>{{ $alat->nama }}</td>
                            <td>{{ $row->item ?: '-' }}</td>
                            <td>{{ $row->statusLabel() }}</td>
                            <td>{{ $row->hasil ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td>{{ $alat->nama }}</td>
                            <td colspan="3">-</td>
                        </tr>
                    @endforelse
                @endforeach
            </tbody>
        </table>
    @elseif (is_array($ujiJson) && count($ujiJson))
        <table class="grid">
            <thead>
                <tr>
                    <th>Item uji</th>
                    <th>Status</th>
                    <th>Hasil</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ujiJson as $row)
                    <tr>
                        <td>{{ $row['item'] ?? '-' }}</td>
                        <td>{{ ($row['status'] ?? '') === 'memenuhi' ? 'Memenuhi' : (($row['status'] ?? '') === 'tidak_memenuhi' ? 'Tidak memenuhi' : '-') }}</td>
                        <td>{{ $row['hasil'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <p>{!! nl2br(e($suket->nilaiLhpp('narasi_uji') ?: $suket->hasil_analisis ?: '-')) !!}</p>

    <h4>7. Kesimpulan dan saran</h4>
    <div class="hasil">KESIMPULAN: {{ $hasil }} DIPAKAI / DIOPERASIKAN</div>
    <p>{!! nl2br(e($suket->hasil_analisis ?: '-')) !!}</p>
    <p><strong>Saran:</strong></p>
    <p>{!! nl2br(e($suket->rekomendasi ?: '-')) !!}</p>

    <div class="ttd">
        <p>Pengawas Ketenagakerjaan</p>
        <div class="space">
            @if (! $untukDs && $suket->ttd_pengawas_at)
                <p><em>Telah ditandatangani secara elektronik</em><br>
                    {{ optional($suket->ttd_pengawas_at)->format('d-m-Y H:i') }}</p>
            @elseif ($untukDs && $logoBsre)
                <table class="ttd-bsre-wrap">
                    <tr>
                        <td><img src="{{ $logoBsre }}" alt="Logo BSrE" class="ttd-bsre"></td>
                    </tr>
                </table>
            @else
                <br><br><br>
            @endif
        </div>
        <p><strong><u>{{ $suket->pengawas->name ?? '................................' }}</u></strong></p>
    </div>
    <div style="clear:both"></div>

    @if ($tampilFooter && $logoBsre)
        <div class="bsre-footer-wrap">
            @include('backend.blk.pelatihan._bsre-footer', ['sertifikat' => [
                'logo_bsre' => $logoBsre,
                'bsre_teks' => $bsreTeks,
            ]])
        </div>
    @endif
</body>
</html>
