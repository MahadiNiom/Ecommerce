@extends('base')

@section('title', 'Edit Product Variant - ' . config('app.name'))

@section('content')
    <a href="{{ route('products.product-variants.show', [$product, $productVariant]) }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to product variant
    </a>

    <div class="mt-6 max-w-2xl animate-fade-in-up">
        <h1 class="page-title">Edit product variant</h1>
        <div class="card mt-6 p-6 sm:p-8">
            <form action="{{ route('products.product-variants.update', [$product, $productVariant]) }}" method="POST">
                @csrf
                @method('PUT')
                @php
                    $selectedByVariant = $productVariant->variantOptions->pluck('id', 'variant_id');
                @endphp
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ($variants as $variant)
                        <div class="field">
                            <label for="option_{{ $variant->id }}" class="label">{{ $variant->name }}</label>
                            <select id="option_{{ $variant->id }}" name="options[{{ $variant->id }}]" class="select">
                                <option value="">Select an option</option>
                                @foreach ($variant->variantOptions as $option)
                                    <option value="{{ $option->id }}" @selected(old("options.{$variant->id}", $selectedByVariant->get($variant->id)) == $option->id)>{{ $option->name }}</option>
                                @endforeach
                            </select>
                            @error('options')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                    <div class="field">
                        <label for="price" class="label">Price</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $productVariant->price) }}" class="input">
                        @error('price')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="stock" class="label">Stock</label>
                        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $productVariant->stock) }}" class="input">
                        @error('stock')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('products.product-variants.show', [$product, $productVariant]) }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update product variant</button>
                </div>
            </form>
        </div>
    </div>
@endsection