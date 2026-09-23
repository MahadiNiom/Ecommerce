@extends('base')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
    <h1 class="page-title animate-fade-in-up">Checkout</h1>

    @if ($cart->items->isEmpty())
        <div class="card mt-8 flex flex-col items-center gap-4 px-6 py-16 text-center animate-fade-in-up">
            <span class="text-6xl" aria-hidden="true">&#128722;</span>
            <h2 class="text-lg font-bold text-stone-900">Your cart is empty</h2>
            <a href="{{ route('shop.index') }}" class="btn-primary">Browse the shop</a>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_420px]">
            {{-- Shipping form --}}
            <div class="card p-6 animate-fade-in-up sm:p-8">
                <h2 class="font-display text-xl font-bold text-stone-900">Shipping details</h2>
                <p class="mt-1 text-sm text-stone-500">Tell us where to deliver your order.</p>

                <form action="{{ route('checkout.store') }}" method="POST" class="mt-6">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="field sm:col-span-2">
                            <label for="shipping_name" class="label">Full name</label>
                            <input id="shipping_name" type="text" name="shipping_name" value="{{ old('shipping_name') }}" required placeholder="Jane Doe" class="input">
                            @error('shipping_name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field sm:col-span-2">
                            <label for="shipping_address" class="label">Street address</label>
                            <input id="shipping_address" type="text" name="shipping_address" value="{{ old('shipping_address') }}" required placeholder="123 Garden Lane" class="input">
                            @error('shipping_address')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="shipping_city" class="label">City</label>
                            <input id="shipping_city" type="text" name="shipping_city" value="{{ old('shipping_city') }}" required placeholder="Green Valley" class="input">
                            @error('shipping_city')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="shipping_zip" class="label">Zip / Postal code</label>
                            <input id="shipping_zip" type="text" name="shipping_zip" value="{{ old('shipping_zip') }}" required placeholder="12345" class="input">
                            @error('shipping_zip')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field sm:col-span-2">
                            <label for="shipping_country" class="label">Country</label>
                            <input id="shipping_country" type="text" name="shipping_country" value="{{ old('shipping_country') }}" required placeholder="Plantlandia" class="input">
                            @error('shipping_country')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('cart.index') }}" class="btn-ghost">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                            Back to cart
                        </a>
                        <button type="submit" class="btn-amber w-full sm:w-auto">
                            Place order
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Order summary --}}
            <aside class="animate-fade-in-up [animation-delay:0.1s]">
                <div class="card sticky top-24 p-6">
                    <h2 class="text-lg font-bold text-stone-900">Order summary</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($cart->items as $item)
                            <div class="flex items-start justify-between gap-3 border-b border-dashed border-stone-200 pb-3 text-sm">
                                <div>
                                    <p class="font-medium text-stone-800">{{ $item->product_name }}</p>
                                    <p class="text-xs text-stone-500">{{ $item->variant_label ?: 'No options' }} &times; {{ $item->quantity }}</p>
                                </div>
                                <span class="shrink-0 font-semibold text-stone-800">${{ $item->line_total }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 flex items-baseline justify-between">
                        <span class="font-semibold text-stone-900">Total</span>
                        <span class="text-2xl font-bold text-emerald-700">${{ $cart->total() }}</span>
                    </div>
                </div>
            </aside>
        </div>
    @endif
@endsection