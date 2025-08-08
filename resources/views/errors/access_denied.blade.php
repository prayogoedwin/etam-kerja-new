<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <title>Akses Ditolak</title>
    <style>
        body {
            background-color: #000;
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
        }
        h1 {
            color: red;
            font-size: 48px;
        }
        p {
            color: red;
            font-size: 18px;
        }
        #countdown {
            font-size: 24px;
            font-weight: bold;
            color: blue;
        }
    </style>
    <script>
        let countdown = 7; // Hitung mundur mulai dari 5 detik

        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            const interval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(interval); // Hentikan hitung mundur
                    window.location.href = "https://polri.go.id"; // Redirect ke URL tujuan
                }
            }, 1000); // Interval 1 detik
        }

        window.onload = startCountdown; // Mulai hitung mundur saat halaman selesai dimuat
    </script>
</head>
<body>
    <h1>Akses Ditolak</h1>
    <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <p>Anda akan diarahkan ke <a href="https://polri.go.id" target="_blank">polri.go.id</a> dalam <span id="countdown">7</span> detik.</p>
</body>
</html>
