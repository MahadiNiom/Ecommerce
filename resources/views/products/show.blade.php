@extends('base')

@section('title', $product->name . ' - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4 animate-fade-in-up">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">Product</p>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle">{{ $product->category?->name ?: 'No category' }} &middot; {{ $product->brand?->name ?: 'No brand' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.index') }}" class="btn-ghost">Back</a>
            <a href="{{ route('products.edit', $product) }}" class="btn-primary">Edit product</a>
        </div>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-2">
        {{-- Variant types --}}
        <div class="card animate-fade-in-up">
            <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                <h2 class="text-lg font-bold text-stone-900">Variant types</h2>
                <a href="{{ route('products.variants.create', $product) }}" class="btn-primary btn-sm">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add variant
                </a>
            </div>
            @if ($product->variants->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-stone-500">No variants defined yet.</div>
            @else
                <ul class="divide-y divide-stone-100">
                    @foreach ($product->variants as $variant)
                        <li class="flex items-center justify-between px-6 py-4 transition-colors hover:bg-emerald-50/50">
                            <a href="{{ route('products.variants.show', [$product, $variant]) }}" class="font-medium text-stone-900 transition-colors hover:text-emerald-700">{{ $variant->name }}</a>
                            <span class="badge-stone">{{ $variant->variantOptions->count() }} option{{ $variant->variantOptions->count() === 1 ? '' : 's' }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Product variants (SKUs) --}}
        <div class="card animate-fade-in-up [animation-delay:0.1s]">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-100 px-6 py-4">
                <h2 class="text-lg font-bold text-stone-900">Pricing & stock</h2>
                <div class="flex gap-2">
                    <a href="{{ route('products.product-variants.assign', $product) }}" class="btn-outline btn-sm">Generate by combination</a>
                    <a href="{{ route('products.product-variants.create', $product) }}" class="btn-primary btn-sm">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add
                    </a>
                </div>
            </div>

            @if ($product->productVariants->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-stone-500">
                    @if ($product->price !== null)
                        Product price: <span class="font-bold text-emerald-700">${{ $product->price }}</span> / Stock: {{ $product->stock }}
                    @else
                        No product variants and no base price set.
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Combination</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($product->productVariants as $productVariant)
                            <tr>
                                <td class="font-medium text-stone-800">{{ $productVariant->combinationLabel() }}</td>
                                <td>${{ $productVariant->price }}</td>
                                <td>
                                    @if ($productVariant->stock > 0)
                                        <span class="badge-green">{{ $productVariant->stock }}</span>
                                    @else
                                        <span class="badge-red">{{ $productVariant->stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('products.product-variants.edit', [$product, $productVariant]) }}" class="btn-outline btn-sm">Edit</a>
                                        <form action="{{ route('products.product-variants.destroy', [$product, $productVariant]) }}" method="POST" onsubmit="return confirm('Delete this product variant?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection