<nav class="pcoded-navbar menu-light ">
    <div class="navbar-wrapper  ">
        <div class="navbar-content scroll-div " >
            
            <div class="">
                <div class="main-menu-header">
                    <img class="img-radius" src="{{ asset('admin/assets/images/user/avatar-2.jpg') }}" alt="User-Profile-Image">
                    <div class="user-details">
                        <div id="more-details">Admin Provinsi <i class="fa fa-caret-down"></i></div>
                    </div>
                </div>
                <div class="collapse" id="nav-user-link">
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="user-profile.html" data-toggle="tooltip" title="View Profile"><i class="feather icon-user"></i></a></li>
                        <li class="list-inline-item"><a href="email_inbox.html"><i class="feather icon-mail" data-toggle="tooltip" title="Messages"></i><small class="badge badge-pill badge-primary">5</small></a></li>
                        <li class="list-inline-item"><a href="auth-signin.html" data-toggle="tooltip" title="Logout" class="text-danger"><i class="feather icon-power"></i></a></li>
                    </ul>
                </div>
            </div>
            
            <ul class="nav pcoded-inner-navbar ">
                <li class="nav-item pcoded-menu-caption">
                    <label>Navigation</label>
                </li>
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link "><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a></li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="{{ route('setting.banner') }}" class="nav-link "><span class="pcoded-micon"><i class="feather icon-sliders"></i></span><span class="pcoded-mtext">Setting</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('setting.banner') }}">Banner</a></li>
                        <li><a href="dashboard-sale.html">Infografis</a></li>
                        <li><a href="dashboard-crm.html">Galeri</a></li>
                        <li><a href="dashboard-analytics.html">Berita</a></li>
                        <li><a href="dashboard-analytics.html">FAQ</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-users"></i></span><span class="pcoded-mtext">User</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="user-profile.html">Admin</a></li>
                        <li><a href="user-profile.html">Admin Kabupaten/Kota</a></li>
                        <li><a href="user-profile.html">Officer Kabupaten/Kota</a></li>
                        <li><a href="user-profile.html">Perusahaan</a></li>
                        <li><a href="user-profile.html">Pencari Kerja</a></li>
                        <li><a href="user-profile.html">BKK</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-layers"></i></span><span class="pcoded-mtext">Lowongan Kerja</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="user-profile.html">Lowongan Kerja</a></li>
                        <li><a href="user-profile.html">History Lowongan Kerja </a></li>
                        <li><a href="user-profile.html">Penempatan</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-book"></i></span><span class="pcoded-mtext">AK1</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="user-profile.html">Cetak AK1</a></li>
                        <li><a href="user-profile.html">Data AK1 </a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-activity"></i></span><span class="pcoded-mtext">Rekap</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="user-profile.html">Pencari Kerja</a></li>
                        <li><a href="user-profile.html">Perusahaan </a></li>
                        <li><a href="user-profile.html">Lowongan Kerja</a></li>
                        <li><a href="user-profile.html">Penempatan</a></li>
                        <li><a href="user-profile.html">BKK</a></li>
                    </ul>
                </li>

            </ul>
            
            <div class="card text-center">
                <div class="card-block">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-hidden="true"></button>
                    <i class="feather icon-sunset f-40"></i>
                    <h6 class="mt-3">Help?</h6>
                    <p>Butuh bantuan, klik link dibawah untuk mendapatkan bantuan</p>
                    <a href="#!" target="_blank" class="btn btn-primary btn-sm text-white m-0">Bantuan</a>
                </div>
            </div>
            
        </div>
    </div>
</nav>