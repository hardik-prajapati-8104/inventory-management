@extends('public.layouts.app')

@section('title', 'Spare Parts Catalogue')
@section('meta_description', 'Browse our full range of vehicle spare parts by category, brand, or vehicle compatibility.')

@section('content')

<div class="pub-hero">
    <div class="container text-center">
        <h2 class="fw-bold mb-2">Find the Right Part</h2>
        <p class="mb-0 opacity-75">Search by part name, OEM number, or the make/model of your vehicle.</p>
    </div>
</div>

<div class="container pb-5">

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <form method="GET">
                @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                <label class="small fw-medium mb-1">Category</label>
                <select name="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="col-md-3">
            <form method="GET">
                @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                @if (request('category_id'))<input type="hidden" name="category_id" value="{{ request('category_id') }}">@endif
                <label class="small fw-medium mb-1">Brand</label>
                <select name="brand_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-md-end">
            <span class="text-muted small">{{ $parts->total() }} part(s) found</span>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($parts as $part)
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('catalogue.show', $part->slug) }}" class="text-decoration-none">
                <div class="part-card">
                    <div class="part-card__image">
                        @if ($part->main_image)
                            <img src="{{ asset('storage/'.$part->main_image) }}" alt="{{ $part->name }}">
                        @else
                            <i class="bi bi-gear-wide-connected"></i>
                        @endif
                    </div>
                    <div class="part-card__body">
                        <div class="small text-muted">{{ $part->brand->name ?? $part->category->name ?? '' }}</div>
                        <div class="fw-medium text-dark">{{ $part->name }}</div>
                        <div class="part-card__price mt-1">₹{{ number_format($part->retail_price, 2) }}</div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-search fs-1 d-block mb-2"></i>
            No parts found. Try a different search or filter.
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $parts->links() }}</div>

</div>

@endsection
