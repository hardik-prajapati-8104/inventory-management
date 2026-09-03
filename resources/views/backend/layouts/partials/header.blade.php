@php
    $u = Auth::guard('admin')->user();
    $notifications = \App\Services\NotificationService::items();
@endphp

<header class="vsp-header">
    <button type="button" class="vsp-header__toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="vsp-header__search d-none d-md-flex">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search parts, invoices, customers…" aria-label="Global search">
    </div>

    <div class="vsp-header__right">
        <div class="dropdown">
            <button class="vsp-header__icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                @if (count($notifications))
                    <span class="vsp-badge-dot"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end vsp-notif-menu">
                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                    Notifications
                    @if (count($notifications))<span class="badge bg-danger">{{ count($notifications) }}</span>@endif
                </h6>
                @forelse ($notifications as $n)
                    <a href="{{ $n['url'] }}" class="dropdown-item d-flex align-items-start gap-2 py-2">
                        <i class="bi bi-{{ $n['icon'] }} text-{{ $n['severity'] }} mt-1"></i>
                        <span class="small">{{ $n['message'] }}</span>
                    </a>
                @empty
                    <div class="dropdown-item-text text-muted small">All caught up — nothing needs attention right now.</div>
                @endforelse
            </div>
        </div>

        <div class="dropdown">
            <button class="vsp-header__profile" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="vsp-avatar">{{ strtoupper(substr($u->first_name ?? 'A', 0, 1)) }}</span>
                <span class="d-none d-lg-inline">{{ $u->name ?? 'Admin' }}</span>
                <i class="bi bi-chevron-down small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.change-password') }}"><i class="bi bi-key me-2"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
