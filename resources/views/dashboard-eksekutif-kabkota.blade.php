<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Eksekutif - {{ $nama_kabkota }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: #fff;
            overflow-y: auto;
        }

        .dashboard {
            padding: 20px;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 0 20px;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(90deg, #60a5fa, #4ade80);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        .header .timestamp {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Grid Layout - 4 columns */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            align-items: start;
        }

        /* Column wrapper for stacked cards */
        .card-column {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
        }

        .card-header {
            margin-bottom: 12px;
        }

        .card-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            font-weight: 600;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Big Numbers */
        .big-number {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-bottom: 12px;
        }

        /* Card Pencari */
        .card-pencari {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.05));
            border-color: rgba(59, 130, 246, 0.3);
        }

        .card-pencari .big-number {
            color: #60a5fa;
        }

        /* Card Diterima */
        .card-diterima {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(22, 163, 74, 0.05));
            border-color: rgba(34, 197, 94, 0.3);
        }

        .card-diterima .big-number {
            color: #4ade80;
        }

        /* Card Penyedia */
        .card-penyedia {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(2, 132, 199, 0.05));
            border-color: rgba(14, 165, 233, 0.3);
        }

        .card-penyedia .big-number {
            color: #38bdf8;
        }

        /* Card Lowongan */
        .card-lowongan {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(245, 158, 11, 0.05));
            border-color: rgba(251, 191, 36, 0.3);
        }

        .card-lowongan .big-number {
            color: #fbbf24;
        }

        /* Card Sektor */
        .card-sektor {
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(219, 39, 119, 0.05));
            border-color: rgba(236, 72, 153, 0.3);
        }

        /* Gender Split */
        .gender-split {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gender-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .gender-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.75rem;
        }

        .male-icon {
            background: rgba(59, 130, 246, 0.3);
            color: #60a5fa;
        }

        .female-icon {
            background: rgba(236, 72, 153, 0.3);
            color: #f472b6;
        }

        .gender-number {
            font-size: 1rem;
            font-weight: 700;
        }

        .gender-label {
            font-size: 0.55rem;
            color: #94a3b8;
        }

        /* Section Title */
        .section-title {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 600;
        }

        /* Top List */
        .top-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .top-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            font-size: 0.7rem;
        }

        .top-item .rank {
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #60a5fa, #4ade80);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.55rem;
            font-weight: 700;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .top-item .name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .top-item .count {
            font-weight: 700;
            color: #60a5fa;
            margin-left: 6px;
        }

        /* Kecamatan List */
        .kecamatan-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .kecamatan-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            background: rgba(34, 197, 94, 0.1);
            border-radius: 6px;
        }

        .kecamatan-item .kecamatan-rank {
            font-size: 0.6rem;
            color: #94a3b8;
            margin-right: 6px;
        }

        .kecamatan-item .kecamatan-name {
            flex: 1;
            font-size: 0.7rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kecamatan-item .kecamatan-count {
            font-size: 0.9rem;
            font-weight: 700;
            color: #4ade80;
        }

        /* Kebutuhan */
        .kebutuhan-row {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .kebutuhan-item {
            flex: 1;
            text-align: center;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
        }

        .kebutuhan-item .k-value {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .kebutuhan-item .k-label {
            font-size: 0.55rem;
            color: #94a3b8;
        }

        .kebutuhan-item.pria .k-value {
            color: #60a5fa;
        }

        .kebutuhan-item.wanita .k-value {
            color: #f472b6;
        }

        /* Lamaran Stats */
        .lamaran-section {
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 12px;
        }

        .lamaran-big {
            text-align: center;
            margin-bottom: 10px;
        }

        .lamaran-big .l-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #fbbf24;
        }

        .lamaran-big .l-label {
            font-size: 0.6rem;
            color: #94a3b8;
        }

        .lamaran-detail {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .lamaran-badge {
            padding: 5px 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .lamaran-badge .badge-count {
            font-weight: 700;
            color: #fbbf24;
        }

        /* Stat Row */
        .stat-row {
            display: flex;
            gap: 12px;
            margin-bottom: 8px;
        }

        .stat-box {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .stat-box .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #4ade80;
        }

        .stat-box .stat-label {
            font-size: 0.6rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        .stat-box.highlight .stat-value {
            color: #fbbf24;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>📊 Dashboard Eksekutif Ketenagakerjaan</h1>
                <div class="subtitle">{{ $nama_kabkota }}</div>
            </div>
            <div class="timestamp">
                <span>Diperbarui:</span>
                <strong>{{ $generated_at }}</strong> | 
                <span><a href="{{ route('logout') }}" class="dropdown-item" style="color:#fff"><i class="feather icon-lock"></i> Logout</a></span>
            </div>
        </div>

        <!-- 4 Cards Grid -->
        <div class="cards-grid">
            <!-- Column 1: Pencari Kerja -->
            <div class="card-column">
                <!-- Card: Pencari Kerja Aktif -->
                <div class="card card-pencari">
                    <div class="card-header">
                        <div class="card-title">👤 Pencari Kerja Aktif</div>
                    </div>
                    <div class="card-body">
                        <div class="big-number">{{ number_format($pencari['total']) }}</div>
                        <div class="subtitle">Belum diterima kerja</div>
                        
                        <div class="gender-split">
                            <div class="gender-item">
                                <div class="gender-icon male-icon">♂</div>
                                <div>
                                    <div class="gender-number">{{ number_format($pencari['laki_laki']) }}</div>
                                    <div class="gender-label">Laki-laki</div>
                                </div>
                            </div>
                            <div class="gender-item">
                                <div class="gender-icon female-icon">♀</div>
                                <div>
                                    <div class="gender-number">{{ number_format($pencari['perempuan']) }}</div>
                                    <div class="gender-label">Perempuan</div>
                                </div>
                            </div>
                        </div>

                        <div class="section-title">📍 Top 5 Kecamatan</div>
                        <div class="kecamatan-list">
                            @forelse($pencari['top_kecamatan'] as $index => $kecamatan)
                            <div class="kecamatan-item">
                                <span class="kecamatan-rank">#{{ $index + 1 }}</span>
                                <span class="kecamatan-name">{{ $kecamatan->nama }}</span>
                                <span class="kecamatan-count">{{ number_format($kecamatan->total) }}</span>
                            </div>
                            @empty
                            <div class="kecamatan-item">
                                <span class="kecamatan-name">Tidak ada data</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Card: Pencari Diterima -->
                <div class="card card-diterima">
                    <div class="card-header">
                        <div class="card-title">✅ Pencari Sudah Bekerja</div>
                    </div>
                    <div class="card-body">
                        <div class="stat-row">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($pencari_diterima['total_diterima']) }}</div>
                                <div class="stat-label">Total Diterima Kerja</div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-box highlight">
                                <div class="stat-value">{{ number_format($pencari_diterima['total_ditempatkan']) }}</div>
                                <div class="stat-label">Ditempatkan via Aplikasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 2: Perusahaan -->
            <div class="card-column">
                <div class="card card-penyedia">
                    <div class="card-header">
                        <div class="card-title">🏢 Perusahaan Terdaftar</div>
                    </div>
                    <div class="card-body">
                        <div class="big-number">{{ number_format($penyedia['total']) }}</div>
                        <div class="subtitle">Total perusahaan</div>

                        <div class="section-title">📍 Top 10 Kecamatan</div>
                        <div class="kecamatan-list">
                            @forelse($penyedia['top_kecamatan'] as $index => $kecamatan)
                            <div class="kecamatan-item">
                                <span class="kecamatan-rank">#{{ $index + 1 }}</span>
                                <span class="kecamatan-name">{{ $kecamatan->nama }}</span>
                                <span class="kecamatan-count">{{ number_format($kecamatan->total) }}</span>
                            </div>
                            @empty
                            <div class="kecamatan-item">
                                <span class="kecamatan-name">Tidak ada data</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Card: Top 5 Sektor Perusahaan -->
                <div class="card card-penyedia">
                    <div class="card-header">
                        <div class="card-title">🏭 Top 5 Sektor Perusahaan</div>
                    </div>
                    <div class="card-body">
                        <ul class="top-list">
                            @forelse($sektor_perusahaan as $index => $item)
                            <li class="top-item">
                                <span class="rank">{{ $index + 1 }}</span>
                                <span class="name">{{ $item->nama }}</span>
                                <span class="count">{{ number_format($item->total) }}</span>
                            </li>
                            @empty
                            <li class="top-item">
                                <span class="name">Tidak ada data</span>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Column 3: Pendidikan & Jurusan -->
            <div class="card-column">
                <!-- Card: Top 5 Pendidikan -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">🎓 Top 5 Pendidikan</div>
                    </div>
                    <div class="card-body">
                        <ul class="top-list">
                            @forelse($pendidikan as $index => $item)
                            <li class="top-item">
                                <span class="rank">{{ $index + 1 }}</span>
                                <span class="name">{{ $item->nama }}</span>
                                <span class="count">{{ number_format($item->total) }}</span>
                            </li>
                            @empty
                            <li class="top-item">
                                <span class="name">Tidak ada data</span>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Card: Top 5 Jurusan -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">📚 Top 5 Jurusan</div>
                    </div>
                    <div class="card-body">
                        <ul class="top-list">
                            @forelse($jurusan as $index => $item)
                            <li class="top-item">
                                <span class="rank">{{ $index + 1 }}</span>
                                <span class="name">{{ $item->nama }}</span>
                                <span class="count">{{ number_format($item->total) }}</span>
                            </li>
                            @empty
                            <li class="top-item">
                                <span class="name">Tidak ada data</span>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Column 4: Lowongan + Sektor Lowongan -->
            <div class="card-column">
                <div class="card card-lowongan">
                    <div class="card-header">
                        <div class="card-title">💼 Lowongan Aktif</div>
                    </div>
                    <div class="card-body">
                        <div class="big-number">{{ number_format($lowongan['total']) }}</div>
                        <div class="subtitle">Lowongan tersedia</div>

                        <div class="kebutuhan-row">
                            <div class="kebutuhan-item pria">
                                <div class="k-value">{{ number_format($lowongan['kebutuhan_pria']) }}</div>
                                <div class="k-label">Kebutuhan Pria</div>
                            </div>
                            <div class="kebutuhan-item wanita">
                                <div class="k-value">{{ number_format($lowongan['kebutuhan_wanita']) }}</div>
                                <div class="k-label">Kebutuhan Wanita</div>
                            </div>
                        </div>

                        <div class="lamaran-section" style="margin-top: 0; border-top: none; padding-top: 0;">
                            <div class="lamaran-big">
                                <div class="l-value">{{ number_format($lowongan['lamaran_proses']) }}</div>
                                <div class="l-label">Lamaran Dalam Proses</div>
                            </div>
                            @if($lowongan['lamaran_detail']->count() > 0)
                            <div class="lamaran-detail">
                                @foreach($lowongan['lamaran_detail'] as $detail)
                                <div class="lamaran-badge">
                                    <span>{{ $detail->status }}</span>
                                    <span class="badge-count">{{ number_format($detail->total) }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card: Top 5 Sektor Lowongan -->
                <div class="card card-sektor">
                    <div class="card-header">
                        <div class="card-title">🏭 Top 5 Sektor Lowongan</div>
                    </div>
                    <div class="card-body">
                        <ul class="top-list">
                            @forelse($sektor as $index => $item)
                            <li class="top-item">
                                <span class="rank">{{ $index + 1 }}</span>
                                <span class="name">{{ $item->nama }}</span>
                                <span class="count">{{ number_format($item->total) }}</span>
                            </li>
                            @empty
                            <li class="top-item">
                                <span class="name">Tidak ada data</span>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh setiap 5 menit
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>