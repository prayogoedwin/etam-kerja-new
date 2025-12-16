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
                                <h5 class="m-b-10">Rekap IPK III/4</h5>
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
                            <form method="GET" action="{{ route('rekap.ak34') }}" class="mb-4 no-print">
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
                                        <a href="{{ route('rekap.ak34') }}" class="btn btn-secondary btn-sm">
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
                                    <h5>IPK III/4: LOWONGAN KERJA YANG TERDAFTAR, DIPENUHI DAN DIHAPUSKAN</h5>
                                    <h6>({{ strtoupper($namaBulan) }} {{ $tahun }})</h6>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableIPK34">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="2" style="vertical-align: middle;">Kode</th>
                                            <th rowspan="2" style="vertical-align: middle;">Jenis Pendidikan</th>
                                            <th colspan="2">Sisa Akhir Bulan<br><small>(Lalu)</small></th>
                                            <th colspan="2">Pendaftaran<br><small>Bulan Ini</small></th>
                                            <th colspan="2">Dipenuhi<br><small>Bulan Ini</small></th>
                                            <th colspan="2">Penghapusan<br><small>Bulan Ini</small></th>
                                            <th colspan="2">Sisa Akhir<br><small>Bulan Ini</small></th>
                                        </tr>
                                        <tr>
                                            <th>L</th>
                                            <th>W</th>
                                            <th>L</th>
                                            <th>W</th>
                                            <th>L</th>
                                            <th>W</th>
                                            <th>L</th>
                                            <th>W</th>
                                            <th>L</th>
                                            <th>W</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Nomor Kolom -->
                                        <tr class="text-center table-secondary">
                                            <td colspan="2">1</td>
                                            <td>2</td>
                                            <td>3</td>
                                            <td>4</td>
                                            <td>5</td>
                                            <td>6</td>
                                            <td>7</td>
                                            <td>8</td>
                                            <td>9</td>
                                            <td>10</td>
                                            <td>11</td>
                                        </tr>

                                        <!-- Data per Pendidikan -->
                                        @foreach($pendidikans as $id => $data)
                                        <tr>
                                            <td class="text-center">{{ $data['kode'] }}</td>
                                            <td>{{ $data['nama'] }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_lalu']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_lalu']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['pendaftaran']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['pendaftaran']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penempatan']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penempatan']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penghapusan']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penghapusan']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_ini']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_bulan_ini']['W'] ?: '' }}</td>
                                        </tr>
                                        @endforeach

                                        <!-- Jumlah Total -->
                                        <tr class="text-center table-primary fw-bold">
                                            <td colspan="2"><strong>JUMLAH</strong></td>
                                            <td>{{ $jumlah['sisa_bulan_lalu']['L'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_lalu']['W'] }}</td>
                                            <td>{{ $jumlah['pendaftaran']['L'] }}</td>
                                            <td>{{ $jumlah['pendaftaran']['W'] }}</td>
                                            <td>{{ $jumlah['penempatan']['L'] }}</td>
                                            <td>{{ $jumlah['penempatan']['W'] }}</td>
                                            <td>{{ $jumlah['penghapusan']['L'] }}</td>
                                            <td>{{ $jumlah['penghapusan']['W'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_ini']['L'] }}</td>
                                            <td>{{ $jumlah['sisa_bulan_ini']['W'] }}</td>
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
            font-size: 10pt;
        }
        th, td {
            padding: 4px !important;
        }
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    var table = document.getElementById('tableIPK34');
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.table_to_sheet(table, { raw: true });
    
    // Set column widths
    ws['!cols'] = [
        {wch: 8},   // Kode
        {wch: 30},  // Jenis Pendidikan
        {wch: 6},   // Sisa L
        {wch: 6},   // Sisa W
        {wch: 6},   // Pendaftaran L
        {wch: 6},   // Pendaftaran W
        {wch: 6},   // Dipenuhi L
        {wch: 6},   // Dipenuhi W
        {wch: 6},   // Penghapusan L
        {wch: 6},   // Penghapusan W
        {wch: 6},   // Sisa Ini L
        {wch: 6},   // Sisa Ini W
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "IPK III-4");
    
    var filename = "IPK_III_4_{{ $namaBulan }}_{{ $tahun }}.xlsx";
    XLSX.writeFile(wb, filename);
}
</script>
@endpush