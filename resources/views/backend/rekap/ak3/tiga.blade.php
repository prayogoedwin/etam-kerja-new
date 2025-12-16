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
                                <h5 class="m-b-10">Rekap IPK III/3</h5>
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
                            <form method="GET" action="{{ route('rekap.ak33') }}" class="mb-4 no-print">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label for="triwulan" class="form-label">Triwulan</label>
                                        <select name="triwulan" id="triwulan" class="form-control">
                                            <option value="1" {{ $triwulan == 1 ? 'selected' : '' }}>Triwulan I (Jan - Mar)</option>
                                            <option value="2" {{ $triwulan == 2 ? 'selected' : '' }}>Triwulan II (Apr - Jun)</option>
                                            <option value="3" {{ $triwulan == 3 ? 'selected' : '' }}>Triwulan III (Jul - Sep)</option>
                                            <option value="4" {{ $triwulan == 4 ? 'selected' : '' }}>Triwulan IV (Okt - Des)</option>
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
                                        <a href="{{ route('rekap.ak33') }}" class="btn btn-secondary btn-sm">
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
                                    <h5>IPK III/3: PENCARI KERJA TERDAFTAR, DITEMPATKAN DAN DIHAPUSKAN MENURUT GOLONGAN JABATAN</h5>
                                    <h6>({{ strtoupper($namaTriwulan) }} {{ $tahun }})</h6>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableIPK33">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="2" style="vertical-align: middle; width: 80px;">Kode</th>
                                            <th rowspan="2" style="vertical-align: middle;">Uraian Jenis Golongan Pokok Jabatan</th>
                                            <th colspan="2">Sisa Akhir<br><small>Triwulan Lalu</small></th>
                                            <th colspan="2">Pendaftaran<br><small>Triwulan Ini</small></th>
                                            <th colspan="2">Penempatan<br><small>Triwulan Ini</small></th>
                                            <th colspan="2">Penghapusan<br><small>Triwulan Ini</small></th>
                                            <th colspan="2">Sisa Akhir<br><small>Triwulan Ini</small></th>
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
                                            <td colspan="2">(1)</td>
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
                                        </tr>

                                        <!-- Data per Jabatan -->
                                        @foreach($jabatans as $id => $data)
                                        @php
                                            $hasData = $data['sisa_triwulan_lalu']['L'] > 0 || 
                                                       $data['sisa_triwulan_lalu']['W'] > 0 || 
                                                       $data['pendaftaran']['L'] > 0 || 
                                                       $data['pendaftaran']['W'] > 0 ||
                                                       $data['penempatan']['L'] > 0 || 
                                                       $data['penempatan']['W'] > 0 ||
                                                       $data['penghapusan']['L'] > 0 || 
                                                       $data['penghapusan']['W'] > 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $data['kode'] }}</td>
                                            <td>{{ $data['nama'] }}</td>
                                            <td class="text-center">{{ $data['sisa_triwulan_lalu']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_triwulan_lalu']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['pendaftaran']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['pendaftaran']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penempatan']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penempatan']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penghapusan']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['penghapusan']['W'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_triwulan_ini']['L'] ?: '' }}</td>
                                            <td class="text-center">{{ $data['sisa_triwulan_ini']['W'] ?: '' }}</td>
                                        </tr>
                                        @endforeach

                                        <!-- Jumlah Total -->
                                        <tr class="text-center table-primary fw-bold">
                                            <td colspan="2"><strong>JUMLAH</strong></td>
                                            <td>{{ $jumlah['sisa_triwulan_lalu']['L'] }}</td>
                                            <td>{{ $jumlah['sisa_triwulan_lalu']['W'] }}</td>
                                            <td>{{ $jumlah['pendaftaran']['L'] }}</td>
                                            <td>{{ $jumlah['pendaftaran']['W'] }}</td>
                                            <td>{{ $jumlah['penempatan']['L'] }}</td>
                                            <td>{{ $jumlah['penempatan']['W'] }}</td>
                                            <td>{{ $jumlah['penghapusan']['L'] }}</td>
                                            <td>{{ $jumlah['penghapusan']['W'] }}</td>
                                            <td>{{ $jumlah['sisa_triwulan_ini']['L'] }}</td>
                                            <td>{{ $jumlah['sisa_triwulan_ini']['W'] }}</td>
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
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    var table = document.getElementById('tableIPK33');
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.table_to_sheet(table, { raw: true });
    
    // Set column widths
    ws['!cols'] = [
        {wch: 10},  // Kode
        {wch: 50},  // Uraian Jabatan
        {wch: 6},   // Sisa L
        {wch: 6},   // Sisa W
        {wch: 6},   // Pendaftaran L
        {wch: 6},   // Pendaftaran W
        {wch: 6},   // Penempatan L
        {wch: 6},   // Penempatan W
        {wch: 6},   // Penghapusan L
        {wch: 6},   // Penghapusan W
        {wch: 6},   // Sisa Ini L
        {wch: 6},   // Sisa Ini W
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "IPK III-3");
    
    var filename = "IPK_III_3_Triwulan_{{ $triwulan }}_{{ $tahun }}.xlsx";
    XLSX.writeFile(wb, filename);
}
</script>
@endpush