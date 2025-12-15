@extends('backend.template.backend-notif')

@section('content')
<body class="box-layout container background-green">
<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Page header -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Notifikasi</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="javascript:">Notifikasi</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page body -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>Semua Notifikasi</h5>
                                <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
                                    <i class="feather icon-check"></i> Tandai Semua Dibaca
                                </button>
                            </div>
                            <div class="card-body p-0">
                                @if($notifications->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <tbody>
                                                @foreach($notifications as $notif)
                                                <tr class="{{ $notif->is_open == 0 ? 'table-primary' : '' }}" id="notif-row-{{ $notif->id }}">
                                                    <td style="width: 50px;">
                                                        <i class="feather icon-bell {{ $notif->is_open == 0 ? 'text-primary' : 'text-muted' }}" style="font-size: 20px;"></i>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="{{ $notif->is_open == 0 ? 'fw-bold' : '' }}">
                                                                {{ $notif->info }}
                                                            </span>
                                                            <small class="text-muted mt-1">
                                                                <i class="feather icon-clock"></i> 
                                                                {{ $notif->created_at->diffForHumans() }}
                                                                @if($notif->is_open == 0)
                                                                    <span class="badge bg-primary ms-2">Baru</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td class="text-end" style="width: 150px;">
                                                        @if($notif->url_redirection)
                                                            <a href="javascript:void(0)" 
                                                               class="btn btn-sm btn-outline-info me-1"
                                                               onclick="openNotifLink({{ $notif->id }}, '{{ $notif->url_redirection }}')">
                                                                <i class="feather icon-external-link"></i>
                                                            </a>
                                                        @endif
                                                        @if($notif->is_open == 0)
                                                            <button class="btn btn-sm btn-outline-success me-1" 
                                                                    onclick="markAsRead({{ $notif->id }})"
                                                                    title="Tandai dibaca">
                                                                <i class="feather icon-check"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteNotification({{ $notif->id }})"
                                                                title="Hapus">
                                                            <i class="feather icon-trash-2"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="card-footer">
                                        {{ $notifications->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="feather icon-bell-off text-muted" style="font-size: 50px;"></i>
                                        <p class="text-muted mt-3">Tidak ada notifikasi</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/notifications/read/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(`notif-row-${id}`);
            row.classList.remove('table-primary');
            const boldEl = row.querySelector('.fw-bold');
            if (boldEl) boldEl.classList.remove('fw-bold');
            const badgeEl = row.querySelector('.badge');
            if (badgeEl) badgeEl.remove();
            const checkBtn = row.querySelector('.btn-outline-success');
            if (checkBtn) checkBtn.remove();
        }
    }).catch(err => {
        console.error('Error:', err);
        alert('Gagal menandai notifikasi');
    });
}

function openNotifLink(id, url) {
    // Mark as read dulu, baru redirect
    fetch(`/notifications/read/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(() => {
        window.location.href = url;
    }).catch(() => {
        // Tetap redirect meski gagal mark as read
        window.location.href = url;
    });
}

function deleteNotification(id) {
    if (!confirm('Hapus notifikasi ini?')) return;

    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`notif-row-${id}`).remove();
        }
    }).catch(err => {
        console.error('Error:', err);
        alert('Gagal menghapus notifikasi');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('markAllReadBtn').addEventListener('click', function() {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            location.reload();
        }).catch(err => {
            console.error('Error:', err);
            alert('Gagal menandai semua notifikasi');
        });
    });
});
</script>
@endsection