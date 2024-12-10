<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER ETAM KERJA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('assets') }}/etam_fe/images/logo/icon_etam.png" type="image/x-icon">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <style>
        body {
            background-image: url('{{ asset('assets/etam_fe/images/bg_unsplash.png') }}');
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

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step-indicator .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
        }

        .step-indicator .circle.active {
            background-color: #005c99;
            color: #fff;
        }

        .btn-primary {
            background-color: #005c99;
            border-color: #005c99;
        }

        .btn-primary:hover {
            background-color: #00487a;
            border-color: #00487a;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="text-center">
            <img src="{{ asset('assets/etam_fe/images/logo/LOGO_ETAM_KERJA.png') }}" height="75" alt="">
        </div>
        <h2 class="text-center mb-4">Register {{ $dt['role_name'] }} </h2>
        <div class="step-indicator">
            <div class="circle active" data-step="1">1</div>
            <div class="circle" data-step="2">2</div>
            <div class="circle" data-step="3">3</div>
        </div>
        {{-- <form id="registrationForm"> --}}
        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="kode_role" id="kode_role" value="{{ $dt['role'] }}">
        <!-- Step 1 -->
        <div class="step" id="step1">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="whatsapp" class="form-label">WhatsApp</label>
                <input type="text" class="form-control" id="whatsapp" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" class="form-control" id="password" required>
                <input type="checkbox" id="show-password"><small>Lihat Kata Sandi</small>
            </div>
            <button type="button" id="btnStep1" class="btn btn-primary w-100 mt-3"
                onclick="nextStepBaru1()">Lanjut</button>
        </div>

        <!-- Step 2 -->
        <div class="step d-none" id="step2">
            <div class="mb-3">
                <label for="pin" class="form-label">OTP</label>
                <input type="text" class="form-control" id="otpwa" name="otpwa" maxlength="6" required>
                <input type="hidden" id="email_registered" name="email_registered">
                <input type="hidden" id="_token2" name="_token2" value="{{ csrf_token() }}">
            </div>
            {{-- <button type="button" class="btn btn-secondary w-100 mt-3" onclick="previousStep()">Back</button> --}}
            <button type="button" id="btnStep2" class="btn btn-primary w-100 mt-3"
                onclick="nextStepBaru2()">Lanjut</button>
        </div>

        <!-- Step 3 -->
        <div id="step3-container">
            @if ($dt['role'] == 'pencari-kerja')
                @include('depan.step3_pencarikerja')
            @endif

            @if ($dt['role'] == 'penyedia-kerja')
                @include('depan.step3_penyediakerja')
            @endif

            @if ($dt['role'] == 'admin-bkk')
                @include('depan.step3_bkk')
            @endif
        </div>


        {{-- </form> --}}
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#show-password").change(function() {
                $(this).prop("checked") ? $("#password").prop("type", "text") : $("#password").prop("type",
                    "password");
            });
        });
    </script>

    <script>
        let currentStep = 1;

        function showStep(step) {
            document.querySelectorAll('.step').forEach((element, index) => {
                element.classList.add('d-none');
                if (index === step - 1) {
                    element.classList.remove('d-none');
                }
            });

            document.querySelectorAll('.step-indicator .circle').forEach((circle, index) => {
                circle.classList.remove('active');
                if (index < step) {
                    circle.classList.add('active');
                }
            });
        }

        function nextStep() {
            if (currentStep < 3) {
                currentStep++;
                showStep(currentStep);
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        $("#jabatan_harapan_id").select2({
            placeholder: "Pilih Jabatan Harapan",
            allowClear: true
        });

        $("#jurusan_id").select2({
            placeholder: "Pilih Jurusan",
            allowClear: true
        });

        // document.getElementById('registrationForm').addEventListener('submit', function(event) {
        //     event.preventDefault();
        //     alert('Registration Successful!');
        // });

        // Initialize to show only the first step
        showStep(currentStep);

        function nextStepBaru1() {
            var imel = $('#email').val();
            var wa = $('#whatsapp').val();
            var pass = $('#password').val()
            var _token = $('#_token').val();
            var kd_role = $('#kode_role').val();

            if (!imel || !wa || !pass) {
                // Swal.fire({
                //     title: "<i>Title</i>",
                //     html: "Testno  sporocilo za objekt: <b>test</b>",
                //     confirmButtonText: "V <u>redu</u>",
                // });
                Swal.fire({
                    // title: 'Pastikan semua kolom terisi',
                    text: 'Pastikan semua kolom terisi',
                    icon: 'info'
                });
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('cek-awal-akun') }}",
                type: "POST",
                data: {
                    _token: _token,
                    email: imel,
                    wa: wa,
                    password: pass,
                    role: kd_role
                },
                // dataType: "html",
                success: function(response) {
                    // console.log(`STEP 1 RES : ${response}`)
                    var sts = response.status
                    var msg = response.message
                    var dt = response.data

                    if (sts == 0) {
                        Swal.fire({
                            title: 'Ooppss',
                            text: msg,
                            icon: 'warning'
                        });
                    } else if (sts == 5) {
                        Swal.fire({
                            title: 'Ooppss',
                            text: msg,
                            icon: 'warning'
                        });
                    } else {
                        Swal.fire({
                            title: 'Berhasil',
                            text: msg,
                            icon: 'success'
                        });

                        if (currentStep < 3) {
                            currentStep++;
                            showStep(currentStep);
                        }

                        $('#email_registered').val(dt.email)
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    Swal.fire({
                        title: 'Error!',
                        text: thrownError,
                        icon: 'error',
                    });
                }
            });
        }

        function nextStepBaru2() {
            var otpwa = $('#otpwa').val();
            var email_registered = $('#email_registered').val();
            var _token2 = $('#_token2').val()
            var kd_role = $('#kode_role').val();

            if (!otpwa) {
                Swal.fire({
                    // title: 'Pastikan semua kolom terisi',
                    text: 'Pastikan kolom terisi',
                    icon: 'info'
                });
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('cek-awal-otp') }}",
                type: "POST",
                data: {
                    _token: _token2,
                    email_registered: email_registered,
                    otp: otpwa,
                },
                // dataType: "html",
                success: function(response) {
                    console.log(`STEP 2 RES : ${response}`)

                    var sts = response.status
                    var msg = response.message
                    // var dt = response.data

                    if (sts == 0) {
                        Swal.fire({
                            title: 'Ooppss',
                            text: msg,
                            icon: 'warning'
                        });
                    } else {
                        // Swal.fire({
                        //     title: 'Oke',
                        //     text: 'Lanjuttt ISI BANYAK',
                        //     icon: 'success'
                        // });



                        if (currentStep < 3) {
                            currentStep++;
                            showStep(currentStep);
                        }
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    Swal.fire({
                        title: 'Error!',
                        text: thrownError,
                        icon: 'error',
                    });
                }
            });
        }


        $('#kabkota_id').on('change', function() {
            // console.log(this.value);
            var kd = this.value

            // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
            $.ajax({
                url: "{{ route('get-kecamatan-bykabkota', ':id') }}".replace(':id', kd), // Panggil API
                type: 'GET',
                success: function(response) {
                    // Kosongkan dropdown kecamatan sebelumnya
                    $('#kecamatan_id').empty();

                    $('#desa_id').empty();
                    $('#desa_id').append('<option selected disabled>Pilih Desa/Kelurahan</option>');

                    // Tambahkan opsi default
                    $('#kecamatan_id').append('<option selected disabled>Pilih Kecamatan</option>');

                    // Loop data kecamatan dan tambahkan ke dropdown
                    $.each(response, function(index, kecamatan) {
                        $('#kecamatan_id').append('<option value="' + kecamatan.id + '">' +
                            kecamatan.name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });


        $('#kecamatan_id').on('change', function() {
            // console.log(this.value);
            var kd = this.value

            // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
            $.ajax({
                url: "{{ route('get-desa-bykecamatan', ':id') }}".replace(':id', kd), // Panggil API
                type: 'GET',
                success: function(response) {
                    // Kosongkan dropdown kecamatan sebelumnya
                    $('#desa_id').empty();

                    // Tambahkan opsi default
                    $('#desa_id').append('<option selected disabled>Pilih Desa/Kelurahan</option>');

                    // Loop data kecamatan dan tambahkan ke dropdown
                    $.each(response, function(index, kecamatan) {
                        $('#desa_id').append('<option value="' + kecamatan.id + '">' +
                            kecamatan.name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });


        $('#pendidikan_id').on('change', function() {
            // console.log(this.value);
            var kd = this.value

            // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
            $.ajax({
                url: "{{ route('get-jurusan-bypendidikan', ':id') }}".replace(':id', kd), // Panggil API
                type: 'GET',
                success: function(response) {
                    // Kosongkan dropdown kecamatan sebelumnya
                    $('#jurusan_id').empty();

                    // Tambahkan opsi default
                    $('#jurusan_id').append('<option selected disabled>Pilih Jurusan</option>');

                    // Loop data kecamatan dan tambahkan ke dropdown
                    $.each(response, function(index, jurusan) {
                        $('#jurusan_id').append('<option value="' + jurusan.id + '">' +
                            jurusan.nama + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });

        $('#disabilitas').on('change', function() {
            // console.log(this.value);
            var kd = this.value

            if (kd == 1) {
                //disabilitas
                $('#drop_jenisdisabilitas').removeClass('d-none');
                $('#wrap_ket_disabilitas').removeClass('d-none');

                //add attr required
                $('#jenis_disabilitas').prop('required', true);
                $('#keterangan_disabilitas').prop('required', true);
            } else {
                $('#drop_jenisdisabilitas').addClass('d-none');
                $('#wrap_ket_disabilitas').addClass('d-none');

                $('#jenis_disabilitas').prop('required', false);
                $('#keterangan_disabilitas').prop('required', false);
            }
        });
    </script>

    <script>
        $('#provinsi_id').on('change', function() {
            // console.log(this.value);
            var kd = this.value

            // Panggil API untuk mendapatkan kecamatan berdasarkan kabkota_id
            $.ajax({
                url: "{{ route('get-kabkota-byprov', ':id') }}".replace(':id', kd), // Panggil API
                type: 'GET',
                success: function(response) {
                    // Kosongkan dropdown kecamatan sebelumnya
                    $('#kabkota_id').empty();

                    // Tambahkan opsi default
                    $('#kabkota_id').append('<option selected disabled>Pilih Kabupaten/Kota</option>');

                    // Loop data kecamatan dan tambahkan ke dropdown
                    $.each(response, function(index, kabkota) {
                        $('#kabkota_id').append('<option value="' + kabkota.id + '">' +
                            kabkota.name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>
