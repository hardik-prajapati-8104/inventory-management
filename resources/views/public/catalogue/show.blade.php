@extends('public.layouts.app')

@php
    $metaDescription = $part->seo_description ?: $part->short_description ?: mb_strimwidth(strip_tags($part->description ?? ''), 0, 155, '…');
@endphp

@section('title', $part->seo_title ?: $part->name)
@section('meta_description', $metaDescription)

@section('content')

<div class="container py-4">

    <nav class="mb-3">
        <a href="{{ route('catalogue.index') }}" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left"></i> Back to Catalogue</a>
    </nav>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="part-card">
                <div class="part-card__image" style="aspect-ratio: 1/1;">
                    @if ($part->main_image)
                        <img src="{{ asset('storage/'.$part->main_image) }}" alt="{{ $part->name }}">
                    @else
                        <i class="bi bi-gear-wide-connected" style="font-size: 4rem;"></i>
                    @endif
                </div>
            </div>
            @if ($part->images->count())
            <div class="d-flex gap-2 mt-2 flex-wrap">
                @foreach ($part->images as $img)
                    <img src="{{ asset('storage/'.$img->path) }}" width="70" height="70" class="rounded border" style="object-fit:cover;">
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-md-7">
            <div class="small text-muted mb-1">{{ $part->category->name ?? '' }} @if($part->brand) &middot; {{ $part->brand->name }} @endif</div>
            <h2 class="fw-bold mb-2">{{ $part->name }}</h2>
            <div class="small text-muted mb-3">Part No. {{ $part->part_number }} @if($part->oem_number) &middot; OEM {{ $part->oem_number }} @endif</div>

            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="fs-3 fw-bold" style="color: var(--vsp-primary-darker);">₹{{ number_format($part->retail_price, 2) }}</span>
                @if ($part->current_stock > 0)
                    <span class="badge bg-success">In Stock</span>
                @else
                    <span class="badge bg-secondary">Currently Unavailable</span>
                @endif
            </div>

            @if ($part->short_description)
                <p class="text-muted">{{ $part->short_description }}</p>
            @endif

            @if ($part->description)
                <div class="mb-3">{!! nl2br(e($part->description)) !!}</div>
            @endif

            @if ($part->vehicles->count())
            <div class="mb-3">
                <h6 class="small text-uppercase text-muted mb-2">Compatible Vehicles</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach ($part->vehicles as $v)
                        <span class="badge bg-light text-dark border">{{ $v->label }}{{ $v->pivot->position !== 'Universal' ? ' — '.$v->pivot->position : '' }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <a href="tel:" class="btn btn-add text-white"><i class="bi bi-telephone"></i> Enquire About This Part</a>
        </div>
    </div>

    @if ($related->count())
    <hr class="my-5">
    <h5 class="mb-3">Related Parts</h5>
    <div class="row g-3">
        @foreach ($related as $r)
        <div class="col-6 col-md-3">
            <a href="{{ route('catalogue.show', $r->slug) }}" class="text-decoration-none">
                <div class="part-card">
                    <div class="part-card__image">
                        @if ($r->main_image)
                            <img src="{{ asset('storage/'.$r->main_image) }}" alt="{{ $r->name }}">
                        @else
                            <i class="bi bi-gear-wide-connected"></i>
                        @endif
                    </div>
                    <div class="part-card__body">
                        <div class="fw-medium text-dark">{{ $r->name }}</div>
                        <div class="part-card__price mt-1">₹{{ number_format($r->retail_price, 2) }}</div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @endif

</div>

@endsection
