<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from ableproadmin.com/bootstrap/default/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 05 Sep 2022 02:50:40 GMT -->

<head>
    <title>ETAM KERJA DASHBOARD</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Phoenixcoded" />
    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('assets/etam_be/images/logo/icon_etam.png') }}" type="image/x-icon">
    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/etam_be/css/style.css') }}">

    <!-- data tables css -->
    <link rel="stylesheet" href="{{ asset('assets/etam_be/css/plugins/dataTables.bootstrap4.min.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />


    <!-- Summernote CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.css" rel="stylesheet">

   
</head>

<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<!-- [ Header ] start -->
<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        <a href="#!" class="b-brand">
            <!-- ========   change your logo hear   ============ -->
            <img src="{{ asset('assets') }}/etam_be/images/logo/logo_etam_white.png" alt="" class="logo"
                width="100px">
        </a>
        <a href="#!" class="mob-toggler">
            <i class="feather icon-more-vertical"></i>
        </a>
    </div>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <!-- <a href="#!" class="pop-search"><i class="feather icon-search"></i></a> -->
                <div class="search-bar">
                    <!-- <input type="text" class="form-control border-0 shadow-none" placeholder="Search hear"> -->
                    <button type="button" class="close close btn-close position-absolute top-50 end-0 translate-middle"
                        aria-label="Close">
                    </button>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto" >
            {{-- <li hidden>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown"><i
                            class="icon feather icon-bell"></i></a>
                    <div class="dropdown-menu dropdown-menu-end notification">
                        <div class="noti-head">
                            <h6 class="d-inline-block m-b-0">Notifications</h6>
                            <div class="float-end">
                                <a href="#!" class="m-r-10">mark as read</a>
                                <a href="#!">clear all</a>
                            </div>
                        </div>
                        <ul class="noti-body">
                            <li class="n-title">
                                <p class="m-b-0">NEW</p>
                            </li>
                            <li class="notification">
                                <div class="d-flex">
                                    <img class="img-radius" src="{{ asset('assets') }}/etam_be/images/user/avatar-1.jpg"
                                        alt="Generic placeholder image">
                                    <div class="flex-grow-1">
                                        <p><strong>John Doe</strong><span class="n-time text-muted"><i
                                                    class="icon feather icon-clock m-r-10"></i>5 min</span></p>
                                        <p>New ticket Added</p>
                                    </div>
                                </div>
                            </li>
                            <li class="n-title">
                                <p class="m-b-0">EARLIER</p>
                            </li>
                            <li class="notification">
                                <div class="d-flex">
                                    <img class="img-radius" src="{{ asset('assets') }}/etam_be/images/user/avatar-2.jpg"
                                        alt="Generic placeholder image">
                                    <div class="flex-grow-1">
                                        <p><strong>Joseph William</strong><span class="n-time text-muted"><i
                                                    class="icon feather icon-clock m-r-10"></i>10 min</span></p>
                                        <p>Prchace New Theme and make payment</p>
                                    </div>
                                </div>
                            </li>
                            <li class="notification">
                                <div class="d-flex">
                                    <img class="img-radius" src="{{ asset('assets/etam_be/images/user/avatar-1.jpg') }}"
                                        alt="Generic placeholder image">
                                    <div class="flex-grow-1">
                                        <p><strong>Sara Soudein</strong><span class="n-time text-muted"><i
                                                    class="icon feather icon-clock m-r-10"></i>12 min</span></p>
                                        <p>currently login</p>
                                    </div>
                                </div>
                            </li>
                            <li class="notification">
                                <div class="d-flex">
                                    <img class="img-radius"
                                        src="{{ asset('assets/etam_be/images/user/avatar-2.jpg') }}"
                                        alt="Generic placeholder image">
                                    <div class="flex-grow-1">
                                        <p><strong>Joseph William</strong><span class="n-time text-muted"><i
                                                    class="icon feather icon-clock m-r-10"></i>30 min</span></p>
                                        <p>Prchace New Theme and make payment</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="noti-footer">
                            <a href="{{ route('notifications.index') }}">lihat semua</a>
                        </div>
                    </div>
                </div>
            </li> --}}

            <li>
                <div class="dropdown" >
                    <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown" id="notificationDropdown" style="position: relative;">
                        <span class="badge bg-danger notification-badge" id="notifCount">0</span>
                        <i class="icon feather icon-bell"></i>
                        
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification" style="width: 350px;">
                        <div class="noti-head">
                            <h6 class="d-inline-block m-b-0">Notifikasi</h6>
                            <div class="float-end">
                                <a href="#!" class="m-r-10" id="markAllRead">tandai dibaca</a>
                            </div>
                        </div>
                        <ul class="noti-body" id="notificationList" style="max-height: 300px; overflow-y: auto;">
                            <li class="notification text-center">
                                <p class="m-b-0 text-muted">Memuat notifikasi...</p>
                            </li>
                        </ul>
                        <div class="noti-footer">
                            <a href="{{ route('notifications.index') ?? '#' }}" id="showAllNotif" target="BLANK">lihat semua</a>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="feather icon-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-notification">
                        <div class="pro-head">
                            @if (Auth::user()->roles[0]['name'] == 'super-admin')
                                <img src="{{ asset('assets/etam_be/images/user/avatar-1.jpg') }}" class="img-radius"
                                    alt="User-Profile-Image">
                            @elseif (Auth::user()->roles[0]['name'] == 'pencari-kerja')
                                @php
                                    $cekft = getRowPencariById(Auth::user()->id)->foto;
                                    $xfoto = asset('storage/' . getRowPencariById(Auth::user()->id)->foto);
                                @endphp
                                @if ($cekft != null)
                                    <img src="{{ $xfoto }}" class="img-radius" alt="User-Profile-Image">
                                @else
                                    <img src="{{ asset('assets/etam_be/images/user/avatar-1.jpg') }}"
                                        class="img-radius" alt="User-Profile-Image">
                                @endif
                            @elseif (Auth::user()->roles[0]['name'] == 'penyedia-kerja')
                                {{-- @php
                                    $cekft = getRowPenyediaById(Auth::user()->id)->foto;
                                    $xfoto = asset('storage/' . getRowPenyediaById(Auth::user()->id)->foto);
                                @endphp --}}
                                @php
                                    $cekft = getRowPenyediaById(Auth::user()->id)->foto ?? null;
                                    $xfoto = isset(getRowPenyediaById(Auth::user()->id)->foto)
                                        ? asset('storage/' . getRowPenyediaById(Auth::user()->id)->foto)
                                        : asset('path/ke/gambar/default.jpg');
                                @endphp
                                @if ($cekft != null)
                                    <img src="{{ $xfoto }}" class="img-radius" alt="User-Profile-Image">
                                @else
                                    <img src="{{ asset('assets/etam_be/images/user/avatar-1.jpg') }}"
                                        class="img-radius" alt="User-Profile-Image">
                                @endif
                            @elseif (Auth::user()->roles[0]['name'] == 'admin-bkk')
                                @php
                                    $cekft = getRowBkkById(Auth::user()->id)->foto;
                                    $xfoto = asset('storage/' . getRowBkkById(Auth::user()->id)->foto);
                                @endphp
                                @if ($cekft != null)
                                    <img src="{{ $xfoto }}" class="img-radius" alt="User-Profile-Image">
                                @else
                                    <img src="{{ asset('assets/etam_be/images/user/avatar-1.jpg') }}"
                                        class="img-radius" alt="User-Profile-Image">
                                @endif
                            @endif


                            <span>{{ Auth::user()->name }}</span>
                            <a href="auth-signin.html" class="dud-logout" title="Logout">
                                <i class="feather icon-log-out"></i>
                            </a>
                        </div>
                        <ul class="pro-body">
                            <li>
                                @if (Auth::user()->roles[0]['name'] == 'super-admin')
                                    <a href="#" class="dropdown-item"><i class="feather icon-user"></i>
                                        Profil</a>
                                @elseif (Auth::user()->roles[0]['name'] == 'pencari-kerja')
                                    <a href="{{ route('profil.pencari.index') }}" class="dropdown-item"><i
                                            class="feather icon-user"></i>
                                        Profil</a>
                                @elseif (Auth::user()->roles[0]['name'] == 'penyedia-kerja')
                                    <a href="{{ route('profil.penyedia.index') }}" class="dropdown-item"><i
                                            class="feather icon-user"></i>
                                        Profil</a>
                                @elseif (Auth::user()->roles[0]['name'] == 'admin-bkk')
                                    <a href="{{ route('profil.bkk.index') }}" class="dropdown-item"><i
                                            class="feather icon-user"></i>
                                        Profil</a>
                                @endif
                            </li>
                            <li>
                                <a href="#" onclick="showUbahPassword()" class="dropdown-item"><i
                                        class="feather icon-unlock"></i>
                                    Ubah Password</a>
                            </li>
                            <!-- <li><a href="email_inbox.html" class="dropdown-item"><i class="feather icon-mail"></i> My Messages</a></li> -->
                            <li><a href="{{ route('logout') }}" class="dropdown-item"><i
                                        class="feather icon-lock"></i>
                                    Logout</a></li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>

