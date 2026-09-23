@extends('base')

@section('title', $productVariant->combinationLabel() . ' - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4 animate-fade-in-up">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">{{ $product->name }}</p>
            <h1 class="page-title">{{ $productVariant->combinationLabel() }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.product-variants.index', $product) }}" class="btn-ghost">Back to product variants</a>
            <a href="{{ route('products.product-variants.edit', [$product, $productVariant]) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="card mt-8 max-w-xl animate-fade-in-up">
        <dl class="divide-y divide-stone-100 text-sm">
            <div class="flex justify-between px-6 py-4">
                <dt class="text-stone-500">Product</dt>
                <dd><a href="{{ route('products.show', $product) }}" class="link">{{ $product->name }}</a></dd>
            </div>
            <div class="flex justify-between px-6 py-4">
                <dt class="text-stone-500">Combination</dt>
                <dd class="font-medium text-stone-900">{{ $productVariant->combinationLabel() }}</dd>
            </div>
            <div class="flex justify-between px-6 py-4">
                <dt class="text-stone-500">Price</dt>
                <dd class="font-bold text-emerald-700">${{ $productVariant->price }}</dd>
            </div>
            <div class="flex justify-between px-6 py-4">
                <dt class="text-stone-500">Stock</dt>
                <dd>
                    @if ($productVariant->stock > 0)
                        <span class="badge-green">{{ $productVariant->stock }}</span>
                    @else
                        <span class="badge-red">{{ $productVariant->stock }}</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
@endsection