<nav class="pcoded-navbar menu-light ">
    <div class="navbar-wrapper  ">
        <div class="navbar-content scroll-div ">

            <div class="">
                <div class="main-menu-header">
                    {{-- @php
                        $cekfoto = getRowPenyediaById(Auth::user()->id)->foto;
                        $xfoto = asset('storage/' . getRowPenyediaById(Auth::user()->id)->foto);
                    @endphp --}}
                    @php
                        $cekfoto = getRowPenyediaById(Auth::user()->id)->foto ?? null;
                        $xfoto = isset(getRowPenyediaById(Auth::user()->id)->foto)
                            ? asset('storage/' . getRowPenyediaById(Auth::user()->id)->foto)
                            : asset('path/ke/gambar/default.jpg');
                    @endphp
                    @if ($cekfoto != null)
                        <img class="img-radius" src="{{ $xfoto }}" alt="User-Profile-Image" width="300px">
                    @else
                        <img class="img-radius" src="{{ asset('assets/etam_be/images/user/avatar-x.png') }}"
                            alt="User-Profile-Image">
                    @endif

                    <div class="user-details">
                        {{-- <div id="more-details"> {{ Auth::user()->name }} <i class="fa fa-caret-down"></i></div> --}}
                        <div id="more-details"> {{ Auth::user()->name }} </div>
                    </div>
                </div>
                {{-- <div class="collapse" id="nav-user-link" hidden>
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

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                        <span class="pcoded-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('lowongan.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-briefcase"></i></span>
                        <span class="pcoded-mtext">Lowongan Kerja</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pencari_diterima.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                        <span class="pcoded-mtext">Pencari Diterima</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('bkk.penyedia.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="fa fa-building"></i></span>
                        <span class="pcoded-mtext">BKK</span>
                    </a>
                </li>

                <!-- Layouts -->
                {{-- <li class="menu-item open">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-table"></i>
                        <div data-i18n="Layouts">Lowongan Kerja</div>
                    </a>

                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <div data-i18n="Without menu">Lowongan Kerja</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <div data-i18n="Without navbar">History Loker</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <div data-i18n="Without navbar">Penempatan</div>
                            </a>
                        </li>


                    </ul>
                </li> --}}

        </div>
    </div>
</nav>
