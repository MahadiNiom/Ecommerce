<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::withCount('products')->orderBy('name')->get();

        return view('brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('brands.create', ['brand' => new Brand]);
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        Brand::create($request->validated());

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $brand->update($request->validated());

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
}
