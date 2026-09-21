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
                    <a href="{{ route('blk.pelatihan.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-book"></i></span>
                        <span class="pcoded-mtext">Pelatihan BLK</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('blk.form.index', 'wawancara') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-message-square"></i></span>
                        <span class="pcoded-mtext">Wawancara</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('blk.form.index', 'pretest') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-edit"></i></span>
                        <span class="pcoded-mtext">Pretest</span>
                    </a>
                </li>
                @php
                    $roleName = Auth::user()->roles[0]['name'] ?? null;
                    $blkProfile = getRowBlkById(Auth::id());
                    $canSeeUserBlk = $roleName === 'admin-balai'
                        || ($blkProfile && in_array((int) $blkProfile->tipe_akun, [0, 1, 2, 3], true));
                @endphp
                @if ($canSeeUserBlk)
                    <li class="nav-item">
                        <a href="{{ route('blk.users.index') }}" class="nav-link ">
                            <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                            <span class="pcoded-mtext">{{ $roleName === 'admin-balai' ? 'Kelola User BLK' : 'User BLK' }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
