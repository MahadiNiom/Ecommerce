@extends('base')

@section('title', $variantOption->name . ' - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4 animate-fade-in-up">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">{{ $variant->name }}</p>
            <h1 class="page-title">{{ $variantOption->name }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('variants.variant-options.index', $variant) }}" class="btn-ghost">Back to options</a>
            <a href="{{ route('variants.variant-options.edit', [$variant, $variantOption]) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="card mt-8 max-w-xl animate-fade-in-up">
        <dl class="divide-y divide-stone-100 text-sm">
            <div class="flex justify-between px-6 py-4">
                <dt class="text-stone-500">Option</dt>
                <dd class="font-medium text-stone-900">{{ $variantOption->name }}</dd>
            </div>
            <div class="flex justify-between px-6 py-4">
                <dt class="text-stone-500">Variant</dt>
                <dd>
                    <a href="{{ route('products.variants.show', [$variantOption->variant->product_id, $variant]) }}" class="link">{{ $variant->name }}</a>
                </dd>
            </div>
        </dl>
    </div>
@endsection