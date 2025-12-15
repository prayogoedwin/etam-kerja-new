@include('backend.template.header')



<body>
    @yield('content')
    @include('backend.template.footer')
    @stack('js')
</body>

</html>
