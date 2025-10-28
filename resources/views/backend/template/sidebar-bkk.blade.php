<nav class="pcoded-navbar menu-light ">
    <div class="navbar-wrapper  ">
        <div class="navbar-content scroll-div ">

            <div class="">
                <div class="main-menu-header">
                    @php
                        $cekfoto = getRowBkkById(Auth::user()->id)->foto;
                        $xfoto = asset('storage/' . getRowBkkById(Auth::user()->id)->foto);
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
                    <a href="{{ route('alumni.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                        <span class="pcoded-mtext">Alumni</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('perusahaan.index') }}" class="nav-link ">
                        <span class="pcoded-micon"><i class="feather icon-tablet"></i></span>
                        <span class="pcoded-mtext">Perusahaan</span>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</nav>
