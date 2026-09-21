@include('backend.template.header')

@if (Auth::user()->roles[0]['name'] == 'super-admin')
    @include('backend.template.sidebar')
@endif

@if (Auth::user()->roles[0]['name'] == 'admin-provinsi')
    @include('backend.template.sidebar')
@endif


@if (Auth::user()->roles[0]['name'] == 'admin-kabkota' || Auth::user()->roles[0]['name'] == 'admin-kabkota-officer')
    @include('backend.template.sidebar-kabkota')
@endif

@if (Auth::user()->roles[0]['name'] == 'pencari-kerja')
    @include('backend.template.sidebar-pencari')
@endif

@if (Auth::user()->roles[0]['name'] == 'penyedia-kerja')
    @include('backend.template.sidebar-penyedia')
@endif

@if (Auth::user()->roles[0]['name'] == 'admin-bkk')
    @include('backend.template.sidebar-bkk')
@endif

@if (in_array(Auth::user()->roles[0]['name'], ['admin-blk', 'kepala-balai', 'admin-balai', 'petugas-balai']))
    @include('backend.template.sidebar-blk')
@endif

@if (Auth::user()->roles[0]['name'] == 'admin-bidang' && Auth::user()->kode_struktur == '41')
    @include('backend.template.sidebar-hi')
@endif

<body>
    @yield('content')
    @include('backend.template.footer')
    @stack('js')
</body>

</html>
