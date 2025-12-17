<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flowchart Penempatan Tenaga Kerja - ETAMKERJA</title>
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
            min-width: 800px;
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

        /* Section divider */
        .section-divider {
            width: 100%;
            max-width: 600px;
            border-top: 2px dashed #bdc3c7;
            margin: 20px 0;
            position: relative;
        }

        .section-divider span {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 15px;
            font-size: 0.7rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
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

        /* Parallel process indicator */
        .parallel-section {
            display: flex;
            gap: 60px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FLOWCHART PENEMPATAN TENAGA KERJA - ETAMKERJA</h1>
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

                <!-- Parallel Registration Section -->
                <div class="parallel-section">
                    <!-- Perusahaan Lane -->
                    <div class="parallel-lane">
                        <div class="parallel-title">👔 Perusahaan</div>
                        <div class="decision">Punya<br>Akun?</div>
                        <div class="connector"></div>
                        <div class="branch-row" style="gap: 30px;">
                            <div class="branch-item">
                                <div class="branch-connector"></div>
                                <span class="branch-label label-no">Tidak</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="process process-auth" style="min-width: 120px;">Register Akun</div>
                            </div>
                            <div class="branch-item">
                                <div class="branch-connector"></div>
                                <span class="branch-label label-yes">Ya</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="process process-auth" style="min-width: 120px;">Login</div>
                            </div>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="data">Data Perusahaan</div>
                    </div>

                    <!-- Pencari Kerja Lane -->
                    <div class="parallel-lane">
                        <div class="parallel-title">👤 Pencari Kerja</div>
                        <div class="decision">Punya<br>Akun?</div>
                        <div class="connector"></div>
                        <div class="branch-row" style="gap: 30px;">
                            <div class="branch-item">
                                <div class="branch-connector"></div>
                                <span class="branch-label label-no">Tidak</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="process process-auth" style="min-width: 120px;">Register Akun</div>
                            </div>
                            <div class="branch-item">
                                <div class="branch-connector"></div>
                                <span class="branch-label label-yes">Ya</span>
                                <div class="arrow-down arrow-short"></div>
                                <div class="process process-auth" style="min-width: 120px;">Login</div>
                            </div>
                        </div>
                        <div class="arrow-down"></div>
                        <div class="data">Data Pencari Kerja</div>
                    </div>
                </div>

                <div class="arrow-down"></div>

                <!-- PERUSAHAAN: Buat Lowongan -->
                <div class="actor-label">👔 Perusahaan</div>
                <div class="process">Isi Form Lowongan Kerja</div>
                <div class="arrow-down"></div>
                <div class="process">Kirim Data Lowongan</div>
                <div class="arrow-down"></div>
                <div class="data">Data Lowongan</div>
                <div class="arrow-down"></div>

                <!-- ADMIN KAB/KOTA: Verifikasi -->
                <div class="actor-label">🏛️ Admin Kabupaten/Kota</div>
                <div class="process">Verifikasi Lowongan Kerja</div>
                <div class="arrow-down"></div>
                <div class="decision">Lowongan<br>Valid?</div>
                <div class="connector"></div>

                <div class="branch-row">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-reject">Lowongan Ditolak</div>
                        <div class="arrow-down arrow-short"></div>
                        <div style="font-size: 0.8rem; color: #7f8c8d;">↩ Kembali ke Perusahaan</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-success">Lowongan Disetujui</div>
                        <div class="merge-connector"></div>
                    </div>
                </div>

                <div class="arrow-down"></div>
                <div class="data">Lowongan Terverifikasi</div>
                <div class="arrow-down"></div>

                <!-- PENCARI KERJA: Melamar -->
                <div class="actor-label">👤 Pencari Kerja</div>
                <div class="process">Lihat Lowongan Terverifikasi</div>
                <div class="arrow-down"></div>
                <div class="decision">Tertarik<br>Melamar?</div>
                <div class="connector"></div>

                <div class="branch-row">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div style="font-size: 0.8rem; color: #7f8c8d;">↩ Cari Lowongan Lain</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process">Kirim Lamaran</div>
                        <div class="merge-connector"></div>
                    </div>
                </div>

                <div class="arrow-down"></div>
                <div class="data">Data Lamaran</div>
                <div class="arrow-down"></div>

                <!-- PERUSAHAAN: Verifikasi Lamaran -->
                <div class="actor-label">👔 Perusahaan</div>
                <div class="process">Lihat Lamaran Masuk</div>
                <div class="arrow-down"></div>
                <div class="process">Verifikasi Lamaran</div>
                <div class="arrow-down"></div>
                <div class="decision">Lamaran<br>Diterima?</div>
                <div class="connector"></div>

                <div class="branch-row">
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-no">Tidak</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-reject">Lamaran Ditolak</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="document" style="min-width: 150px;">Notifikasi Penolakan</div>
                    </div>
                    <div class="branch-item">
                        <div class="branch-connector"></div>
                        <span class="branch-label label-yes">Ya</span>
                        <div class="arrow-down arrow-short"></div>
                        <div class="process process-success">Lamaran Diterima</div>
                        <div class="arrow-down arrow-short"></div>
                        <div class="document" style="min-width: 150px;">Notifikasi Penerimaan</div>
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

                <!-- Data Penempatan -->
                <div class="data">Data Penempatan</div>
                <div class="arrow-down"></div>

                <!-- ADMIN PROVINSI: Monitoring -->
                <div class="actor-label">🏢 Admin Provinsi</div>
                <div class="process">Monitoring Data Penempatan</div>
                <div class="arrow-down"></div>
                {{-- <div class="process">Generate Laporan IPK</div>
                <div class="arrow-down"></div> --}}
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
                </div>


                <h3 style="margin-top: 20px;">🎭 Aktor</h3>
                <div class="legend-items" style="margin-top: 10px;">
                    <div class="legend-item">
                        <div class="actor-label admin-prov" style="margin: 0; font-size: 0.7rem;">Admin Prov</div>
                    </div>
                    <div class="legend-item">
                        <div class="actor-label admin-kab" style="margin: 0; font-size: 0.7rem;">Admin Kab/Kota</div>
                    </div>
                    <div class="legend-item">
                        <div class="actor-label perusahaan" style="margin: 0; font-size: 0.7rem;">Perusahaan</div>
                    </div>
                    <div class="legend-item">
                        <div class="actor-label pencari" style="margin: 0; font-size: 0.7rem;">Pencari Kerja</div>
                    </div>
                </div>

            </div>

            
        </div>
    </div>
</body>
</html>