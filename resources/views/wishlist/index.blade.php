@extends('base')

@section('title', 'Your Wishlist - ' . config('app.name'))

@section('content')
    <h1 class="page-title animate-fade-in-up">Your Wishlist</h1>

    @if ($wishlist->isEmpty())
        <div class="card mt-8 flex flex-col items-center gap-4 px-6 py-16 text-center animate-fade-in-up">
            <span class="text-6xl" aria-hidden="true">&#129505;</span>
            <h2 class="text-lg font-bold text-stone-900">Your wishlist is empty</h2>
            <p class="max-w-sm text-sm text-stone-500">Save the things you love and come back for them later.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary">Browse the shop</a>
        </div>
    @else
        <div class="table-wrap mt-8 animate-fade-in-up">
            <table class="table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Options</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($wishlist as $wishlistItem)
                    <tr>
                        <td class="font-medium text-stone-800">{{ $wishlistItem->product_name }}</td>
                        <td class="text-stone-500">{{ $wishlistItem->variant_label ?: '-' }}</td>
                        <td>${{ $wishlistItem->unit_price }}</td>
                        <td>
                            @if (($wishlistItem->stock ?? 0) > 0)
                                <span class="badge-green">{{ $wishlistItem->stock }} in stock</span>
                            @else
                                <span class="badge-red">Out of stock</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                @if (($wishlistItem->stock ?? 0) > 0)
                                    <form action="{{ route('cart.store') }}" method="POST" class="inline">
                                        @csrf
                                        @if ($wishlistItem->product_variant_id !== null)
                                            <input type="hidden" name="product_variant_id" value="{{ $wishlistItem->product_variant_id }}">
                                        @else
                                            <input type="hidden" name="product_id" value="{{ $wishlistItem->product_id }}">
                                        @endif
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-primary btn-sm">Add to cart</button>
                                    </form>
                                @endif
                                <form action="{{ route('wishlist.destroy', $wishlistItem) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm btn-ghost text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection