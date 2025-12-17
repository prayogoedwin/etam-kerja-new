<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flowchart Job Fair - ETAMKERJA</title>
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .header .subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            text-align: right;
        }
        
        .flowchart-container {
            padding: 40px;
            overflow-x: auto;
        }

        .flowchart {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            min-width: 900px;
        }

        /* Shapes */
        .terminal {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
        }

        .process {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 0.85rem;
            text-align: center;
            min-width: 200px;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .process-auth {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
            box-shadow: 0 4px 15px rgba(155, 89, 182, 0.3);
        }

        .process-success {
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .process-reject {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .process-admin {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            box-shadow: 0 4px 15px rgba(52, 73, 94, 0.3);
        }

        .process-jobfair {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .decision {
            width: 180px;
            height: 90px;
            background: linear-gradient(135deg, #f39c12 0%, #d68910 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 500;
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
            padding: 10px;
        }

        .data {
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            color: white;
            padding: 15px 25px;
            clip-path: polygon(15% 0%, 100% 0%, 85% 100%, 0% 100%);
            font-size: 0.85rem;
            text-align: center;
            min-width: 200px;
            box-shadow: 0 4px 15px rgba(26, 188, 156, 0.3);
        }

        .document {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 8px 8px 0 0;
            font-size: 0.85rem;
            text-align: center;
            min-width: 180px;
            position: relative;
            box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);
        }

        .document::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            border-radius: 0 0 50% 50% / 0 0 100% 100%;
        }

        /* Database shape */
        .database {
            width: 160px;
            height: 60px;
            background: linear-gradient(180deg, #3498db 0%, #2980b9 100%);
            border-radius: 10px 10px 50% 50% / 10px 10px 20px 20px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            text-align: center;
            padding-top: 5px;
        }

        .database::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 15px;
            background: linear-gradient(180deg, #5dade2 0%, #3498db 100%);
            border-radius: 50%;
        }

        /* Arrows */
        .arrow-down {
            width: 3px;
            height: 30px;
            background: #2c3e50;
            position: relative;
        }

        .arrow-down::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 10px solid #2c3e50;
        }

        .arrow-short {
            height: 20px;
        }

        /* Branch */
        .branch-row {
            display: flex;
            gap: 80px;
            align-items: flex-start;
            position: relative;
        }

        .branch-row::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 100px);
            height: 3px;
            background: #2c3e50;
        }

        .branch-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .branch-connector {
            width: 3px;
            height: 25px;
            background: #2c3e50;
        }

        .branch-label {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 12px;
        }

        .label-yes {
            background: #d4edda;
            color: #155724;
        }

        .label-no {
            background: #f8d7da;
            color: #721c24;
        }

        .label-open {
            background: #cce5ff;
            color: #004085;
        }

        .label-closed {
            background: #fff3cd;
            color: #856404;
        }

        .label-prov {
            background: #e8daef;
            color: #6c3483;
        }

        .label-kab {
            background: #d4e6f1;
            color: #1a5276;
        }

        /* Merge point */
        .merge-row {
            display: flex;
            gap: 80px;
            align-items: flex-end;
            position: relative;
        }

        .merge-row::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 100px);
            height: 3px;
            background: #2c3e50;
        }

        .merge-connector {
            width: 3px;
            height: 25px;
            background: #2c3e50;
        }

        /* Actor label */
        .actor-label {
            background: #ecf0f1;
            color: #2c3e50;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 15px 0 5px 0;
            border: 2px solid #bdc3c7;
        }

        .actor-label.admin-prov {
            background: #e8daef;
            border-color: #9b59b6;
            color: #6c3483;
        }

        .actor-label.admin-kab {
            background: #d4e6f1;
            border-color: #3498db;
            color: #1a5276;
        }

        .actor-label.perusahaan {
            background: #d5f5e3;
            border-color: #27ae60;
            color: #1e8449;
        }

        .actor-label.pencari {
            background: #fdebd0;
            border-color: #f39c12;
            color: #b9770e;
        }

        .actor-label.admin-both {
            background: #fadbd8;
            border-color: #e74c3c;
            color: #922b21;
        }

        /* Connector line */
        .connector {
            width: 3px;
            height: 15px;
            background: #2c3e50;
        }

        /* Legend */
        .legend {
            margin-top: 40px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .legend h3 {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.85rem;
            color: #555;
        }

        .legend-shape {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .legend-terminal {
            width: 60px;
            height: 25px;
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            border-radius: 15px;
        }

        .legend-process {
            width: 60px;
            height: 25px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border-radius: 4px;
        }

        .legend-decision {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f39c12 0%, #d68910 100%);
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
        }

        .legend-data {
            width: 60px;
            height: 25px;
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            clip-path: polygon(10% 0%, 100% 0%, 90% 100%, 0% 100%);
        }

        .legend-document {
            width: 50px;
            height: 30px;
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            border-radius: 4px 4px 0 0;
            position: relative;
        }

        .legend-document::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 10px;
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            border-radius: 0 0 50% 50% / 0 0 100% 100%;
        }

        .legend-database {
            width: 40px;
            height: 30px;
            background: linear-gradient(180deg, #3498db 0%, #2980b9 100%);
            border-radius: 5px 5px 50% 50% / 5px 5px 15px 15px;
            position: relative;
        }

        .legend-database::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(180deg, #5dade2 0%, #3498db 100%);
            border-radius: 50%;
        }

        /* Back arrow text */
        .back-text {
            font-size: 0.8rem;
            color: #7f8c8d;
            font-style: italic;
        }

        /* Info box */
        .info-box {
            background: #e8f4f8;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.75rem;
            color: #0c5460;
            margin: 5px 0;
            max-width: 280px;
            text-align: center;
        }

        /* Parallel section */
        .parallel-section {
            display: flex;
            gap: 40px;
            align-items: flex-start;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px dashed #bdc3c7;
            margin: 10px 0;
        }

        .parallel-lane {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            min-width: 200px;
        }

        .parallel-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
            width: 100%;
            text-align: center;
        }

        /* Job Fair Badge */
        .jobfair-badge {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ALUR JOB FAIR - ETAMKERJA</h1>
            <div class="subtitle">
                <div>ETAMKERJA</div>
                <div>DISNAKERTRANS KALIMANTAN TIMUR</div>
            </div>
        </div>
        
        <div class="flowchart-container">
            <div class="flowchart">
                <!-- START -->
                <div class="terminal">MULAI</div>
                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: ADMIN RILIS JOB FAIR -->
                <!-- ============================================ -->
                <div class="jobfair-badge">🎪 Job Fair</div>
                <div class="arrow-down"></div>

                <div class="decision">Admin<br>Level?</div>
                <div class="connector"></div>

                <div class="branch-row" style="gap: 100px;">
                    <!-- Admin Provinsi -->
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-prov">Provinsi</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="actor-label admin-prov" style="margin: 5px 0;">Admin Provinsi</div>
                        <div class="process process-auth" style="min-width: 150px;">Login</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-admin" style="min-width: 150px;">Buat Job Fair</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-success" style="min-width: 150px;">Langsung Aktif</div>
                        <div class="info-box">✓ Otomatis Terverifikasi</div>
                    </div>

                    <!-- Admin Kab/Kota -->
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-kab">Kab/Kota</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="actor-label admin-kab" style="margin: 5px 0;">Admin Kab/Kota</div>
                        <div class="process process-auth" style="min-width: 150px;">Login</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-admin" style="min-width: 150px;">Buat Job Fair</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="info-box">⏳ Menunggu Verifikasi<br>Admin Provinsi</div>
                    </div>
                </div>

                <div class="connector"></div>
                <div class="merge-row" style="gap: 100px;">
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                </div>

                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: VERIFIKASI JOB FAIR KABKOTA -->
                <!-- ============================================ -->
                <div class="actor-label admin-prov">🏢 Admin Provinsi</div>
                <div class="process">Verifikasi Job Fair<br><small>(dari Admin Kab/Kota)</small></div>
                <div class="arrow-down"></div>
                <div class="decision">Job Fair<br>Disetujui?</div>
                <div class="connector"></div>

                <div class="branch-row">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-reject" style="min-width: 150px;">Job Fair Ditolak</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="back-text">↩ Revisi</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-success" style="min-width: 150px;">Job Fair Disetujui</div>
                        <div class="merge-connector"></div>
                    </div>
                </div>

                <div class="arrow-down"></div>
                <div class="data">Data Job Fair Aktif</div>
                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: STATUS KEMITRAAN -->
                <!-- ============================================ -->
                <div class="decision">Status<br>Kemitraan?</div>
                <div class="connector"></div>

                <div class="branch-row" style="gap: 120px;">
                    <!-- TERTUTUP -->
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-closed">Tertutup</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="actor-label admin-both" style="margin: 5px 0;">Admin Prov/Kab</div>
                        <div class="process process-admin" style="min-width: 180px;">Pilih Perusahaan<br>dari Database</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="database">Database<br>Perusahaan</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-success" style="min-width: 180px;">Tambahkan sebagai<br>Mitra Job Fair</div>
                    </div>

                    <!-- TERBUKA -->
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-open">Terbuka</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="actor-label perusahaan" style="margin: 5px 0;">Perusahaan</div>
                        <div class="decision" style="width: 140px; height: 70px; font-size: 0.7rem;">Punya<br>Akun?</div>
                        <div class="connector"></div>
                        <div class="branch-row" style="gap: 20px;">
                            <div class="branch-item">
                                <div class="branch-connector" style="height: 15px;"></div>
                                <span class="branch-label label-no" style="font-size: 0.65rem;">Tidak</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="process process-auth" style="min-width: 90px; font-size: 0.75rem;">Register</div>
                            </div>
                            <div class="branch-item">
                                <div class="branch-connector" style="height: 15px;"></div>
                                <span class="branch-label label-yes" style="font-size: 0.65rem;">Ya</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="process process-auth" style="min-width: 90px; font-size: 0.75rem;">Login</div>
                            </div>
                        </div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process" style="min-width: 180px;">Daftar Mitra<br>Job Fair</div>
                    </div>
                </div>

                <div class="connector"></div>
                <div class="merge-row" style="gap: 120px;">
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                </div>

                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: VERIFIKASI MITRA -->
                <!-- ============================================ -->
                <div class="actor-label admin-both">🏢 Admin Provinsi / Kab-Kota</div>
                <div class="process">Verifikasi Pendaftaran Mitra</div>
                <div class="arrow-down"></div>
                <div class="decision">Mitra<br>Disetujui?</div>
                <div class="connector"></div>

                <div class="branch-row">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-reject" style="min-width: 150px;">Mitra Ditolak</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="document" style="min-width: 140px;">Notifikasi<br>Penolakan</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-success" style="min-width: 150px;">Mitra Diterima</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="document" style="min-width: 140px;">Notifikasi<br>Penerimaan</div>
                    </div>
                </div>

                <div class="connector"></div>
                <div class="merge-row">
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                </div>
                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: PERUSAHAAN TAMBAH LOWONGAN -->
                <!-- ============================================ -->
                <div class="actor-label perusahaan">👔 Perusahaan (Mitra Job Fair)</div>
                <div class="process">Tambah Lowongan Kerja</div>
                <div class="arrow-down"></div>
                <div class="data">Data Lowongan Job Fair</div>
                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: PENCARI KERJA DAFTAR -->
                <!-- ============================================ -->
                <div class="actor-label pencari">👤 Pencari Kerja</div>
                
                <div class="decision">Punya<br>Akun?</div>
                <div class="connector"></div>
                <div class="branch-row" style="gap: 60px;">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-auth" style="min-width: 140px;">Register Akun</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-auth" style="min-width: 140px;">Login</div>
                    </div>
                </div>
                <div class="connector"></div>
                <div class="merge-row" style="gap: 60px;">
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                </div>

                <div class="arrow-down"></div>
                <div class="data">Data Pencari Kerja</div>
                <div class="arrow-down"></div>

                <div class="process">Lihat Lowongan Job Fair</div>
                <div class="arrow-down"></div>
                <div class="decision">Tertarik<br>Melamar?</div>
                <div class="connector"></div>

                <div class="branch-row">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="back-text">↩ Cari Lowongan Lain</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process">Lamar Lowongan</div>
                        <div class="merge-connector"></div>
                    </div>
                </div>

                <div class="arrow-down"></div>
                <div class="data">Data Lamaran</div>
                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: PERUSAHAAN VERIFIKASI PELAMAR -->
                <!-- ============================================ -->
                <div class="actor-label perusahaan">👔 Perusahaan</div>
                <div class="process">Lihat Lamaran Masuk</div>
                <div class="arrow-down"></div>
                <div class="process">Verifikasi Pelamar</div>
                <div class="arrow-down"></div>
                <div class="decision">Pelamar<br>Diterima?</div>
                <div class="connector"></div>

                <div class="branch-row">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-reject" style="min-width: 150px;">Pelamar Ditolak</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="document" style="min-width: 140px;">Notifikasi<br>Penolakan</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-success" style="min-width: 150px;">Pelamar Diterima</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="document" style="min-width: 140px;">Notifikasi<br>Penerimaan</div>
                    </div>
                </div>

                <div class="connector"></div>
                <div class="merge-row">
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                    <div class="branch-item">
                        <div class="merge-connector"></div>
                    </div>
                </div>
                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: DATA PENEMPATAN -->
                <!-- ============================================ -->
                <div class="data" style="min-width: 250px;">Data Penempatan</div>
                <div class="arrow-down"></div>

                <!-- ============================================ -->
                <!-- SECTION: ADMIN MONITORING -->
                <!-- ============================================ -->
                <div class="actor-label admin-both">🏢 Admin Provinsi / Kab-Kota</div>
                <div class="process">Monitoring Data Job Fair</div>
                <div class="arrow-down"></div>
                <div class="process">Monitoring Data Penempatan</div>
                {{-- <div class="arrow-down"></div>
                <div class="process">Generate Laporan Job Fair</div> --}}
                <div class="arrow-down"></div>
                <div class="document">Laporan Penempatan</div>
                
                <div class="arrow-down" style="margin-top: 15px;"></div>

                <!-- END -->
                <div class="terminal">SELESAI</div>
            </div>

            <!-- Legend -->
            <div class="legend" hidden>
                <h3>📋 Legenda Simbol Flowchart</h3>
                <div class="legend-items">
                    <div class="legend-item">
                        <div class="legend-shape">
                            <div class="legend-terminal"></div>
                        </div>
                        <span>Terminal (Mulai/Selesai)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-shape">
                            <div class="legend-process"></div>
                        </div>
                        <span>Proses</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-shape">
                            <div class="legend-decision"></div>
                        </div>
                        <span>Keputusan (Decision)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-shape">
                            <div class="legend-data"></div>
                        </div>
                        <span>Data / Input-Output</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-shape">
                            <div class="legend-document"></div>
                        </div>
                        <span>Dokumen</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-shape">
                            <div class="legend-database"></div>
                        </div>
                        <span>Database</span>
                    </div>
                </div>

                <h3 style="margin-top: 20px;">🎭 Aktor</h3>
                <div class="legend-items" style="margin-top: 10px;">
                    <div class="legend-item">
                        <div class="actor-label admin-prov" style="margin: 0; font-size: 0.7rem;">Admin Prov</div>
                        <span>Buat & verifikasi Job Fair</span>
                    </div>
                    <div class="legend-item">
                        <div class="actor-label admin-kab" style="margin: 0; font-size: 0.7rem;">Admin Kab/Kota</div>
                        <span>Buat Job Fair (perlu ACC)</span>
                    </div>
                    <div class="legend-item">
                        <div class="actor-label perusahaan" style="margin: 0; font-size: 0.7rem;">Perusahaan</div>
                        <span>Mitra Job Fair</span>
                    </div>
                    <div class="legend-item">
                        <div class="actor-label pencari" style="margin: 0; font-size: 0.7rem;">Pencari Kerja</div>
                        <span>Pelamar</span>
                    </div>
                </div>

                <h3 style="margin-top: 20px;">📌 Status Kemitraan Job Fair</h3>
                <div class="legend-items" style="margin-top: 10px;">
                    <div class="legend-item">
                        <span class="branch-label label-open">Terbuka</span>
                        <span>Perusahaan dapat mendaftar sendiri sebagai mitra</span>
                    </div>
                    <div class="legend-item">
                        <span class="branch-label label-closed">Tertutup</span>
                        <span>Admin menambahkan perusahaan dari database</span>
                    </div>
                </div>

                <h3 style="margin-top: 20px;">📌 Ringkasan Alur</h3>
                <div class="info-box" style="max-width: 100%; text-align: left; margin-top: 10px;">
                    <ol style="margin-left: 20px; line-height: 1.8;">
                        <li><strong>Admin Provinsi</strong> Login → Buat Job Fair (langsung aktif)</li>
                        <li><strong>Admin Kab/Kota</strong> Login → Buat Job Fair → Tunggu ACC Admin Provinsi</li>
                        <li><strong>Status Kemitraan Terbuka:</strong> Perusahaan Register/Login → Daftar Mitra</li>
                        <li><strong>Status Kemitraan Tertutup:</strong> Admin pilih dari Database Perusahaan</li>
                        <li><strong>Admin</strong> Verifikasi Mitra (ACC/Tolak)</li>
                        <li><strong>Perusahaan</strong> Tambah Lowongan Kerja</li>
                        <li><strong>Pencari Kerja</strong> Register/Login → Lihat & Lamar Lowongan</li>
                        <li><strong>Perusahaan</strong> Verifikasi Pelamar (Diterima/Ditolak)</li>
                        <li><strong>Data Penempatan</strong> tersimpan di sistem</li>
                        <li><strong>Admin</strong> Monitoring & Generate Laporan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>