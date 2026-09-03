@extends('backend.layouts.master')

@section('title')
Notifications - Vehicle Spare Parts Inventory
@endsection

@section('admin-content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="page-title mb-1">Notifications</h4>
            <ul class="breadcrumbs">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><span>Notifications</span></li>
            </ul>
        </div>
        @if (Auth::guard('admin')->user()->isSuperAdmin())
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <form action="{{ route('admin.notifications.send-digest-now') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-envelope"></i> Send Test Digest Now</button>
            </form>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body">
        @forelse ($notifications as $n)
            <a href="{{ $n['url'] }}" class="d-flex align-items-start gap-3 py-3 text-decoration-none {{ ! $loop->last ? 'border-bottom' : '' }}">
                <div class="vsp-kpi__icon flex-shrink-0" style="background: var(--vsp-primary-lighter);">
                    <i class="bi bi-{{ $n['icon'] }} text-{{ $n['severity'] }}"></i>
                </div>
                <div>
                    <div class="text-dark">{{ $n['message'] }}</div>
                    <div class="small text-muted text-capitalize">{{ $n['severity'] }}</div>
                </div>
            </a>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                All caught up — nothing needs your attention right now.
            </div>
        @endforelse
    </div>
</div>

@endsection
