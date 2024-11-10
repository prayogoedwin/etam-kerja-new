@include('backend.template.header')

 @if (Auth::user()->roles[0]['name'] == 'super-admin')
 @include('backend.template.sidebar')
 @endif

 @if (Auth::user()->roles[0]['name'] == 'pencari-kerja')
 @include('backend.template.sidebar-pencari')
 @endif

<body>
    @yield('content')
    @include('backend.template.footer')
    @stack('js')
</body>
</html>