<!-- [ Header ] end -->



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load notifikasi saat halaman ready
    loadNotifications();

    // Refresh setiap 30 detik
    setInterval(loadNotifications, 1800000);

    // Mark all as read
    document.getElementById('markAllRead')?.addEventListener('click', function(e) {
        e.preventDefault();
        markAllAsRead();
    });
});

function loadNotifications() {
    fetch('{{ route("notifications.get") }}')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.unread_count);
            renderNotifications(data.notifications);
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notifCount');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function renderNotifications(notifications) {
    const list = document.getElementById('notificationList');
    
    if (notifications.length === 0) {
        list.innerHTML = `
            <li class="notification text-center py-3">
                <p class="m-b-0 text-muted">Tidak ada notifikasi</p>
            </li>
        `;
        return;
    }

    let html = '';
    notifications.forEach(notif => {
        const isUnread = notif.is_open == 0;
        const bgClass = isUnread ? 'background-color: #f0f7ff;' : '';
        const timeAgo = formatTimeAgo(notif.created_at);
        
        html += `
            <li class="notification" style="${bgClass} cursor: pointer; border-bottom: 1px solid #eee;" 
                onclick="openNotification(${notif.id}, '${notif.url_redirection || '#'}')">
                <div class="d-flex p-2">
                    <div class="flex-shrink-0">
                        <i class="feather icon-bell text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="m-b-0" style="font-size: 13px; ${isUnread ? 'font-weight: 600;' : ''}">
                            ${truncateText(notif.info, 80)}
                        </p>
                        <small class="text-muted">
                            <i class="feather icon-clock"></i> ${timeAgo}
                        </small>
                        ${isUnread ? '<span class="badge bg-primary ms-2" style="font-size: 9px;">Baru</span>' : ''}
                    </div>
                </div>
            </li>
        `;
    });

    list.innerHTML = html;
}

function openNotification(id, url) {
    // Mark as read
    fetch(`/notifications/read/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => {
        // Redirect ke URL tujuan
        if (url && url !== '#' && url !== 'null') {
            window.location.href = url;
        } else {
            // Refresh notifikasi jika tidak ada URL
            loadNotifications();
        }
    });
}

function markAllAsRead() {
    fetch('{{ route("notifications.readAll") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => {
        loadNotifications();
    });
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Baru saja';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' menit lalu';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' jam lalu';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' hari lalu';
    
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}
</script>
