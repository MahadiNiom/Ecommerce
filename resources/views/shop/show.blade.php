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
                    @php
                        $canPurchase = $product->stock === null || $product->stock > 0;
                        $maxQuantity = $product->stock === null ? 99 : min($product->stock, 99);
                    @endphp
                    @if ($product->price !== null)
                        <p class="text-sm text-stone-500">Price</p>
                        <p class="text-3xl font-bold text-emerald-700">${{ $product->price }}</p>

                        @if ($canPurchase)
                            <div class="mt-4 flex items-center gap-2">
                                @if ($product->stock === null)
                                    <span class="badge-stone rounded-lg">Stock not tracked</span>
                                @else
                                    <span class="badge-green rounded-lg">In stock: {{ $product->stock }}</span>
                                @endif
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
                                            <input type="number" x-model.number="qty" min="1" max="{{ $maxQuantity }}" class="input text-center">
                                            <button type="button" @click="qty = Math.min({{ $maxQuantity }}, qty + 1)" class="btn-outline btn-sm !px-3">+</button>
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
                    @php
                        $firstPv = $product->productVariants->first();
                        $variantTypes = $product->variants
                            ->map(fn (App\Models\Variant $variant) => [
                                'id' => $variant->id,
                                'name' => $variant->name,
                                'options' => $variant->variantOptions
                                    ->map(fn (App\Models\VariantOption $option) => ['id' => $option->id, 'name' => $option->name])
                                    ->values(),
                            ])
                            ->values();
                        $variantCombinations = $product->productVariants
                            ->map(fn (App\Models\ProductVariant $pv) => [
                                'id' => $pv->id,
                                'price' => (float) $pv->price,
                                'stock' => $pv->stock,
                                'option_ids' => $pv->variantOptions->pluck('id')->map(fn (int $id) => $id)->values(),
                            ])
                            ->values();
                    @endphp

                    <div
                        x-data="{
                            types: @js($variantTypes),
                            combinations: @js($variantCombinations),
                            selection: {},
                            selected: null,
                            selectedLabel: '',
                            selectedPrice: '',
                            selectedStock: 0,
                            qty: 1,
                            init() {
                                this.types.forEach((type) => {
                                    if (type.options.length > 0) {
                                        this.selection[type.id] = type.options[0].id;
                                    }
                                });
                                this.recompute();
                            },
                            select(typeId, optionId) {
                                this.selection[typeId] = optionId;
                                this.recompute();
                                this.qty = Math.max(1, Math.min(99, this.qty, this.selectedStock || 1));
                            },
                            isSelected(typeId, optionId) {
                                return this.selection[typeId] === optionId;
                            },
                            recompute() {
                                const isComplete = this.types.every((type) => this.selection[type.id] !== undefined);
                                const chosen = this.types.map((type) => this.selection[type.id]);
                                this.selected = isComplete
                                    ? this.combinations.find((combination) => chosen.every((id) => combination.option_ids.includes(id))) ?? null
                                    : null;

                                if (this.selected) {
                                    this.selectedLabel = this.types
                                        .map((type) => {
                                            const option = type.options.find((option) => option.id === this.selection[type.id]);
                                            return option ? `${type.name}: ${option.name}` : null;
                                        })
                                        .filter(Boolean)
                                        .join(' / ');
                                    this.selectedPrice = Number(this.selected.price).toFixed(2);
                                    this.selectedStock = this.selected.stock;
                                } else {
                                    this.selectedLabel = '';
                                    this.selectedPrice = '';
                                    this.selectedStock = 0;
                                }
                            }
                        }">
                        <p class="text-sm text-stone-500">Select options</p>

                        <template x-for="type in types" :key="`type-${type.id}`">
                            <div class="mt-4">
                                <label class="label" x-text="type.name"></label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="option in type.options" :key="`option-${option.id}`">
                                        <button type="button"
                                            @click="select(type.id, option.id)"
                                            class="rounded-xl border px-3 py-1.5 text-sm font-medium transition-colors"
                                            :class="isSelected(type.id, option.id)
                                                ? 'border-emerald-600 bg-emerald-600 text-white shadow-soft'
                                                : 'border-stone-300 bg-white text-stone-600 hover:border-emerald-400 hover:text-emerald-700'"
                                            x-text="option.name"></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="mt-5" x-show="selected">
                            <p class="text-sm font-semibold text-stone-500"><span x-text="selectedLabel">{{ $firstPv?->combinationLabel() }}</span></p>
                            <p class="mt-1 text-3xl font-bold text-emerald-700"><span x-text="selectedPrice">${{ $firstPv ? number_format((float) $firstPv->price, 2) : '0.00' }}</span></p>
                            <span class="mt-2 inline-block rounded-full px-2 py-0.5 text-xs font-semibold"
                                :class="selectedStock > 0 ? 'badge-green' : 'badge-red'">
                                <span x-text="selectedStock > 0 ? selectedStock + ' in stock' : 'Out of stock'">{{ $firstPv && $firstPv->stock > 0 ? $firstPv->stock.' in stock' : 'Out of stock' }}</span>
                            </span>
                        </div>

                        <div class="mt-6 rounded-xl bg-stone-50 p-4 text-center" x-cloak x-show="selected && selectedStock < 1">
                            <p class="text-sm font-medium text-stone-600">This combination is out of stock.</p>
                        </div>

                        <div class="mt-6 rounded-xl bg-amber-50 p-4 text-center" x-cloak x-show="!selected">
                            <p class="text-sm font-medium text-amber-800">This combination is not available right now.</p>
                        </div>

                        @auth
                            <div class="mt-6 space-y-3" x-show="selected && selectedStock > 0" x-cloak>
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_variant_id" :value="selected.id" value="{{ $firstPv?->id }}">
                                    <input type="hidden" name="quantity" :value="qty">
                                    <label class="label">Quantity</label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="btn-outline btn-sm !px-3">&#8722;</button>
                                        <input type="number" x-model.number="qty" :max="Math.min(selectedStock, 99)" min="1" class="input text-center">
                                        <button type="button" @click="qty = Math.min(selectedStock, 99, qty + 1)" class="btn-outline btn-sm !px-3">+</button>
                                    </div>
                                    <button type="submit" class="btn-primary mt-4 w-full">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                                        Add to cart
                                    </button>
                                </form>
                                <form action="{{ route('wishlist.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_variant_id" :value="selected.id" value="{{ $firstPv?->id }}">
                                    <button type="submit" class="btn-outline w-full">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                        Add to wishlist
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-6 rounded-xl bg-stone-50 p-4 text-center" x-show="selected && selectedStock > 0" x-cloak>
                                <p class="text-sm text-stone-600">Ready to order?</p>
                                <a href="{{ route('login') }}" class="btn-primary mt-3 w-full">Log in to buy</a>
                            </div>
                        @endauth
                    </div>
                @endif
            </div>
        </aside>
    </div>
@endsection