<nav class="pcoded-navbar menu-light ">
    <div class="navbar-wrapper  ">
        <div class="navbar-content scroll-div ">
            <div class="">
                <div class="main-menu-header">
                    <img class="img-radius" src="{{ asset('assets/etam_be/images/user/avatar-x.png') }}"
                        alt="User-Profile-Image">
                    <div class="user-details">
                        <div id="more-details"> {{ Auth::user()->name }}</div>
                    </div>
                </div>
            </div>

            <ul class="nav pcoded-inner-navbar ">
                <li class="nav-item pcoded-menu-caption">
                    <label>Navigation</label>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard.eksekutif') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                        <span class="pcoded-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengawasan.k3.kadis.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-book"></i></span>
                        <span class="pcoded-mtext">K3 Pengawas</span>
                    </a>
                </li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-edit-3"></i></span>
                        <span class="pcoded-mtext">TTE</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('blk.ttd.menunggu') }}">Menunggu TTD</a></li>
                        <li><a href="{{ route('blk.ttd.sudah') }}">Sudah ditandatangani</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
