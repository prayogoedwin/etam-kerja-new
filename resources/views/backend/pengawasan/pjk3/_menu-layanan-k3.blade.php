<li class="nav-item pcoded-hasmenu">
    <a href="#!" class="nav-link">
        <span class="pcoded-micon"><i class="feather icon-shield"></i></span>
        <span class="pcoded-mtext">Layanan K3</span>
    </a>
    <ul class="pcoded-submenu">
        <li><a href="{{ route('pengawasan.pjk3.admin.index') }}">Pendaftaran PJK3</a></li>
        <li><a href="{{ route('pengawasan.suket.admin.pengajuan') }}">Pengajuan Suket Layak K3 oleh PJK3</a></li>
        <li><a href="{{ route('pengawasan.suket.admin.terbit') }}">Suket Layak K3</a></li>
        @php
            $roleMasterK3 = Auth::user()->roles[0]['name'] ?? '';
            $kodeMasterK3 = (string) (Auth::user()->kode_struktur ?? '');
            $bisaMasterK3 = in_array($roleMasterK3, ['super-admin', 'admin-provinsi'], true)
                || ($roleMasterK3 === 'admin-bidang' && ($kodeMasterK3 === '2' || str_starts_with($kodeMasterK3, '2')));
        @endphp
        @if ($bisaMasterK3)
            <li><a href="{{ route('pengawasan.k3.master.kategori.index') }}">Kategori K3</a></li>
            <li><a href="{{ route('pengawasan.k3.master.jenis.index') }}">Jenis objek K3</a></li>
        @endif
    </ul>
</li>
