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
                    <a href="{{ route('dashboard') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                        <span class="pcoded-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('hi.pp.admbidang.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-book"></i></span>
                        <span class="pcoded-mtext">Ajuan PP</span>
                    </a>
                </li>
                {{-- @php
                    $blkProfile = getRowBlkById(Auth::id());
                @endphp
                @if ($blkProfile && in_array((int) $blkProfile->tipe_akun, [0, 1, 2, 3], true))
                    <li class="nav-item">
                        <a href="{{ route('blk.users.index') }}" class="nav-link ">
                            <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                            <span class="pcoded-mtext">User BLK</span>
                        </a>
                    </li>
                @endif --}}
            </ul>
        </div>
    </div>
</nav>
