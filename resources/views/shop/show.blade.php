@extends('base')

@section('title', $product->name . ' - ' . config('app.name'))

@section('content')
    <a href="{{ route('shop.index') }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to shop
    </a>

    <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_360px]">
        {{-- Main --}}
        <div class="animate-fade-in-up">
            <div class="shimmer-bg flex h-64 items-center justify-center overflow-hidden rounded-3xl text-8xl sm:h-80">
                {{ mb_substr($product->name, 0, 1) }}
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-2">
                @if ($product->category)
                    <a href="{{ route('shop.index', ['category' => $product->category_id]) }}" class="badge-green transition-transform hover:scale-105">Category: {{ $product->category->name }}</a>
                @endif
                @if ($product->brand)
                    <a href="{{ route('shop.index', ['brand' => $product->brand_id]) }}" class="badge-stone transition-transform hover:scale-105">Brand: {{ $product->brand->name }}</a>
                @endif
            </div>

            <h1 class="mt-3 font-display text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">{{ $product->name }}</h1>

            @if ($product->description)
                <p class="mt-4 leading-relaxed text-stone-600">{{ $product->description }}</p>
            @endif

            @if ($product->tags->isNotEmpty())
                <div class="mt-5 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wide text-stone-400">Tags:</span>
                    @foreach ($product->tags as $tag)
                        <a href="{{ route('shop.index', ['tag' => $tag->id]) }}" class="badge-amber transition-transform hover:scale-105">#{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Purchase card --}}
        <aside class="animate-fade-in-up [animation-delay:0.1s]">
            <div class="card sticky top-24 p-6">
                @if ($product->productVariants->isEmpty())
                    @if ($product->price !== null)
                        <p class="text-sm text-stone-500">Price</p>
                        <p class="text-3xl font-bold text-emerald-700">${{ $product->price }}</p>

                        @if ($product->stock > 0)
                            <div class="mt-4 flex items-center gap-2">
                                <span class="badge-green rounded-lg">In stock: {{ $product->stock }}</span>
                            </div>
                            @auth
                                <div class="mt-6 space-y-3" x-data="{ qty: 1 }">
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" :value="qty">
                                        <label class="label">Quantity</label>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="btn-outline btn-sm !px-3">&#8722;</button>
                                            <input type="number" x-model.number="qty" min="1" max="{{ min($product->stock, 99) }}" class="input text-center">
                                            <button type="button" @click="qty = Math.min({{ min($product->stock, 99) }}, qty + 1)" class="btn-outline btn-sm !px-3">+</button>
                                        </div>
                                        <button type="submit" class="btn-primary mt-4 w-full">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                                            Add to cart
                                        </button>
                                    </form>
                                    <form action="{{ route('wishlist.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" class="btn-outline w-full">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                            Add to wishlist
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="mt-6 rounded-xl bg-stone-50 p-4 text-center">
                                    <p class="text-sm text-stone-600">Ready to order?</p>
                                    <a href="{{ route('login') }}" class="btn-primary mt-3 w-full">Log in to buy</a>
                                </div>
                            @endauth
                        @else
                            <span class="badge-red mt-3">Out of stock</span>
                        @endif
                    @else
                        <div class="rounded-xl bg-stone-50 p-4 text-sm text-stone-600">
                            This product is not available for purchase yet.
                        </div>
                    @endif
                @else
                    <p class="text-sm text-stone-500">Starting price</p>
                    <p class="text-3xl font-bold text-emerald-700">From ${{ number_format($product->productVariants->min('price'), 2) }}</p>
                    <p class="mt-3 text-sm text-stone-500">Choose a variant below to add to your cart.</p>
                @endif
            </div>
        </aside>
    </div>

    {{-- Variants --}}
    @if ($product->productVariants->isNotEmpty())
        <section class="mt-12 animate-fade-in-up [animation-delay:0.2s]">
            <h2 class="text-2xl font-bold text-stone-900">Available variants</h2>
            <div class="table-wrap mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Options</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($product->productVariants as $variant)
                        <tr>
                            <td class="font-medium text-stone-800">{{ $variant->combinationLabel() }}</td>
                            <td>${{ $variant->price }}</td>
                            <td>
                                @if ($variant->stock > 0)
                                    <span class="badge-green">{{ $variant->stock }} in stock</span>
                                @else
                                    <span class="badge-red">Out of stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    @if ($variant->stock > 0)
                                        @auth
                                            <form action="{{ route('cart.store') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn-primary btn-sm">Add to cart</button>
                                            </form>
                                            <form action="{{ route('wishlist.store') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                                <button type="submit" class="btn-outline btn-sm">&#9825;</button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}" class="btn-primary btn-sm">Log in to buy</a>
                                        @endauth
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
@endsection