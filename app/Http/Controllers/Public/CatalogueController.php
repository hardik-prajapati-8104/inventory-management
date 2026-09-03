<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SparePart;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $query = SparePart::published()->with(['category', 'brand', 'unit']);

        if ($term = $request->get('q')) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                    ->orWhere('part_number', 'like', "%$term%")
                    ->orWhere('oem_number', 'like', "%$term%")
                    ->orWhereHas('vehicles.model_.make', fn ($vq) => $vq->where('name', 'like', "%$term%"))
                    ->orWhereHas('vehicles.model_', fn ($vq) => $vq->where('name', 'like', "%$term%"));
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId = $request->get('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        $parts = $query->orderBy('name')->paginate(24)->withQueryString();

        $categories = Category::topLevel()->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('public.catalogue.index', compact('parts', 'categories', 'brands'));
    }

    public function show(string $slug)
    {
        $part = SparePart::published()
            ->with(['category', 'brand', 'manufacturer', 'unit', 'images', 'vehicles.model_.make'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = SparePart::published()
            ->where('category_id', $part->category_id)
            ->where('id', '!=', $part->id)
            ->limit(4)
            ->get();

        return view('public.catalogue.show', compact('part', 'related'));
    }
}
