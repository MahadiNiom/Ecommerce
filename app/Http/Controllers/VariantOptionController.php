<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVariantOptionRequest;
use App\Http\Requests\UpdateVariantOptionRequest;
use App\Models\Variant;
use App\Models\VariantOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VariantOptionController extends Controller
{
    public function index(Variant $variant): View
    {
        $variantOptions = $variant->variantOptions()->get();

        return view('variant-options.index', compact('variant', 'variantOptions'));
    }

    public function create(Variant $variant): View
    {
        return view('variant-options.create', ['variant' => $variant, 'variantOption' => new VariantOption]);
    }

    public function store(StoreVariantOptionRequest $request, Variant $variant): RedirectResponse
    {
        $variant->variantOptions()->create($request->validated());

        return redirect()->route('variants.variant-options.index', $variant)->with('success', 'Variant option created successfully.');
    }

    public function show(Variant $variant, VariantOption $variantOption): View
    {
        return view('variant-options.show', compact('variant', 'variantOption'));
    }

    public function edit(Variant $variant, VariantOption $variantOption): View
    {
        return view('variant-options.edit', compact('variant', 'variantOption'));
    }

    public function update(UpdateVariantOptionRequest $request, Variant $variant, VariantOption $variantOption): RedirectResponse
    {
        $variantOption->update($request->validated());

        return redirect()->route('variants.variant-options.index', $variant)->with('success', 'Variant option updated successfully.');
    }

    public function destroy(Variant $variant, VariantOption $variantOption): RedirectResponse
    {
        $variantOption->delete();

        return redirect()->route('variants.variant-options.index', $variant)->with('success', 'Variant option deleted successfully.');
    }
}
