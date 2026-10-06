<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dokumentasi API - ETAMKERJA</title>
    <style>
        :root {
            --bg: #0f2a1f;
            --card: #163528;
            --accent: #3dba7c;
            --text: #e8f5ee;
            --muted: #9bb8a8;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top right, rgba(61,186,124,.25), transparent 40%),
                linear-gradient(160deg, #0b1f17, #123024 55%, #0f2a1f);
        }
        .card {
            width: min(420px, 92vw);
            background: var(--card);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 20px 50px rgba(0,0,0,.35);
        }
        h1 { margin: 0 0 8px; font-size: 1.4rem; }
        p { margin: 0 0 20px; color: var(--muted); font-size: .95rem; }
        label { display: block; margin-bottom: 6px; font-size: .9rem; }
        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(0,0,0,.2);
            color: var(--text);
            margin-bottom: 14px;
        }
        button {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 12px 14px;
            background: var(--accent);
            color: #062114;
            font-weight: 700;
            cursor: pointer;
        }
        .error {
            background: rgba(220, 53, 69, .15);
            border: 1px solid rgba(220, 53, 69, .4);
            color: #ffb3bc;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: .9rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Swagger API Integrasi</h1>
        <p>Masuk dengan username & password user integrasi untuk membuka Swagger UI.</p>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('docs.api.login.post') }}">
            @csrf
            <label for="username">Username</label>
            <input id="username" name="username" value="{{ old('username') }}" required autofocus>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <button type="submit">Masuk Dokumentasi</button>
        </form>
    </div>
</body>
</html>
