    {{-- START MODAL UBAH PASSWORD --}}
    <div class="modal fade" id="modal-ubahpass" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-ubahpass">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password"
                                    class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('current_password')">
                                    <i id="icon-current_password" class="feather icon-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" id="new_password" name="new_password" class="form-control"
                                    required>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('new_password')">
                                    <i id="icon-new_password" class="feather icon-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('new_password_confirmation')">
                                    <i id="icon-confirm_password" class="feather icon-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="save-ubahpass" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- END MODAL UBAH PASSWORD --}}

        <!-- Modal ak1 -->
        <div class="modal fade" id="ak1Modal" tabindex="-1" aria-labelledby="ak1ModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ak1ModalLabel">Pilih Opsi Cetak AK1</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>Silakan pilih apakah pengguna sudah memiliki akun atau belum.</p>
                        <div class="d-grid gap-2">
                            <form action="#" method="POST">
                                @csrf
                                <button type="submit" name="role_dipilih" value="tenaga-kerja"
                                    class="btn btn-primary">Belum Punya Akun</button>
                            </form>
                            <button class="btn btn-secondary" onclick="cetakExisting()">Sudah Punya Akun</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
          <!-- emd Modal ak1 -->

    <!-- Required Js -->

    <script src="{{ asset('assets') }}/etam_be/js/vendor-all.min.js"></script>
    <script src="{{ asset('assets') }}/etam_be/js/plugins/bootstrap.min.js"></script>
    <script src="{{ asset('assets') }}/etam_be/js/ripple.js"></script>
    <script src="{{ asset('assets') }}/etam_be/js/pcoded.min.js"></script>

    <!-- Include jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- datatable Js -->
    <script src="{{ asset('assets') }}/etam_be/js/plugins/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/etam_be/js/plugins/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/etam_be/js/pages/data-basic-custom.js"></script>

    <!-- Masukkan script Summernote -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <!-- Apex Chart -->
    <script src="{{ asset('assets') }}/etam_be/js/plugins/apexcharts.min.js"></script>
    <!-- custom-chart js -->
    <script src="{{ asset('assets') }}/etam_be/js/pages/dashboard-main.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            checkCookie();
        });

        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function checkCookie() {
            var ticks = getCookie("modelopen");
            if (ticks != "") {
                ticks++;
                setCookie("modelopen", ticks, 1);
                if (ticks == "2" || ticks == "1" || ticks == "0") {
                    $('#exampleModalCenter').modal();
                }
            } else {
                // user = prompt("Please enter your name:", "");
                $('#exampleModalCenter').modal();
                ticks = 1;
                setCookie("modelopen", ticks, 1);
            }
        }
    </script>

    <script>
        function showUbahPassword() {
            $('#modal-ubahpass').modal('show');
        }

        $('#save-ubahpass').on('click', function() {
            const formData = {
                current_password: $('#current_password').val(),
                new_password: $('#new_password').val(),
                new_password_confirmation: $('#new_password_confirmation').val(),
            };

            $.ajax({
                url: '{{ route('ubah-password') }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 1) {
                        alert('Password berhasil diubah');
                        $('#modal-ubahpass').modal('hide');
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseText);
                }
            });
        });

        function togglePassword(fieldId) {
            const inputField = document.getElementById(fieldId);
            const icon = document.getElementById(`icon-${fieldId}`);

            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.remove('icon-eye');
                icon.classList.add('icon-eye-off');
            } else {
                inputField.type = 'password';
                icon.classList.remove('icon-eye-off');
                icon.classList.add('icon-eye');
            }
        }
    </script>

    <!-- Trigger pop-up -->
    <script>
        function cetakExisting() {
            window.location.href = '{{ route('ak1.existing') }}';
        }
    </script>
