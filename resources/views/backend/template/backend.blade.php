@include('backend.template.header')
@include('backend.template.sidebar')

<body>
    @yield('content')

    <!-- Tempatkan di sini sebelum penutupan </body> -->
  
    @include('backend.template.footer')
    @stack('js')
</body>
</html>

