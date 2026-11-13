@php
    $perusahaan = $suket->profilPerusahaan;
    $untukDs = $untukDs ?? false;
    $tampilDraft = $untukDs ? false : (bool) ($tampilDraft ?? $suket->isDraft());
    $tampilFooter = $untukDs || ! $tampilDraft;
    $logoBsre = $logoBsre ?? null;
    $bsreTeks = $bsreTeks ?? '';
    $layak = (int) $suket->is_layak === 1;
    $judul = $layak ? 'SURAT KETERANGAN LAYAK K3' : 'SURAT KETERANGAN TIDAK LAYAK K3';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Suket {{ $suket->nomor_suket }}</title>
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
        <p>Jalan Kesuma Bangsa No. 4, Samarinda</p>
    </div>

    <h2>{{ $judul }}</h2>
    <p class="nomor">Nomor: {{ $suket->nomor_suket ?? '-' }}</p>

    <p>Yang bertanda tangan di bawah ini, Kepala Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur,
        menerangkan bahwa objek K3 berikut:</p>

    <p><strong>A. Identitas</strong></p>
    <table class="data">
        <tr>
            <td width="32%">1. Jenis objek</td>
            <td>: {{ $suket->jenis->nama ?? ($suket->kategori->nama ?? '-') }}</td>
        </tr>
        <tr>
            <td>2. Nama perusahaan</td>
            <td>: {{ $perusahaan->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>3. Alamat</td>
            <td>: {{ $perusahaan->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIB</td>
            <td>: {{ $perusahaan->nib ?? '-' }}</td>
        </tr>
    </table>

    <p><strong>B. Data teknis</strong></p>
    @php
        $perangkat = $suket->perangkat;
        $teknis = $suket->nilaiLhpp('data_teknis', []);
    @endphp
    @if ($perangkat->isNotEmpty())
        @foreach ($perangkat as $i => $row)
            <p>{{ $i + 1 }}. {{ $row->nama ?: ($row->jenis->nama ?? '-') }}
                — merk {{ $row->merk ?: '-' }},
                jumlah {{ $row->jumlah ?: '-' }},
                lokasi {{ $row->lokasi ?: '-' }}</p>
        @endforeach
    @elseif (is_array($teknis) && count($teknis))
        @foreach ($teknis as $i => $row)
            <p>{{ $i + 1 }}. {{ $row['nama'] ?? '-' }}
                — merk {{ $row['merk'] ?? '-' }},
                jumlah {{ $row['jumlah'] ?? '-' }},
                lokasi {{ $row['lokasi'] ?? '-' }}</p>
        @endforeach
    @else
        <table class="data">
            <tr>
                <td width="32%">Objek / alat</td>
                <td>: {{ $suket->nama_alat }}</td>
            </tr>
            <tr>
                <td>Kapasitas / jumlah</td>
                <td>: {{ $suket->kapasitas_alat ?: '-' }} / {{ $suket->jumlah_unit }} unit</td>
            </tr>
        </table>
    @endif

    <p><strong>C. Hasil pengujian</strong></p>
    <p>Berdasarkan LHPP PJK3 nomor <strong>{{ $suket->nilaiLhpp('nomor_lhpp_pjk3') ?: ($suket->nomor_suket ?? '-') }}</strong>
        tanggal {{ optional($suket->tanggal_pemeriksaan)->translatedFormat('d F Y') ?? '-' }},
        PJK3 {{ $suket->profilPjk3->name ?? '-' }}.</p>

    <p><strong>D. Hasil evaluasi</strong></p>
    <div class="hasil">{{ $layak ? 'MEMENUHI' : 'TIDAK MEMENUHI' }}</div>

    <p>{{ $layak
        ? 'dengan demikian objek tersebut dapat dioperasikan sesuai ketentuan peraturan perundang-undangan di bidang keselamatan dan kesehatan kerja.'
        : 'dengan demikian objek tersebut belum dapat dioperasikan sampai dilakukan perbaikan dan pemeriksaan ulang sesuai ketentuan peraturan perundang-undangan.'
    }}</p>

    <div class="ttd">
        <p>Kepala Dinas</p>
        <div class="space">
            @if (! $untukDs && $suket->ttd_kadis_at)
                <p><em>Telah ditandatangani secara elektronik</em><br>
                    {{ optional($suket->ttd_kadis_at)->format('d-m-Y H:i') }}</p>
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
        <p><strong><u>Kepala Dinas Tenaga Kerja dan Transmigrasi</u></strong></p>
        <p>Pengawas: {{ $suket->pengawas->name ?? '-' }}
            @if ($suket->ttd_pengawas_at)
                (TTE {{ optional($suket->ttd_pengawas_at)->format('d-m-Y') }})
            @endif
        </p>
    </div>
    <div style="clear:both"></div>
    <p>Tembusan: Direktur Jenderal Pembinaan Pengawasan Ketenagakerjaan.</p>

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
