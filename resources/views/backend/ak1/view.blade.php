<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail AK1</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            max-width: 500px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
            margin: 8px 0;
        }

        p strong {
            color: #333;
        }

        .qr-code {
            margin-top: 15px;
            display: flex;
            justify-content: center;
        }

        img {
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 4px;
            width: 150px;
            height: 150px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Detail AK1</h1>
        <p><strong>Tanggal Cetak:</strong>
            {{ \Carbon\Carbon::parse($ak1->tanggal_cetak)->locale('id')->translatedFormat('d F Y') }}</p>
        <p><strong>Status Cetak:</strong> {{ $ak1->status_cetak == '0' ? 'Mandiri' : 'Admin' }}</p>
        <p><strong>Dicetak oleh:</strong> {{ $ak1->dicetakOleh->name ?? 'Tidak diketahui' }}</p>
        <p><strong>Berhenti Berlaku:</strong>
            {{ \Carbon\Carbon::parse($ak1->berlaku_hingga)->locale('id')->translatedFormat('d F Y') }}</p>
    </div>
</body>

</html>
