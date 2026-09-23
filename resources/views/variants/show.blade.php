@extends('base')

@section('title', $variant->name . ' - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4 animate-fade-in-up">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">{{ $product->name }}</p>
            <h1 class="page-title">{{ $variant->name }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.variants.index', $product) }}" class="btn-ghost">Back to variants</a>
            <a href="{{ route('products.variants.edit', [$product, $variant]) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="card mt-8 animate-fade-in-up">
        <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
            <h2 class="text-lg font-bold text-stone-900">Variant options</h2>
            <a href="{{ route('variants.variant-options.create', $variant) }}" class="btn-primary btn-sm">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add option
            </a>
        </div>
        @if ($variant->variantOptions->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-stone-500">No options yet. Add values like "Small" or "Red".</div>
        @else
            <ul class="divide-y divide-stone-100">
                @foreach ($variant->variantOptions as $option)
                    <li class="flex items-center justify-between px-6 py-3.5 transition-colors hover:bg-emerald-50/50">
                        <a href="{{ route('variants.variant-options.show', [$variant, $option]) }}" class="font-medium text-stone-900 transition-colors hover:text-emerald-700">{{ $option->name }}</a>
                        <a href="{{ route('variants.variant-options.edit', [$variant, $option]) }}" class="btn-outline btn-sm">Edit</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection