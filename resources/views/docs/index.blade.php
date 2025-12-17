<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi Alur Sistem - ETAMKERJA</title>
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .logo-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .logo-icon {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            padding: 8px;
            overflow: hidden;
        }
        
        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .logo-text {
            text-align: left;
        }
        
        .logo-text h1 {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .logo-text p {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
        }
        
        .header-desc {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }
        
        .card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        }
        
        .card-header {
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(30deg);
        }
        
        .card-header.penempatan {
            background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%);
        }
        
        .card-header.magang-pemerintah {
            background: linear-gradient(135deg, #16a085 0%, #0e6655 100%);
        }
        
        .card-header.magang-mandiri {
            background: linear-gradient(135deg, #2874a6 0%, #1b4f72 100%);
        }
        
        .card-header.jobfair {
            background: linear-gradient(135deg, #c0392b 0%, #922b21 100%);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .card-desc {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .card-features {
            list-style: none;
            margin-bottom: 20px;
        }
        
        .card-features li {
            padding: 6px 0;
            font-size: 0.85rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-features li::before {
            content: '✓';
            color: #27ae60;
            font-weight: bold;
        }
        
        .card-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .card:hover .card-btn {
            background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%);
        }
        
        .card-btn svg {
            width: 16px;
            height: 16px;
            transition: transform 0.3s ease;
        }
        
        .card:hover .card-btn svg {
            transform: translateX(4px);
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        
        .footer p {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }
        
        .footer a {
            color: white;
            text-decoration: none;
        }
        
        /* Stats */
        .stats {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: white;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            margin-top: 5px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .logo-text h1 {
                font-size: 1.5rem;
            }
            
            .logo-icon {
                width: 55px;
                height: 55px;
            }
            
            .stats {
                gap: 30px;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-wrapper">
                <div class="logo-icon">
                    <img src="{{ asset('assets/etam_fe/images/logo/LOGO_ETAM_KERJA.png') }}" alt="Logo ETAMKERJA">
                </div>
                <div class="logo-text">
                    <h1>ETAMKERJA</h1>
                    <p>Disnakertrans Kalimantan Timur</p>
                </div>
            </div>
            <p class="header-desc">
                Dokumentasi alur sistem dan flowchart untuk memahami proses bisnis pada platform ETAMKERJA
            </p>
            
            <!-- Stats -->
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">4</div>
                    <div class="stat-label">Modul Dokumentasi</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5</div>
                    <div class="stat-label">Aktor Sistem</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">20+</div>
                    <div class="stat-label">Proses Bisnis</div>
                </div>
            </div>
        </div>
        
        <!-- Cards -->
        <div class="cards-grid">
            <!-- Penempatan Kerja -->
            <a href="{{ route('docs.penempatan') }}" class="card">
                <div class="card-header penempatan">
                    <div class="card-icon">💼</div>
                    <div class="card-title">Penempatan Tenaga Kerja</div>
                </div>
                <div class="card-body">
                    <p class="card-desc">
                        Alur proses penempatan tenaga kerja dari pendaftaran lowongan hingga penempatan kerja.
                    </p>
                    <ul class="card-features">
                        <li>Registrasi Perusahaan & Pencari Kerja</li>
                        <li>Verifikasi Lowongan oleh Admin</li>
                        <li>Proses Lamaran & Seleksi</li>
                        <li>Data Penempatan & Laporan IPK</li>
                    </ul>
                    <div class="card-btn">
                        Lihat Flowchart
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
            
            <!-- Magang Pemerintah -->
            <a href="{{ route('docs.magang.pemerintah') }}" class="card">
                <div class="card-header magang-pemerintah">
                    <div class="card-icon">🏛️</div>
                    <div class="card-title">Magang Pemerintah</div>
                </div>
                <div class="card-body">
                    <p class="card-desc">
                        Alur program magang yang diinisiasi oleh pemerintah dengan status kerjasama terbuka/tertutup.
                    </p>
                    <ul class="card-features">
                        <li>Admin Buat Program Magang</li>
                        <li>Status Kerjasama Terbuka/Tertutup</li>
                        <li>Pendaftaran Perusahaan Mitra</li>
                        <li>Monitoring & Laporan Pemagangan</li>
                    </ul>
                    <div class="card-btn">
                        Lihat Flowchart
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
            
            <!-- Magang Mandiri -->
            <a href="{{ route('docs.magang.mandiri') }}" class="card">
                <div class="card-header magang-mandiri">
                    <div class="card-icon">🎓</div>
                    <div class="card-title">Pemagangan Mandiri</div>
                </div>
                <div class="card-body">
                    <p class="card-desc">
                        Alur pemagangan mandiri dimana perusahaan langsung membuat lowongan magang sendiri.
                    </p>
                    <ul class="card-features">
                        <li>Perusahaan Input Lowongan Magang</li>
                        <li>Verifikasi oleh Admin Kab/Kota</li>
                        <li>Pencari Kerja Daftar Magang</li>
                        <li>Seleksi & Data Pemagangan</li>
                    </ul>
                    <div class="card-btn">
                        Lihat Flowchart
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
            
            <!-- Job Fair -->
            <a href="{{ route('docs.job.fair') }}" class="card">
                <div class="card-header jobfair">
                    <div class="card-icon">🎪</div>
                    <div class="card-title">Job Fair</div>
                </div>
                <div class="card-body">
                    <p class="card-desc">
                        Alur penyelenggaraan job fair dari pembuatan event hingga penempatan peserta.
                    </p>
                    <ul class="card-features">
                        <li>Admin Rilis Job Fair</li>
                        <li>Status Kemitraan Terbuka/Tertutup</li>
                        <li>Pendaftaran Mitra Perusahaan</li>
                        <li>Lowongan, Lamaran & Penempatan</li>
                    </ul>
                    <div class="card-btn">
                        Lihat Flowchart
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>© 2025 ETAMKERJA - Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur</p>
        </div>
    </div>
</body>
</html>