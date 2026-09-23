@extends('base')

@section('title', 'Variants for ' . $product->name)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">Variants for {{ $product->name }}</p>
            <h1 class="page-title">Variant types</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.show', $product) }}" class="btn-ghost">Back to product</a>
            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_PRODUCTS]))
                <a href="{{ route('products.variants.create', $product) }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add variant
                </a>
            @endif
        </div>
    </div>

    <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.1s]">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Options</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variants as $variant)
                <tr>
                    <td>
                        <a href="{{ route('products.variants.show', [$product, $variant]) }}" class="font-medium text-stone-900 transition-colors hover:text-emerald-700">{{ $variant->name }}</a>
                    </td>
                    <td><span class="badge-stone">{{ $variant->variantOptions->count() }}</span></td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('products.variants.show', [$product, $variant]) }}" class="btn-outline btn-sm">View</a>
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_PRODUCTS]))
                                <a href="{{ route('products.variants.edit', [$product, $variant]) }}" class="btn-outline btn-sm">Edit</a>
                            @endif
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::DELETE_PRODUCTS]))
                                <form action="{{ route('products.variants.destroy', [$product, $variant]) }}" method="POST" onsubmit="return confirm('Delete this variant?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection