<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Parts Catalogue')</title>
    <meta name="description" content="@yield('meta_description', 'Browse our full range of vehicle spare parts.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">

    <style>
        body { background: var(--vsp-bg); }
        .pub-navbar { background: #fff; border-bottom: 1px solid var(--vsp-border); padding: 1rem 0; }
        .pub-navbar a.brand { font-weight: 700; font-size: 1.2rem; color: var(--vsp-text); text-decoration: none; display: flex; align-items: center; gap: .5rem; }
        .pub-navbar a.brand i { color: var(--vsp-primary); font-size: 1.5rem; }
        .pub-footer { padding: 2rem 0; text-align: center; color: var(--vsp-text-muted); font-size: .85rem; margin-top: 3rem; border-top: 1px solid var(--vsp-border); }
        .pub-hero { background: linear-gradient(135deg, var(--vsp-primary-darker), var(--vsp-primary)); color: #fff; padding: 3rem 0; margin-bottom: 2rem; }
        .part-card { border: 1px solid var(--vsp-border); border-radius: var(--vsp-radius); background: #fff; overflow: hidden; height: 100%; transition: box-shadow .15s; }
        .part-card:hover { box-shadow: 0 8px 24px rgba(20,15,5,.08); }
        .part-card__image { aspect-ratio: 4/3; background: var(--vsp-bg); display: flex; align-items: center; justify-content: center; color: var(--vsp-text-muted); font-size: 2rem; }
        .part-card__image img { width: 100%; height: 100%; object-fit: cover; }
        .part-card__body { padding: 1rem; }
        .part-card__price { color: var(--vsp-primary-darker); font-weight: 700; }
    </style>

    @yield('styles')
</head>
<body>

<nav class="pub-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="{{ route('catalogue.index') }}" class="brand"><i class="bi bi-boxes"></i> VSP Spare Parts</a>
        <form action="{{ route('catalogue.index') }}" method="GET" class="d-flex" style="max-width:420px; flex:1;">
            <input type="text" name="q" class="form-control me-2" placeholder="Search parts, OEM number, vehicle…" value="{{ request('q') }}">
            <button type="submit" class="btn btn-add text-white"><i class="bi bi-search"></i></button>
        </form>
    </div>
</nav>

@yield('content')

<footer class="pub-footer">
    <div class="container">
        &copy; {{ date('Y') }} Vehicle Spare Parts Inventory Software — Parts Catalogue
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
