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
