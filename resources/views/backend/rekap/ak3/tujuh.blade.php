@extends('backend.template.backend')

@section('content')

<body class="box-layout container background-green">
    <div class="pcoded-main-container">
        <div class="pcoded-content">

            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Rekap IPK III/7</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Filter Form -->
                            <form method="GET" action="{{ route('rekap.ak37') }}" class="mb-4 no-print">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label for="bulan" class="form-label">Bulan</label>
                                        <select name="bulan" id="bulan" class="form-control">
                                            @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $key => $nama)
                                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tahun" class="form-label">Tahun</label>
                                        <select name="tahun" id="tahun" class="form-control">
                                            @for($y = date('Y'); $y >= 2020; $y--)
                                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="feather icon-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('rekap.ak37') }}" class="btn btn-secondary btn-sm">
                                            <i class="feather icon-refresh-cw"></i> Reset
                                        </a>
                                        <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
                                            <i class="feather icon-printer"></i> Print
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" onclick="exportToExcel()">
                                            <i class="feather icon-download"></i> Excel
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Report Title -->
                            <div class="row align-items-center m-l-0 mb-3">
                                <div class="col-sm-12 text-center">
                                    <h5>IPK III/7: PENCARI KERJA YANG TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN</h5>
                                    <h6>Menurut Lama Lulusan dan Tingkat Pendidikan</h6>
                                    <h6>({{ strtoupper($namaBulan) }} {{ $tahun }})</h6>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableIPK37">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="3" style="vertical-align: middle; width: 60px;">Kode<br>Lulusan</th>
                                            <th rowspan="3" style="vertical-align: middle;">Tingkat Pendidikan</th>
                                            <th colspan="3">Sisa Akhir<br>Bulan Lalu</th>
                                            <th colspan="3">Terdaftar<br>Bulan Ini</th>
                                            <th colspan="3">Penempatan<br>Bulan Ini</th>
                                            <th colspan="3">Sisa Akhir<br>Bulan Ini</th>
                                        </tr>
                                        <tr>
                                            <th>0-2 th</th>
                                            <th>3-5 th</th>
                                            <th>6+ th</th>
                                            <th>0-2 th</th>
                                            <th>3-5 th</th>
                                            <th>6+ th</th>
                                            <th>0-2 th</th>
                                            <th>3-5 th</th>
                                            <th>6+ th</th>
                                            <th>0-2 th</th>
                                            <th>3-5 th</th>
                                            <th>6+ th</th>
                                        </tr>
                                        <tr>
                                            <th>(3)</th>
                                            <th>(4)</th>
                                            <th>(5)</th>
                                            <th>(6)</th>
                                            <th>(7)</th>
                                            <th>(8)</th>
                                            <th>(9)</th>
                                            <th>(10)</th>
                                            <th>(11)</th>
                                            <th>(12)</th>
                                            <th>(13)</th>
                                            <th>(14)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Nomor Kolom -->
                                        <tr class="text-center table-secondary">
                                            <td>(1)</td>
                                            <td>(2)</td>
                                            <td>(3)</td>
                                            <td>(4)</td>
                                            <td>(5)</td>
                                            <td>(6)</td>
                                            <td>(7)</td>
                                            <td>(8)</td>
                                            <td>(9)</td>
                                            <td>(10)</td>
                                            <td>(11)</td>
                                            <td>(12)</td>
                                            <td>(13)</td>
                                            <td>(14)</td>
                                        </tr>

                                        <!-- Data per Pendidikan -->
                                        @foreach($pendidikans as $id => $data)
                                        <tr>
                                            <td class="text-center">{{ $data['kode'] }}</td>
                                            <td>{{ $data['nama'] }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_lalu']['0-2'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_lalu']['3-5'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_lalu']['6+'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['terdaftar']['0-2'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['terdaftar']['3-5'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['terdaftar']['6+'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penempatan']['0-2'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penempatan']['3-5'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penempatan']['6+'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_ini']['0-2'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_ini']['3-5'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_ini']['6+'] ?: '' }}</td>
                                        </tr>
                                        @endforeach

                                        <!-- Jumlah Total -->
                                        <tr class="text-center table-warning fw-bold">
                                            <td colspan="2"><strong>JUMLAH</strong></td>
                                            <td>{{ $jumlah['sisa_bulan_lalu']['0-2'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_lalu']['3-5'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_lalu']['6+'] }}</td>
                                            <td>{{ $jumlah['terdaftar']['0-2'] }}</td>
                                            <td>{{ $jumlah['terdaftar']['3-5'] }}</td>
                                            <td>{{ $jumlah['terdaftar']['6+'] }}</td>
                                            <td>{{ $jumlah['penempatan']['0-2'] }}</td>
                                            <td>{{ $jumlah['penempatan']['3-5'] }}</td>
                                            <td>{{ $jumlah['penempatan']['6+'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_ini']['0-2'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_ini']['3-5'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_ini']['6+'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->

        </div>
    </div>
</body>

@endsection

@push('css')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .pcoded-header,
        .pcoded-sidebar,
        .page-header {
            display: none !important;
        }
        .pcoded-main-container {
            margin-left: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        table {
            font-size: 9pt;
        }
        th, td {
            padding: 3px !important;
        }
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .table-warning {
        background-color: #FFC !important;
    }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    var table = document.getElementById('tableIPK37');
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.table_to_sheet(table, { raw: true });
    
    // Set column widths
    ws['!cols'] = [
        {wch: 8},   // Kode
        {wch: 35},  // Tingkat Pendidikan
        {wch: 6},   // Sisa 0-2
        {wch: 6},   // Sisa 3-5
        {wch: 6},   // Sisa 6+
        {wch: 6},   // Terdaftar 0-2
        {wch: 6},   // Terdaftar 3-5
        {wch: 6},   // Terdaftar 6+
        {wch: 6},   // Penempatan 0-2
        {wch: 6},   // Penempatan 3-5
        {wch: 6},   // Penempatan 6+
        {wch: 6},   // Sisa Ini 0-2
        {wch: 6},   // Sisa Ini 3-5
        {wch: 6},   // Sisa Ini 6+
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "IPK III-7");
    
    var filename = "IPK_III_7_{{ $namaBulan }}_{{ $tahun }}.xlsx";
    XLSX.writeFile(wb, filename);
}
</script>
@endpush