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
                                    <h5 class="m-b-10">Rekap IPK III/1</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->

                <!-- [ Filter Bulan Tahun ] start -->
                <div class="row mb-4">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" action="{{ route('rekap.ak31') }}" class="row align-items-end">
                                    <div class="col-md-3">
                                        <label for="bulan" class="form-label">Bulan</label>
                                        <select name="bulan" id="bulan" class="form-control">
                                            @foreach([
                                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                                10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ] as $num => $nama)
                                                <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tahun" class="form-label">Tahun</label>
                                        <select name="tahun" id="tahun" class="form-control">
                                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="feather icon-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('rekap.ak31') }}" class="btn btn-secondary btn-sm">
                                            <i class="feather icon-refresh-cw"></i> Reset
                                        </a>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
                                            <i class="feather icon-printer"></i> Cetak
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" id="exportExcel">
                                            <i class="feather icon-download"></i> Export Excel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Filter Bulan Tahun ] end -->

                <!-- [ Main Content ] start -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-12">
                                        <h5>IPK III/1: IKHTISAR STATISTIK IPK ({{ strtoupper($namaBulan) }} {{ $tahun }})</h5>
                                    </div>
                                </div>
                                <div class="table-responsive" id="tableRekap">
                                    <!-- I. PENCARI KERJA -->
                                    <table class="table table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="3" style="vertical-align: middle;">I. PENCARI KERJA</th>
                                                <th colspan="10">KELOMPOK UMUR</th>
                                                <th rowspan="2" colspan="3">Jumlah</th>
                                            </tr>
                                            <tr>
                                                @foreach($kelompokUmur as $ku)
                                                    <th colspan="2">{{ $ku }}</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($kelompokUmur as $ku)
                                                    <th>L</th>
                                                    <th>P</th>
                                                @endforeach
                                                <th>L</th>
                                                <th>P</th>
                                                <th>L+P</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- 1. Pencari kerja yang belum ditempatkan pada bulan yang lalu -->
                                            <tr>
                                                <td>1. Pencari kerja yang belum ditempatkan pada bulan yang lalu</td>
                                                @foreach($kelompokUmur as $ku)
                                                    <td class="text-center">{{ $pencari['1'][$ku]['L'] ?? 0 }}</td>
                                                    <td class="text-center">{{ $pencari['1'][$ku]['P'] ?? 0 }}</td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">{{ $pencari['1']['jumlah']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['1']['jumlah']['P'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['1']['jumlah']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 2. Pencari kerja yang terdaftar pada bulan ini -->
                                            <tr>
                                                <td>2. Pencari kerja yang terdaftar pada bulan ini</td>
                                                @foreach($kelompokUmur as $ku)
                                                    <td class="text-center">{{ $pencari['2'][$ku]['L'] ?? 0 }}</td>
                                                    <td class="text-center">{{ $pencari['2'][$ku]['P'] ?? 0 }}</td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">{{ $pencari['2']['jumlah']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['2']['jumlah']['P'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['2']['jumlah']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- A. JUMLAH (1+2) -->
                                            <tr class="table-secondary">
                                                <td><strong>A. JUMLAH (1+2)</strong></td>
                                                @foreach($kelompokUmur as $ku)
                                                    <td class="text-center font-weight-bold">{{ $pencari['A'][$ku]['L'] ?? 0 }}</td>
                                                    <td class="text-center font-weight-bold">{{ $pencari['A'][$ku]['P'] ?? 0 }}</td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">{{ $pencari['A']['jumlah']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['A']['jumlah']['P'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['A']['jumlah']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 3. Pencari kerja yang ditempatkan pada bulan ini -->
                                            <tr>
                                                <td>3. Pencari kerja yang ditempatkan pada bulan ini</td>
                                                @foreach($kelompokUmur as $ku)
                                                    <td class="text-center">{{ $pencari['3'][$ku]['L'] ?? 0 }}</td>
                                                    <td class="text-center">{{ $pencari['3'][$ku]['P'] ?? 0 }}</td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">{{ $pencari['3']['jumlah']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['3']['jumlah']['P'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['3']['jumlah']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 4. Pencari kerja yang dihapuskan pada bulan ini -->
                                            <tr>
                                                <td>4. Pencari kerja yang dihapuskan pada bulan ini</td>
                                                @foreach($kelompokUmur as $ku)
                                                    <td class="text-center">{{ $pencari['4'][$ku]['L'] ?? 0 }}</td>
                                                    <td class="text-center">{{ $pencari['4'][$ku]['P'] ?? 0 }}</td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">{{ $pencari['4']['jumlah']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['4']['jumlah']['P'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['4']['jumlah']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- B. JUMLAH (3+4) -->
                                            <tr class="table-secondary">
                                                <td><strong>B. JUMLAH (3+4)</strong></td>
                                                @foreach($kelompokUmur as $ku)
                                                    <td class="text-center font-weight-bold">{{ $pencari['B'][$ku]['L'] ?? 0 }}</td>
                                                    <td class="text-center font-weight-bold">{{ $pencari['B'][$ku]['P'] ?? 0 }}</td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">{{ $pencari['B']['jumlah']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['B']['jumlah']['P'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['B']['jumlah']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 5. Pencari kerja yang belum ditempatkan pada akhir bulan ini (A-B) -->
                                            <tr class="table-primary">
                                                <td><strong>5. Pencari kerja yang belum ditempatkan pada akhir bulan ini (A-B)</strong></td>
                                                @foreach($kelompokUmur as $ku)
                                                    <td class="text-center font-weight-bold">{{ $pencari['5'][$ku]['L'] ?? 0 }}</td>
                                                    <td class="text-center font-weight-bold">{{ $pencari['5'][$ku]['P'] ?? 0 }}</td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">{{ $pencari['5']['jumlah']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['5']['jumlah']['P'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $pencari['5']['jumlah']['total'] ?? 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- II. LOWONGAN -->
                                    <table class="table table-bordered mt-4">
                                        <thead class="text-center">
                                            <tr>
                                                <th>II. LOWONGAN</th>
                                                <th>L</th>
                                                <th>W</th>
                                                <th>L+W</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center table-light">
                                                <td><strong>Keterangan</strong></td>
                                                <td><strong>2</strong></td>
                                                <td><strong>3</strong></td>
                                                <td><strong>4</strong></td>
                                            </tr>

                                            <!-- 1. Lowongan yang belum dipenuhi pada akhir bulan lalu -->
                                            <tr>
                                                <td>1. Lowongan yang belum dipenuhi pada akhir bulan lalu</td>
                                                <td class="text-center">{{ $lowongan['1']['L'] ?? 0 }}</td>
                                                <td class="text-center">{{ $lowongan['1']['W'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['1']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 2. Lowongan yang terdaftar bulan ini -->
                                            <tr>
                                                <td>2. Lowongan yang terdaftar bulan ini</td>
                                                <td class="text-center">{{ $lowongan['2']['L'] ?? 0 }}</td>
                                                <td class="text-center">{{ $lowongan['2']['W'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['2']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- C. JUMLAH (1+2) -->
                                            <tr class="table-secondary">
                                                <td><strong>C. JUMLAH (1+2)</strong></td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['C']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['C']['W'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['C']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 3. Lowongan yang dipenuhi bulan ini -->
                                            <tr>
                                                <td>3. Lowongan yang dipenuhi bulan ini</td>
                                                <td class="text-center">{{ $lowongan['3']['L'] ?? 0 }}</td>
                                                <td class="text-center">{{ $lowongan['3']['W'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['3']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 4. Lowongan yang dihapuskan bulan ini -->
                                            <tr>
                                                <td>4. Lowongan yang dihapuskan bulan ini</td>
                                                <td class="text-center">{{ $lowongan['4']['L'] ?? 0 }}</td>
                                                <td class="text-center">{{ $lowongan['4']['W'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['4']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- D. JUMLAH (3+4) -->
                                            <tr class="table-secondary">
                                                <td><strong>D. JUMLAH (3+4)</strong></td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['D']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['D']['W'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['D']['total'] ?? 0 }}</td>
                                            </tr>

                                            <!-- 5. Lowongan yang belum dipenuhi akhir bulan ini (C-D) -->
                                            <tr class="table-primary">
                                                <td><strong>5. Lowongan yang belum dipenuhi akhir bulan ini (C-D)</strong></td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['5']['L'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['5']['W'] ?? 0 }}</td>
                                                <td class="text-center font-weight-bold">{{ $lowongan['5']['total'] ?? 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Info Periode -->
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="feather icon-info"></i>
                                        Data diambil untuk periode: <strong>{{ $namaBulan }} {{ $tahun }}</strong>
                                        | Dicetak pada: {{ date('d F Y H:i') }}
                                    </small>
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
        .page-header, .card:first-child, .btn, .form-control, select, .text-muted {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .table {
            font-size: 10px;
        }
        .pcoded-main-container {
            margin-left: 0 !important;
        }
    }
    
    .table th, .table td {
        vertical-align: middle !important;
    }
    
    .font-weight-bold {
        font-weight: bold !important;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportExcel').addEventListener('click', function() {
        // Get the table element
        var tables = document.querySelectorAll('#tableRekap table');
        var wb = XLSX.utils.book_new();
        
        // First table - Pencari Kerja
        var ws1 = XLSX.utils.table_to_sheet(tables[0]);
        XLSX.utils.book_append_sheet(wb, ws1, "Pencari Kerja");
        
        // Second table - Lowongan
        var ws2 = XLSX.utils.table_to_sheet(tables[1]);
        XLSX.utils.book_append_sheet(wb, ws2, "Lowongan");
        
        // Generate filename with month and year
        var filename = "IPK_3_1_{{ $namaBulan }}_{{ $tahun }}.xlsx";
        
        // Download the file
        XLSX.writeFile(wb, filename);
    });
</script>
@endpush