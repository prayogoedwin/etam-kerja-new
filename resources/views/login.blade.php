<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN ETAM KERJA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <style>
        /* body {
      background-color: #03A859;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    } */

        body {
            background-image: url('https://images.unsplash.com/photo-1646432581107-06cd4333cfde?q=80&w=2873&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            /* Mengatur gambar agar menutupi seluruh latar belakang */
            background-position: center;
            /* Memastikan gambar berada di tengah */
            background-repeat: no-repeat;
            /* Menghindari pengulangan gambar */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }


        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            max-width: 600px;
            width: 100%;
        }

        .btn-primary {
            background-color: #005c99;
            border-color: #005c99;
        }

        .btn-primary:hover {
            background-color: #00487a;
            border-color: #00487a;
        }

        .captcha-image {
            display: block;
            margin-bottom: 10px;
            width: 100%;
            height: 50px;
            background-color: #ddd;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            font-weight: bold;
            color: #555;
        }
    </style>

    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
</head>

<body>
    <div class="container">

        
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tambahkan ini --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


        <div class="text-center">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/etam_fe/images/logo/LOGO_ETAM_KERJA.png') }}" height="75" alt="">
            </a>

        </div>
        <h2 class="text-center mb-4">Login</h2>
        <form action="{{ route('login.action') }}" method="POST" id="loginForm">
            @csrf <!-- Laravel CSRF token for security -->
            <input type="hidden" name="recaptcha_token" id="recaptcha_token">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <input type="checkbox" id="show-password"><small>Lihat Kata Sandi</small>
            </div>
            <!-- <div class="mb-3">
        <div class="captcha-image">AB12CD</div>
        <label for="captcha" class="form-label">Captcha</label>
        <input type="text" class="form-control" id="captcha" placeholder="Enter the text shown" required>
      </div> -->

            <div class="mb-3">
                <label for="captcha" class="form-label">Captcha</label>
                <div>
                    <img src="{{ captcha_src() }}" id="captchaImage" alt="captcha" class="mb-2">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="refreshCaptcha()">Refresh</button>
                </div>
                <input type="text" name="captcha" class="form-control" id="captcha" required
                    placeholder="Enter the text shown">
                @if ($errors->has('captcha'))
                    <span class="text-danger">{{ $errors->first('captcha') }}</span>
                @endif
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>



    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshCaptcha() {
            // location.reload();
             $('#captchaImage').attr('src', '{{ captcha_src() }}' + '?' + Date.now());
        }
    </script>

    <script>
        $(document).ready(function() {
            $("#show-password").change(function() {
                $(this).prop("checked") ? $("#password").prop("type", "text") : $("#password").prop("type",
                    "password");
            });
        });
    </script>

    <script>
        const RECAPTCHA_SITE_KEY = '{{ config('services.recaptcha.site_key') }}';
        
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            let form = this;
            
            console.log('Form submit triggered'); // debug 1
            
            grecaptcha.ready(function() {
                console.log('grecaptcha ready'); // debug 2
                
                grecaptcha.execute(RECAPTCHA_SITE_KEY, {action: 'login'}).then(function(token) {
                    console.log('Token generated:', token); // debug 3 - ini yang penting
                    $('#recaptcha_token').val(token);
                    console.log('Hidden input value:', $('#recaptcha_token').val()); // debug 4
                    form.submit();
                }).catch(function(err) {
                    console.error('reCAPTCHA error:', err);
                });
            });
        });
    </script>

</body>


</html>
