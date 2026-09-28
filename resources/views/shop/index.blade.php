@extends('base')

@section('title', 'Shop - ' . config('app.name'))

@section('content')
    <div class="flex flex-col gap-1 animate-fade-in-up">
        <h1 class="page-title">Shop</h1>
        <p class="page-subtitle">
            @if (request('q'))
                {{ $products->count() }} match{{ $products->count() === 1 ? '' : 'es' }} for &ldquo;{{ request('q') }}&rdquo;
            @else
                {{ $products->count() }} product{{ $products->count() === 1 ? '' : 's' }} hand-picked for you
            @endif
        </p>
    </div>

    {{-- Filters --}}
    <form action="{{ route('shop.index') }}" method="GET" class="card mt-6 animate-fade-in-up p-4 [animation-delay:0.1s] sm:p-5">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_1fr_auto_auto]">
            <div>
                <label for="q" class="label">Search</label>
                <input id="q" name="q" value="{{ request('q') }}" class="input" placeholder="Search products" autocomplete="off">
            </div>
            <div>
                <label for="category" class="label">Category</label>
                <select id="category" name="category" class="select">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="brand" class="label">Brand</label>
                <select id="brand" name="brand" class="select">
                    <option value="">All brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tag" class="label">Tag</label>
                <select id="tag" name="tag" class="select">
                    <option value="">All tags</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary w-full sm:w-auto">Filter</button>
            </div>
            <div class="flex items-end">
                @if (request('q') || request('category') || request('brand') || request('tag'))
                    <a href="{{ route('shop.index') }}" class="btn-ghost w-full sm:w-auto">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Products --}}
    @if ($products->isEmpty())
        <div class="card mt-8 flex flex-col items-center gap-4 px-6 py-16 text-center">
            <span class="text-5xl" aria-hidden="true">&#127806;</span>
            <h2 class="text-lg font-bold text-stone-900">No products found</h2>
            <p class="max-w-sm text-sm text-stone-500">Try adjusting your filters, or browse the full collection.</p>
            <a href="{{ route('shop.index') }}" class="btn-outline">Browse everything</a>
        </div>
    @else
        <div class="stagger mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($products as $product)
                <a href="{{ route('shop.show', $product) }}" class="card-hover group flex flex-col overflow-hidden">
                    <div class="shimmer-bg flex h-40 items-center justify-center text-5xl">
                        <span class="transition-transform duration-300 group-hover:scale-125">{{ mb_substr($product->name, 0, 1) }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex flex-wrap gap-1.5">
                            @if ($product->category)
                                <span class="badge-green">{{ $product->category->name }}</span>
                            @endif
                            @if ($product->brand)
                                <span class="badge-stone">{{ $product->brand->name }}</span>
                            @endif
                        </div>
                        <h3 class="mt-3 font-semibold text-stone-900 transition-colors group-hover:text-emerald-700">{{ $product->name }}</h3>
                        <div class="mt-auto flex items-center justify-between pt-4">
                            @php $price = $product->priceLabel(); @endphp
                            @if ($price === null)
                                <span class="badge-stone">Unavailable</span>
                            @else
                                <span class="text-lg font-bold text-emerald-700">{{ $price }}</span>
                                @if ($product->productVariants->isEmpty())
                                    @if ($product->stock === null)
                                        <span class="badge-stone">Not tracked</span>
                                    @elseif ($product->stock < 1)
                                        <span class="badge-red">Out of stock</span>
                                    @else
                                        <span class="badge-green">In stock</span>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection