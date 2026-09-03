<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vehicle Spare Parts Inventory')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ url('public/backend/css/custom.css') }}">

    @yield('styles')
</head>
<body>

<div class="vsp-wrapper">

    <aside class="vsp-sidebar" id="vspSidebar">
        @include('backend.layouts.partials.sidebar')
    </aside>

    <div class="vsp-main">

        @include('backend.layouts.partials.header')

        <main class="vsp-content">
            @include('backend.layouts.partials.messages')
            @yield('admin-content')
        </main>

        <footer class="vsp-footer">
            <span>&copy; {{ date('Y') }} Vehicle Spare Parts Inventory Software</span>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        document.getElementById('vspSidebar').classList.toggle('vsp-sidebar--collapsed');
        document.querySelector('.vsp-main').classList.toggle('vsp-main--expanded');
    });
</script>

@yield('scripts')
</body>
</html>
