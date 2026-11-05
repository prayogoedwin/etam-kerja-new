@extends('backend.template.backend')

@section('title', 'Report & Rekap HI PP')

@section('content')
<body class="box-layout container background-green">
    <div class="pcoded-main-container">
        <div class="pcoded-content">

            {{-- Page header --}}
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Report & Rekap — Peraturan Perusahaan</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#"><i class="feather icon-home"></i></a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Report & Rekap</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small text-muted mb-1">Tahun</label>
                    <select id="filter-tahun" class="form-control">
                        @foreach (range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small text-muted mb-1">Jenis Ajuan</label>
                    <select id="filter-jenis" class="form-control">
                        <option value="">Semua Jenis</option>
                        @foreach ($jenisAjuan as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2 d-flex align-items-end">
                    <button id="btn-refresh" class="btn btn-primary w-100">
                        <i class="feather icon-refresh-cw"></i> Refresh
                    </button>
                </div>
            </div>

            {{-- ===== KARTU RINGKASAN ===== --}}
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i class="feather icon-file-text text-primary" style="font-size:24px;"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Total Ajuan</p>
                                <h4 class="mb-0" id="stat-total">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <i class="feather icon-briefcase text-info" style="font-size:24px;"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Total Perusahaan</p>
                                <h4 class="mb-0" id="stat-perusahaan">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="feather icon-check-circle text-success" style="font-size:24px;"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Selesai</p>
                                <h4 class="mb-0" id="stat-selesai">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="feather icon-clock text-warning" style="font-size:24px;"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Dalam Proses</p>
                                <h4 class="mb-0" id="stat-proses">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== CHART ===== --}}
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Tren Ajuan per Bulan</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="chart-tren" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Distribusi Status</h6>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <canvas id="chart-status" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Ajuan per Jenis</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="chart-jenis" height="180"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Top 10 Perusahaan</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Perusahaan</th>
                                            <th width="20%" class="text-end">Jumlah Ajuan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-top-perusahaan">
                                        <tr><td colspan="3" class="text-center text-muted">Memuat...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== REKAP PER JENIS ===== --}}
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Rekap per Jenis Ajuan</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Jenis Ajuan</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Menunggu</th>
                                            <th class="text-center">Revisi</th>
                                            <th class="text-center">ACC</th>
                                            <th class="text-center">Selesai</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-jenis">
                                        <tr><td colspan="6" class="text-center text-muted">Memuat...</td></tr>
                                    </tbody>
                                </table>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
$(function () {
    let chartTren, chartStatus, chartJenis;

    function renderCharts(data) {
        // === Tren bulanan ===
        if (chartTren) chartTren.destroy();
        chartTren = new Chart(document.getElementById('chart-tren'), {
            type: 'line',
            data: {
                labels: data.tren_bulanan.map(r => r.bulan),
                datasets: [
                    {
                        label: 'Total',
                        data: data.tren_bulanan.map(r => r.total),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.1)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Selesai',
                        data: data.tren_bulanan.map(r => r.selesai),
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25,135,84,0.1)',
                        tension: 0.3,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // === Distribusi status (doughnut) ===
        if (chartStatus) chartStatus.destroy();
        chartStatus = new Chart(document.getElementById('chart-status'), {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Revisi', 'ACC', 'Selesai'],
                datasets: [{
                    data: [
                        data.ringkasan.menunggu,
                        data.ringkasan.revisi_admin + data.ringkasan.revisi_kasi,
                        data.ringkasan.acc_admin + data.ringkasan.acc_kasi,
                        data.ringkasan.selesai
                    ],
                    backgroundColor: ['#6c757d', '#ffc107', '#198754', '#0d6efd'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // === Per jenis ajuan (bar) ===
        if (chartJenis) chartJenis.destroy();
        chartJenis = new Chart(document.getElementById('chart-jenis'), {
            type: 'bar',
            data: {
                labels: data.per_jenis.map(r => r.jenis_ajuan),
                datasets: [{
                    label: 'Jumlah Ajuan',
                    data: data.per_jenis.map(r => r.total),
                    backgroundColor: ['#0d6efd', '#0dcaf0', '#ffc107', '#dc3545'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    function renderTables(data) {
        // Top perusahaan
        let html = '';
        if (data.top_perusahaan.length === 0) {
            html = '<tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>';
        } else {
            data.top_perusahaan.forEach((row, i) => {
                html += `<tr>
                    <td>${i + 1}</td>
                    <td>${row.perusahaan}</td>
                    <td class="text-end"><span class="badge bg-primary">${row.total_ajuan}</span></td>
                </tr>`;
            });
        }
        $('#tabel-top-perusahaan').html(html);

        // Rekap per jenis
        let rekap = '';
        data.rekap_per_jenis.forEach(row => {
            rekap += `<tr>
                <td>${row.jenis}</td>
                <td class="text-center">${row.total}</td>
                <td class="text-center"><span class="badge bg-secondary">${row.menunggu}</span></td>
                <td class="text-center"><span class="badge bg-warning">${row.revisi}</span></td>
                <td class="text-center"><span class="badge bg-success">${row.acc}</span></td>
                <td class="text-center"><span class="badge bg-primary">${row.selesai}</span></td>
            </tr>`;
        });
        $('#tabel-rekap-jenis').html(rekap || '<tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>');
    }

    function renderStats(data) {
        $('#stat-total').text(data.ringkasan.total);
        $('#stat-perusahaan').text(data.ringkasan.total_perusahaan);
        $('#stat-selesai').text(data.ringkasan.selesai);
        $('#stat-proses').text(data.ringkasan.total - data.ringkasan.selesai);
    }

    function loadData() {
        let params = {
            tahun: $('#filter-tahun').val(),
            jenis_ajuan: $('#filter-jenis').val()
        };

        $.ajax({
            url: "{{ route('hi.pp.admbidang.report.data') }}",
            data: params,
            type: 'GET',
            success: function (res) {
                if (res.status) {
                    renderStats(res);
                    renderCharts(res);
                    renderTables(res);
                }
            },
            error: function (xhr) {
                console.error('Report error:', xhr.responseText);
                alert('Gagal memuat data report. Cek console.');
            }
        });
    }

    $('#btn-refresh').on('click', loadData);
    $('#filter-tahun, #filter-jenis').on('change', loadData);
    loadData(); // initial
});
</script>
@endpush
