@extends('base')

@section('title', 'Products - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Products</h1>
            <p class="page-subtitle">Manage your catalog, variants, and pricing.</p>
        </div>
        @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::CREATE_PRODUCTS]))
        <a href="{{ route('products.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create product
        </a>
        @endif
    </div>

    <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.1s]">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Variants</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="font-medium text-stone-900 transition-colors hover:text-emerald-700">{{ $product->name }}</a>
                        @if ($product->category)
                            <p class="mt-0.5 text-xs text-stone-500">{{ $product->category->name }}</p>
                        @endif
                    </td>
                    <td>
                        <span class="badge-stone">{{ $product->variants->count() }} variant{{ $product->variants->count() === 1 ? '' : 's' }}</span>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('products.show', $product) }}" class="btn-outline btn-sm">View</a>
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_PRODUCTS]))
                                <a href="{{ route('products.edit', $product) }}" class="btn-outline btn-sm">Edit</a>
                            @endif
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::DELETE_PRODUCTS]))
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?');">
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