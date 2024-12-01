@extends('backend.template.backend')

@section('content')
<body class="box-layout container background-green">
    
    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        <div class="pcoded-content">


            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">AK 1</h5>
                            </div>
                            {{-- <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="#!">Hospital</a></li>
                                <li class="breadcrumb-item"><a href="#!">Department</a></li>
                            </ul> --}}
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->


            <!-- [ Main Content ] start -->
            <div class="row">


                <!-- customar project  start -->
                <div class="col-xl-12">
                    <div class="card">

                        <div class="card-body">
                            
                            <div class="container">
                                <h2 class="text-center mb-4">Register {{ $dt['role_name'] }} </h2>
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
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" required>
                                        <input type="checkbox" id="show-password"><small>Lihat Kata Sandi</small>
                                    </div>
                                    <button type="button" id="btnStep1" class="btn btn-primary w-100 mt-3"
                                        onclick="nextStepBaru1()">Next</button>
                                </div>
    
                                <!-- Step 2 -->
                                <div class="step d-none" id="step2">
                                    <div class="mb-3">
                                        <label for="pin" class="form-label">OTP</label>
                                        <input type="text" class="form-control" id="otpwa" name="otpwa" maxlength="6"
                                            required>
                                        <input type="hidden" id="email_registered" name="email_registered">
                                        <input type="hidden" id="_token2" name="_token2" value="{{ csrf_token() }}">
                                    </div>
                                    {{-- <button type="button" class="btn btn-secondary w-100 mt-3" onclick="previousStep()">Back</button> --}}
                                    <button type="button" id="btnStep2" class="btn btn-primary w-100 mt-3"
                                        onclick="nextStepBaru2()">Next</button>
                                </div>
    
                                <!-- Step 3 -->
                                <div id="step3-container">
                                    @if ($dt['role'] == 'pencari-kerja')
                                        @include('backend.ak1.step3_pencarikerja')
                                    @endif
                                </div>
                                {{-- </form> --}}
                            </div>

                        </div>

                    </div>
                </div>
                <!-- customar project  end -->


            </div>
            <!-- [ Main Content ] end -->


        </div>
    </div>
@endsection

@push('js')


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
            url: "{{ route('cek-awal-akun-ak1') }}",
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
                console.log(`STEP 1 RES : ${response}`)
                var sts = response.status
                var msg = response.message
                var dt = response.data

                if (sts == 0) {
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
            url: "{{ route('cek-awal-otp-ak1') }}",
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

<script>
    function togglePekerjaanFields(status) {
        const pekerjaanFields = document.getElementById('pekerjaan-fields');
        pekerjaanFields.style.display = (status === '1') ? 'block' : 'none';
    }
</script>


@endpush
