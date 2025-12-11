<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <title>ETAM KERJA - Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/10.3.3/highcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/10.3.3/modules/exporting.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/10.3.3/modules/export-data.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/10.3.3/modules/offline-exporting.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- <script src="https://kit.fontawesome.com/05ac633eab.js" crossorigin="anonymous"></script> --}}

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 28px;
            margin-bottom: 30px;
            color: #333;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .chart-container {
            width: 100%;
            height: 400px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div id="mainContent" style="display: none;">
        <div class="header">
            ETAM KERJA <br /> Dashboard Kepemimpinan
        </div>

        <!-- Section 1: Jumlah Pencari Kerja -->
        <div class="section">
            <div class="section-title">Jumlah Pencari Kerja</div>
            <div id="chart1" class="chart-container"></div>
        </div>

        <!-- Section 2: Jumlah Pencari Kerja Dalam Proses Rekrutment -->
        <div class="section">
            <div class="section-title">Jumlah Pencari Kerja Dalam Proses Rekrutment</div>
            <div id="chart2" class="chart-container"></div>
        </div>

        <!-- Section 3: Jumlah Perusahaan -->
        <div class="section">
            <div class="section-title">Jumlah Perusahaan</div>
            <div id="chart3" class="chart-container"></div>
        </div>

        <!-- Section 4: Jumlah Lowongan -->
        <div class="section">
            <div class="section-title">Jumlah Kebutuhan Tenaga Kerja</div>
            <div id="chart4" class="chart-container"></div>
        </div>

        <!-- Section 5: Jumlah Penempatan -->
        <div class="section">
            <div class="section-title">Jumlah Penempatan</div>
            <div id="chart5" class="chart-container"></div>
        </div>

        <!-- Section 6: Data Pencari Kerja -->
        <div class="section">
            <div class="section-title">Data Pencari Kerja</div>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <h3 style="text-align: center; color: #2c3e50; margin-bottom: 20px;">Pendidikan Terbanyak</h3>
                    <div id="chart6-1" style="height: 300px;"></div>
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <h3 style="text-align: center; color: #2c3e50; margin-bottom: 20px;">Jurusan Terbanyak</h3>
                    <div id="chart6-2" style="height: 300px;"></div>
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <h3 style="text-align: center; color: #2c3e50; margin-bottom: 20px;">Sektor Terbanyak</h3>
                    <div id="chart6-3" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="accessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="accessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="accessForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accessModalLabel">Masukkan Kode Akses</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input type="password" class="form-control" id="kode" name="kode" autocomplete="off"
                                required>
                            <div class="invalid-feedback">Kode salah, silakan coba lagi.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tunggu sampai Highcharts sepenuhnya dimuat
        window.addEventListener('load', function() {
            if (typeof Highcharts === 'undefined') {
                console.error('Highcharts tidak berhasil dimuat');
                return;
            }

            // Data untuk kabupaten/kota
            // const kabupatenKota = [
            //     'Kabupaten Berau',
            //     'Kabupaten Kutai Barat',
            //     'Kabupaten Kutai Kartanegara',
            //     'Kabupaten Kutai Timur',
            //     'Kabupaten Mahakam Ulu',
            //     'Kabupaten Paser',
            //     'Kabupaten Penajam Paser Utara',
            //     'Kota Balikpapan',
            //     'Kota Bontang',
            //     'Kota Samarinda'
            // ];

            // Ambil data dari PHP ke JS
            const resPencari = @json($resPencari);

            // Ambil array nama_kota, pria, dan wanita
            const kabupatenKota = resPencari.map(item => item.nama_kota);
            const dataPria = resPencari.map(item => item.pria);
            const dataWanita = resPencari.map(item => item.wanita);

            // Chart 1: Jumlah Pencari Kerja
            Highcharts.chart('chart1', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Jumlah Pencari Kerja per Kabupaten/Kota'
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Jumlah_Pencari_Kerja'
                },
                xAxis: {
                    categories: kabupatenKota,
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Pencari Kerja'
                    }
                },
                legend: {
                    align: 'center'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    name: 'Laki-laki',
                    color: '#3498db',
                    // data: [850, 620, 1200, 780, 450, 680, 920, 1500, 540, 1800]
                    data: dataPria
                }, {
                    name: 'Perempuan',
                    color: '#e91e63',
                    // data: [720, 580, 1100, 650, 420, 590, 840, 1350, 480, 1650]
                    data: dataWanita
                }]
            });

            const resLamaran = @json($resLamaran);

            const dataLamaranPria = resLamaran.map(item => item.pria);
            const dataLamaranWanita = resLamaran.map(item => item.wanita);

            // Chart 2: Jumlah Pencari Kerja Dalam Proses Rekrutment
            Highcharts.chart('chart2', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Jumlah Pencari Kerja Dalam Proses Rekrutment'
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Pencari_Kerja_Proses_Rekrutment'
                },
                xAxis: {
                    categories: kabupatenKota,
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah dalam Proses'
                    }
                },
                legend: {
                    align: 'center'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    name: 'Laki-laki',
                    color: '#3498db',
                    // data: [45, 32, 68, 41, 25, 38, 52, 85, 29, 95]
                    data: dataLamaranPria
                }, {
                    name: 'Perempuan',
                    color: '#e91e63',
                    // data: [38, 28, 62, 35, 22, 33, 48, 78, 25, 88]
                    data: dataLamaranWanita
                }]
            });


            const resPerusahaan = @json($resPerusahaan);
            const jmlPerus = resPerusahaan.map(item => item.jumlah);

            // Chart 3: Jumlah Perusahaan
            Highcharts.chart('chart3', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Jumlah Perusahaan per Kabupaten/Kota'
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Jumlah_Perusahaan'
                },
                xAxis: {
                    categories: kabupatenKota,
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Perusahaan'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        },
                        color: '#27ae60'
                    }
                },
                series: [{
                    name: 'Perusahaan',
                    // data: [125, 89, 245, 156, 67, 98, 178, 385, 134, 425]
                    data: jmlPerus
                }]
            });


            const resLowongan = @json($resLowongan);
            const lowonganLaki = resLowongan.map(item => item.pria);
            const lowonganPerempuan = resLowongan.map(item => item.wanita);
            // const jmlLowonganAktif = resLowongan.map(item => item.jumlah_aktif);

            // Chart 4: Jumlah Lowongan
            Highcharts.chart('chart4', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Jumlah Kebutuhan Tenaga Kerja per Kabupaten/Kota'
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Jumlah_Lowongan'
                },
                xAxis: {
                    categories: kabupatenKota,
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Lowongan'
                    }
                },
                legend: {
                    align: 'center'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    name: 'Laki-laki',
                    color: '#3498db',
                    // data: [285, 145, 420, 195, 85, 165, 245, 580, 125, 650],
                    data: lowonganLaki
                }, {
                    name: 'Perempuan',
                    color: '#e74c3c',
                    // data: [120, 68, 180, 85, 45, 78, 105, 245, 58, 285]
                    data: lowonganPerempuan
                }]
            });

            const resPenempatan = @json($resPenempatan);
            const penempatanLaki = resPenempatan.map(item => item.pria);
            const penempatanPerempuan = resPenempatan.map(item => item.wanita);

            // Chart 5: Jumlah Penempatan
            Highcharts.chart('chart5', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Jumlah Penempatan per Kabupaten/Kota'
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Jumlah_Penempatan'
                },
                xAxis: {
                    categories: kabupatenKota,
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Penempatan'
                    }
                },
                legend: {
                    align: 'center'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    name: 'Laki-laki',
                    color: '#3498db',
                    // data: [28, 18, 42, 25, 15, 22, 32, 58, 18, 65]
                    data: penempatanLaki
                }, {
                    name: 'Perempuan',
                    color: '#e91e63',
                    // data: [24, 16, 38, 22, 13, 19, 29, 52, 15, 58]
                    data: penempatanPerempuan
                }]
            });

            const pendidikanData = @json($resTopPendidikan);
            // Chart 6-1: Pendidikan Pencari Kerja Terbanyak
            Highcharts.chart('chart6-1', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: null
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Pendidikan_Pencari_Kerja'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br/>Jumlah: <b>{point.y}</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Pendidikan',
                    colorByPoint: true,
                    data: pendidikanData.map((item, index) => ({
                        name: item.nama_pendidikan,
                        y: item.jumlah,
                        // Opsional: warna bisa diatur otomatis atau manual sesuai urutan
                        color: ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'][
                            index % 5
                        ]
                    }))
                }]
                // series: [{
                //     name: 'Pendidikan',
                //     colorByPoint: true,
                //     data: [{
                //         name: 'SMA/SMK',
                //         y: 4850,
                //         color: '#3498db'
                //     }, {
                //         name: 'SMP',
                //         y: 2120,
                //         color: '#e74c3c'
                //     }, {
                //         name: 'S1',
                //         y: 1980,
                //         color: '#2ecc71'
                //     }, {
                //         name: 'SD',
                //         y: 1450,
                //         color: '#f39c12'
                //     }, {
                //         name: 'S2',
                //         y: 680,
                //         color: '#9b59b6'
                //     }]
                // }]
            });

            const jurusanData = @json($resTopJurusan)
            // Chart 6-2: Jurusan Terbanyak Pencari Kerja
            Highcharts.chart('chart6-2', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: null
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Jurusan_Pencari_Kerja'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br/>Jumlah: <b>{point.y}</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Jurusan',
                    colorByPoint: true,
                    data: jurusanData.map((item, index) => ({
                        name: item.nama_jurusan,
                        y: item.jumlah,
                        // Opsional: warna bisa diatur otomatis atau manual sesuai urutan
                        color: ['#e67e22', '#1abc9c', '#34495e', '#e74c3c', '#3498db'][
                            index % 5
                        ]
                    }))
                }]
                // series: [{
                //     name: 'Jurusan',
                //     colorByPoint: true,
                //     data: [{
                //         name: 'RPL',
                //         y: 1450,
                //         color: '#e67e22'
                //     }, {
                //         name: 'Manajemen',
                //         y: 1280,
                //         color: '#1abc9c'
                //     }, {
                //         name: 'Mesin',
                //         y: 980,
                //         color: '#34495e'
                //     }, {
                //         name: 'Otomotif',
                //         y: 750,
                //         color: '#e74c3c'
                //     }, {
                //         name: 'Jaringan',
                //         y: 685,
                //         color: '#3498db'
                //     }]
                // }]
            });

            const sektorData = @json($resTopSektor);
            // Chart 6-3: Sektor Terbanyak
            Highcharts.chart('chart6-3', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: null
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'printChart', 'separator', 'downloadPNG',
                                'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator',
                                'downloadCSV', 'downloadXLS'
                            ]
                        }
                    },
                    filename: 'Sektor_Pencari_Kerja'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br/>Jumlah: <b>{point.y}</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Sektor',
                    colorByPoint: true,
                    data: sektorData.map((item, index) => ({
                        name: item.nama_sektor,
                        y: item.jumlah,
                        // Opsional: warna bisa diatur otomatis atau manual sesuai urutan
                        color: ['#8e44ad', '#27ae60', '#16a085', '#f39c12', '#c0392b'][
                            index % 5
                        ]
                    }))
                }]
                // series: [{
                //     name: 'Sektor',
                //     colorByPoint: true,
                //     data: [{
                //         name: 'Pertambangan',
                //         y: 2150,
                //         color: '#8e44ad'
                //     }, {
                //         name: 'Perdagangan',
                //         y: 1850,
                //         color: '#27ae60'
                //     }, {
                //         name: 'Kehutanan',
                //         y: 1320,
                //         color: '#16a085'
                //     }, {
                //         name: 'Logistik',
                //         y: 980,
                //         color: '#f39c12'
                //     }, {
                //         name: 'Pengolahan SDA',
                //         y: 750,
                //         color: '#c0392b'
                //     }]
                // }]
            });

        }); // End of window.addEventListener
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tampilkan modal saat halaman load pertama kali
            var accessModal = new bootstrap.Modal(document.getElementById('accessModal'));
            var mainContent = document.getElementById('mainContent');
            accessModal.show();

            // Event submit form
            document.getElementById('accessForm').addEventListener('submit', function(e) {
                e.preventDefault();
                let inputKode = document.getElementById('kode');
                let correctKode = "3tamKerj4";

                if (inputKode.value.trim() === correctKode) {
                    // Tampilkan konten utama
                    mainContent.style.display = 'block';
                    // Tutup modal jika benar
                    accessModal.hide();
                } else {
                    // Tampilkan warning
                    inputKode.classList.add('is-invalid');
                }
            });

            // Hilangkan warning saat mengetik ulang
            document.getElementById('kode').addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>

</html>
