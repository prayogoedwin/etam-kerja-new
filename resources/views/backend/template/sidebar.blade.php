<style>
    .pending-lowongan-badge {
    background-color: #dc3545;
    color: #fff;
    font-size: 11px;
    min-width: 20px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 50%;
    margin-left: 5px;
    padding: 0 5px;
}
</style>
<nav class="pcoded-navbar menu-light ">
    <div class="navbar-wrapper  ">
        <div class="navbar-content scroll-div ">

            <div class="">
                <div class="main-menu-header">
                    <img class="img-radius" src="{{ asset('assets/etam_be/images/user/avatar-x.png') }}"
                        alt="User-Profile-Image">
                    <div class="user-details">
                        <div id="more-details"> {{ Auth::user()->name }}
                            {{-- <i class="fa fa-caret-down"></i> --}}
                        </div>
                    </div>
                </div>
                {{-- <div class="collapse" id="nav-user-link">
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="user-profile.html" data-toggle="tooltip"
                                title="View Profile"><i class="feather icon-user"></i></a></li>
                        <li class="list-inline-item"><a href="email_inbox.html"><i class="feather icon-mail"
                                    data-toggle="tooltip" title="Messages"></i><small
                                    class="badge badge-pill badge-primary">5</small></a></li>
                        <li class="list-inline-item"><a href="auth-signin.html" data-toggle="tooltip" title="Logout"
                                class="text-danger"><i class="feather icon-power"></i></a></li>
                    </ul>
                </div> --}}
            </div>

            <ul class="nav pcoded-inner-navbar ">
                <li class="nav-item pcoded-menu-caption">
                    <label>Navigation</label>
                </li>
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                </li>
                {{-- <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="feather icon-sliders"></i></span><span class="pcoded-mtext">Sample Parent</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('sample') }}">Sample Sub Menu</a></li>
                    </ul>
                </li> --}}

                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-sliders"></i></span><span class="pcoded-mtext">Setting</span></a>
                    <ul class="pcoded-submenu">
                        {{-- <li><a href="{{ route('roles.index') }}">Role</a></li> --}}
                        <li><a href="{{ route('faq.index') }}">FAQ</a></li>
                        <li><a href="{{ route('infografis.index') }}">Infografis</a></li>
                        <li><a href="{{ route('galeri.index') }}">Galeri</a></li>
                        <li><a href="{{ route('berita.index') }}">Berita</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-users"></i></span><span class="pcoded-mtext">Users</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('admin.index') }}">Admin</a></li>
                        <li><a href="{{ route('userpencari.index') }}">Pencari Kerja</a></li>
                        <li><a href="{{ route('userpenyedia.index') }}">Pemberi Kerja</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-users"></i></span><span class="pcoded-mtext">Data</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('datapencari.index') }}">Pencari Kerja</a></li>
                        <li><a href="{{ route('datapenyedia.index') }}">Pemberi Kerja</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-user-minus"></i></span><span class="pcoded-mtext">Data
                            Unfinish</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('datapencariunfinish.index') }}">Pencari Kerja</a></li>
                        <li><a href="{{ route('datapenyediaunfinish.index') }}">Pemberi Kerja</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a href="{{ route('jobfair.index') }}" class="nav-link "><span
                            class="pcoded-micon"><i class="feather icon-briefcase"></i></span><span
                            class="pcoded-mtext">Job Fair</span></a></li>

                <li class="nav-item"><a href="{{ route('magang_dn.index') }}" class="nav-link "><span
                            class="pcoded-micon"><i class="feather icon-briefcase"></i></span><span
                            class="pcoded-mtext">Magang Pemerintah</span></a></li>

                {{-- <li class="nav-item"><a href="{{ route('lowongan.admin.index') }}" class="nav-link "><span
                            class="pcoded-micon"><i class="feather icon-briefcase"></i></span><span
                            class="pcoded-mtext">Lowongan</span></a></li> --}}

                @php
                    $pendingLowonganCount = 0;
                    if (auth()->check()) {
                        $query = \App\Models\Lowongan::whereNull('deleted_at')->where('status_id', 0)->whereIn('tipe_lowongan', [0, 3]);
                        
                        if (auth()->user()->hasRole('admin-kabkota')) {
                            $kabkotaId = auth()->user()->admin?->kabkota_id;
                            if ($kabkotaId) {
                                $query->where('kabkota_id', $kabkotaId);
                            }
                        }
                        
                        $pendingLowonganCount = $query->count();
                    }
                @endphp

                <li class="nav-item">
                    <a href="{{ route('lowongan.admin.index') }}" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-briefcase"></i></span>
                        <span class="pcoded-mtext">Lowongan</span>
                        @if($pendingLowonganCount > 0)
                            <span style="background-color: #dc3545; color: #fff; font-size: 10px; min-width: 18px; height: 18px; line-height: 18px; text-align: center; border-radius: 50%; margin-left: 8px; padding: 0 5px; display: inline-block;">
                                {{ $pendingLowonganCount > 99 ? '99+' : $pendingLowonganCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <li class="nav-item"><a href="{{ route('lowonganbkk.admin.index') }}" class="nav-link "><span
                            class="pcoded-micon"><i class="feather icon-briefcase"></i></span><span
                            class="pcoded-mtext">Lowongan BKK</span></a></li>

                <li class="nav-item"><a href="{{ route('penempatan.admin.index') }}" class="nav-link "><span
                            class="pcoded-micon"><i class="fa fa-archive"></i></span><span
                            class="pcoded-mtext">Penempatan</span></a></li>


                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-users"></i></span><span class="pcoded-mtext">Rekap</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('rekap.ak31') }}">IPK 3.1</a></li>
                        <li><a href="{{ route('rekap.ak32') }}">IPK 3.2</a></li>
                        <li><a href="{{ route('rekap.ak33') }}">IPK 3.3</a></li>
                        <li><a href="{{ route('rekap.ak34') }}">IPK 3.4</a></li>
                        <li><a href="{{ route('rekap.ak35') }}">IPK 3.5</a></li>
                        <li><a href="{{ route('rekap.ak36') }}">IPK 3.6</a></li>
                        <li><a href="{{ route('rekap.ak37') }}">IPK 3.7</a></li>
                        <li><a href="{{ route('rekap.ak38') }}">IPK 3.8</a></li>
                    </ul>
                </li>

                {{-- <li class="nav-item"><a href="{{ route('dashboard.pimpinan.index') }}" class="nav-link" --}}
                {{-- <li class="nav-item"><a href="{{ route('dashboard.eksekutif') }}" class="nav-link"
                        target="_blank"><span class="pcoded-micon"><i class="fa fa-chart-bar"></i></span><span
                            class="pcoded-mtext">Eksekutif</span></a></li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-users"></i></span><span class="pcoded-mtext">Eksekutif</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('') }}">KABUPATEN PASER</a></li>
                        
                    </ul>
                </li> --}}

                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-bar-chart-2"></i></span>
                        <span class="pcoded-mtext">Eksekutif</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('dashboard.eksekutif') }}">📊 Provinsi Kaltim</a></li>
                        <li class="nav-item pcoded-hasmenu">
                            <a href="#" class="nav-link">Per Kabupaten/Kota</a>
                            <ul class="pcoded-submenu">
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6401) }}">Kabupaten Paser</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6402) }}">Kabupaten Kutai Barat</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6403) }}">Kabupaten Kutai Kartanegara</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6404) }}">Kabupaten Kutai Timur</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6405) }}">Kabupaten Berau</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6409) }}">Kabupaten Penajam Paser Utara</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6411) }}">Kabupaten Mahakam Hulu</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6471) }}">Kota Balikpapan</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6472) }}">Kota Samarinda</a></li>
                                <li><a href="{{ route('dashboard.eksekutif.kabkota', 6474) }}">Kota Bontang</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

            </ul>

        </div>
    </div>
</nav>
