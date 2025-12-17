<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alur Penempatan Tenaga Kerja - ETAMKERJA</title>
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
            max-width: 100%;
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
        
        .diagram-container {
            padding: 30px;
            overflow-x: auto;
        }
        
        .swimlane-diagram {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            width: 100%;
            min-width: 900px;
            border: 2px solid #1e3a5f;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .swimlane {
            border-right: 2px solid #1e3a5f;
            min-height: 1200px;
            position: relative;
        }
        
        .swimlane:last-child {
            border-right: none;
        }
        
        .swimlane-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            position: sticky;
            top: 0;
        }
        
        .swimlane-content {
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        
        /* Activity Nodes */
        .activity {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 12px 16px;
            border-radius: 20px;
            text-align: center;
            font-size: 0.8rem;
            width: 90%;
            max-width: 180px;
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }

        /* Auth Activity - different color for register/login */
        .activity-auth {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
            box-shadow: 0 3px 10px rgba(155, 89, 182, 0.3);
        }
        
        /* Start/End Nodes */
        .start-node {
            width: 30px;
            height: 30px;
            background: #2c3e50;
            border-radius: 50%;
        }
        
        .end-node {
            width: 30px;
            height: 30px;
            background: white;
            border: 4px solid #2c3e50;
            border-radius: 50%;
            position: relative;
        }
        
        .end-node::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 14px;
            height: 14px;
            background: #2c3e50;
            border-radius: 50%;
        }
        
        /* Decision Diamond */
        .decision {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .decision-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        
        .decision-label {
            font-size: 0.7rem;
            color: #666;
            font-weight: 500;
        }
        
        /* Fork/Join Bar */
        .fork-bar {
            width: 80%;
            height: 6px;
            background: #2c3e50;
            border-radius: 3px;
        }
        
        /* Data Store (Object Node) */
        .datastore {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 10px;
            border: 2px dashed #7f8c8d;
            border-radius: 8px;
            background: #f8f9fa;
            width: 90%;
            max-width: 180px;
        }
        
        .datastore-label {
            font-size: 0.7rem;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .datastore-name {
            font-size: 0.8rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        /* Arrows */
        .arrow-down {
            width: 2px;
            height: 25px;
            background: #2c3e50;
            position: relative;
        }
        
        .arrow-down::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid #2c3e50;
        }
        
        .arrow-short {
            height: 15px;
        }
        
        /* Connector lines */
        .connector {
            width: 2px;
            height: 15px;
            background: #2c3e50;
        }
        
        /* Horizontal connector */
        .h-connector {
            position: absolute;
            height: 2px;
            background: #2c3e50;
        }
        
        /* Branch labels */
        .branch-label {
            font-size: 0.7rem;
            color: #27ae60;
            font-weight: 600;
            background: #e8f5e9;
            padding: 2px 8px;
            border-radius: 10px;
        }
        
        .branch-label.reject {
            color: #e74c3c;
            background: #fdeaea;
        }
        
        /* Spacer */
        .spacer {
            height: 20px;
        }
        
        .spacer-lg {
            height: 40px;
        }

        .spacer-xl {
            height: 60px;
        }
        
        /* Legend */
        .legend {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .legend h3 {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #555;
        }
        
        .legend-icon {
            width: 40px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .legend-icon.activity {
            width: 60px;
            padding: 5px 10px;
            font-size: 0.7rem;
        }
        
        .legend-icon .decision {
            width: 20px;
            height: 20px;
        }
        
        .legend-icon .start-node,
        .legend-icon .end-node {
            width: 20px;
            height: 20px;
        }
        
        .legend-icon .end-node::after {
            width: 10px;
            height: 10px;
        }
        
        /* Shared Database Section */
        .shared-resources {
            margin-top: 20px;
            padding: 20px;
            background: #e8f4f8;
            border-radius: 8px;
            border: 2px dashed #3498db;
            text-align: center;
        }
        
        .shared-resources h3 {
            font-size: 0.9rem;
            color: #2980b9;
            margin-bottom: 15px;
        }
        
        .database-icon {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        
        .db-cylinder {
            width: 80px;
            height: 60px;
            background: linear-gradient(180deg, #3498db 0%, #2980b9 100%);
            border-radius: 10px 10px 50% 50% / 10px 10px 20px 20px;
            position: relative;
        }
        
        .db-cylinder::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 15px;
            background: linear-gradient(180deg, #5dade2 0%, #3498db 100%);
            border-radius: 50%;
        }
        
        .db-label {
            font-size: 0.85rem;
            color: #2980b9;
            font-weight: 600;
        }

        /* Flow indicators between swimlanes */
        .cross-lane-indicator {
            font-size: 0.7rem;
            color: #7f8c8d;
            font-style: italic;
        }

        .flow-arrow {
            color: #3498db;
            font-weight: bold;
        }

        /* Branch container */
        .branch-container {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .branch-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .activity-small {
            width: 80px;
            font-size: 0.7rem;
            padding: 8px 10px;
        }

        .activity-reject {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .activity-accept {
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        }

        /* Section divider */
        .section-divider {
            width: 90%;
            border-top: 1px dashed #bdc3c7;
            margin: 10px 0;
        }

        .section-label {
            font-size: 0.65rem;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .swimlane-diagram {
                grid-template-columns: repeat(4, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ALUR PENEMPATAN TENAGA KERJA - ETAMKERJA</h1>
            <div class="subtitle">
                <div>ETAMKERJA</div>
                <div>DISNAKERTRANS KALIMANTAN TIMUR</div>
            </div>
        </div>
        
        <div class="diagram-container">
            <div class="swimlane-diagram">
                <!-- Swimlane 1: Perusahaan -->
                <div class="swimlane">
                    <div class="swimlane-header">Perusahaan</div>
                    <div class="swimlane-content">
                        <div class="spacer"></div>
                        <div class="start-node"></div>
                        <div class="arrow-down"></div>
                        
                        <!-- Auth Section -->
                        <div class="decision-container">
                            <div class="decision"></div>
                            <span class="decision-label">Punya Akun?</span>
                        </div>
                        <div class="connector"></div>
                        <div class="branch-container">
                            <div class="branch-item">
                                <span class="branch-label reject">BELUM</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-auth">Register Akun</div>
                            </div>
                            <div class="branch-item">
                                <span class="branch-label">SUDAH</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-auth">Login</div>
                            </div>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Data Perusahaan</span>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <!-- Main Process -->
                        <div class="activity">Isi Form Lowongan Kerja</div>
                        <div class="arrow-down"></div>
                        <div class="activity">Kirim Data Lowongan Kerja</div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Data Lowongan</span>
                        </div>
                        <div class="connector"></div>
                        <div class="spacer-lg"></div>
                        <div class="spacer-lg"></div>
                        <div class="spacer-xl"></div>
                        
                        <!-- Company views applications -->
                        <div class="activity">Lihat Lamaran Masuk</div>
                        <div class="arrow-down"></div>
                        <div class="activity">Verifikasi Lamaran</div>
                        <div class="arrow-down"></div>
                        <div class="decision-container">
                            <div class="decision"></div>
                        </div>
                        <div class="connector"></div>
                        <div class="branch-container">
                            <div class="branch-item">
                                <span class="branch-label reject">TOLAK</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-reject">Ditolak</div>
                            </div>
                            <div class="branch-item">
                                <span class="branch-label">TERIMA</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-accept">Diterima</div>
                            </div>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Data Penempatan</span>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="end-node"></div>
                    </div>
                </div>
                
                <!-- Swimlane 2: Admin Kabupaten/Kota -->
                <div class="swimlane">
                    <div class="swimlane-header">Admin Kabupaten/Kota</div>
                    <div class="swimlane-content">
                        <div class="spacer"></div>
                        <div class="start-node"></div>
                        <div class="arrow-down"></div>
                        
                        <!-- Auth Section - Login Only -->
                        <div class="activity activity-auth">Login</div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Data Admin</span>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <div class="cross-lane-indicator">← dari Perusahaan</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="activity">Lihat Data Lowongan Kerja</div>
                        <div class="arrow-down"></div>
                        <div class="activity">Verifikasi Lowongan Kerja</div>
                        <div class="arrow-down"></div>
                        <div class="decision-container">
                            <div class="decision"></div>
                        </div>
                        <div class="connector"></div>
                        <div class="branch-container">
                            <div class="branch-item">
                                <span class="branch-label reject">TOLAK</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-reject">Tidak Lolos Verifikasi</div>
                            </div>
                            <div class="branch-item">
                                <span class="branch-label">ACC</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-accept">Lolos Verifikasi</div>
                            </div>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Status Lowongan</span>
                        </div>
                        <div class="spacer-lg"></div>
                        <div class="spacer-lg"></div>
                        <div class="spacer-lg"></div>
                        <div class="activity">Lihat Data Penempatan</div>
                        <div class="arrow-down"></div>
                        <div class="end-node"></div>
                    </div>
                </div>
                
                <!-- Swimlane 3: Pencari Kerja -->
                <div class="swimlane">
                    <div class="swimlane-header">Pencari Kerja</div>
                    <div class="swimlane-content">
                        <div class="spacer"></div>
                        <div class="start-node"></div>
                        <div class="arrow-down"></div>
                        
                        <!-- Auth Section -->
                        <div class="decision-container">
                            <div class="decision"></div>
                            <span class="decision-label">Punya Akun?</span>
                        </div>
                        <div class="connector"></div>
                        <div class="branch-container">
                            <div class="branch-item">
                                <span class="branch-label reject">BELUM</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-auth">Register Akun</div>
                            </div>
                            <div class="branch-item">
                                <span class="branch-label">SUDAH</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small activity-auth">Login</div>
                            </div>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Data Pencari Kerja</span>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <!-- Main Process -->
                        <div class="activity">Lihat Lowongan Terverifikasi</div>
                        <div class="arrow-down"></div>
                        <div class="decision-container">
                            <div class="decision"></div>
                            <span class="decision-label">Tertarik?</span>
                        </div>
                        <div class="connector"></div>
                        <div class="branch-container">
                            <div class="branch-item">
                                <span class="branch-label reject">TIDAK</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="cross-lane-indicator">↑ Kembali</div>
                            </div>
                            <div class="branch-item">
                                <span class="branch-label">YA</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="activity activity-small">Lamar Lowongan</div>
                            </div>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Data Lamaran</span>
                        </div>
                        <div class="cross-lane-indicator" style="margin-top: 10px;">→ ke Perusahaan</div>
                        <div class="spacer-lg"></div>
                        <div class="spacer-lg"></div>
                        <div class="spacer-lg"></div>
                        <div class="activity">Lihat Status Lamaran</div>
                        <div class="arrow-down"></div>
                        <div class="end-node"></div>
                    </div>
                </div>
                
                <!-- Swimlane 4: Admin Provinsi -->
                <div class="swimlane">
                    <div class="swimlane-header">Admin Provinsi</div>
                    <div class="swimlane-content">
                        <div class="spacer"></div>
                        <div class="start-node"></div>
                        <div class="arrow-down"></div>
                        
                        <!-- Auth Section - Login Only -->
                        <div class="activity activity-auth">Login</div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Data Admin</span>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <!-- Main Process -->
                        <div class="activity">Monitoring Data Perusahaan</div>
                        <div class="arrow-down"></div>
                        <div class="activity">Monitoring Data Lowongan</div>
                        <div class="arrow-down"></div>
                        <div class="activity">Monitoring Pencari Kerja</div>
                        <div class="arrow-down"></div>
                        <div class="activity">Monitoring Penempatan</div>
                        <div class="arrow-down"></div>
                        <div class="activity">Generate Laporan</div>
                        <div class="arrow-down"></div>
                        <div class="datastore">
                            <span class="datastore-label">«datastore»</span>
                            <span class="datastore-name">Laporan IPK</span>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="end-node"></div>
                    </div>
                </div>
            </div>
            
            <!-- Shared Database Resources -->
            <div class="shared-resources">
                <h3>📦 Shared Data Store (Database ETAM Kerja)</h3>
                <div style="display: flex; justify-content: center; gap: 30px; margin-top: 15px; flex-wrap: wrap;">
                    <div class="database-icon">
                        <div class="db-cylinder"></div>
                        <span class="db-label">Data Perusahaan</span>
                    </div>
                    <div class="database-icon">
                        <div class="db-cylinder"></div>
                        <span class="db-label">Data Pencari Kerja</span>
                    </div>
                    <div class="database-icon">
                        <div class="db-cylinder"></div>
                        <span class="db-label">Data Lowongan</span>
                    </div>
                    <div class="database-icon">
                        <div class="db-cylinder"></div>
                        <span class="db-label">Data Lamaran</span>
                    </div>
                    <div class="database-icon">
                        <div class="db-cylinder"></div>
                        <span class="db-label">Data Penempatan</span>
                    </div>
                    <div class="database-icon">
                        <div class="db-cylinder"></div>
                        <span class="db-label">Data Admin</span>
                    </div>
                </div>
                <p style="font-size: 0.8rem; color: #2980b9; margin-top: 15px;">
                    Semua «datastore» di atas mengacu pada database terpusat ETAM Kerja
                </p>
            </div>
            
            <!-- Legend -->
            <div class="legend" hidden>
                <h3>📋 Legenda Simbol UML Activity Diagram</h3>
                <div class="legend-items">
                    <div class="legend-item">
                        <div class="legend-icon">
                            <div class="start-node" style="width: 20px; height: 20px;"></div>
                        </div>
                        <span>Initial Node (Mulai)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon">
                            <div class="end-node" style="width: 20px; height: 20px;"></div>
                        </div>
                        <span>Final Node (Selesai)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon activity" style="padding: 5px 8px; font-size: 0.65rem;">Action</div>
                        <span>Action/Activity</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon activity activity-auth" style="padding: 5px 8px; font-size: 0.65rem;">Auth</div>
                        <span>Autentikasi (Register/Login)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon">
                            <div class="decision" style="width: 20px; height: 20px;"></div>
                        </div>
                        <span>Decision Node</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon" style="border: 2px dashed #7f8c8d; border-radius: 4px; padding: 3px; font-size: 0.6rem;">«ds»</div>
                        <span>Data Store (Object Node)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>