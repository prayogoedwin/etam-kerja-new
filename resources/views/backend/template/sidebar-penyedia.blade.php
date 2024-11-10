<nav class="pcoded-navbar menu-light ">
    <div class="navbar-wrapper  ">
        <div class="navbar-content scroll-div " >
            
            <div class="">
                <div class="main-menu-header">
                    <img class="img-radius" src="{{ asset('assets/etam_be/images/user/php -x.png') }}" alt="User-Profile-Image">
                    <div class="user-details">
                        <div id="more-details"> {{ Auth::user()->name }} <i class="fa fa-caret-down"></i></div>
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
                
                <!-- Dashboard -->
            <li class="menu-item active">
                <a href="index.html" class="menu-link">
                  <i class="menu-icon tf-icons bx bx-home-circle"></i>
                  <div data-i18n="Analytics">Dashboard</div>
                </a>
              </li>
            
  
               <!-- Layouts -->
               <li class="menu-item open">
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
              </li>
            
        </div>
    </div>
</nav>