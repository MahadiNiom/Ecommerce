@extends('base')

@section('title', 'Your Cart - ' . config('app.name'))

@section('content')
    <h1 class="page-title animate-fade-in-up">Your Cart</h1>

    @if ($cart->items->isEmpty())
        <div class="card mt-8 flex flex-col items-center gap-4 px-6 py-16 text-center animate-fade-in-up">
            <span class="text-6xl" aria-hidden="true">&#128722;</span>
            <h2 class="text-lg font-bold text-stone-900">Your cart is empty</h2>
            <p class="max-w-sm text-sm text-stone-500">Looks like you haven't added anything just yet. Let's fix that!</p>
            <a href="{{ route('shop.index') }}" class="btn-primary">Browse the shop</a>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_340px]">
            {{-- Items --}}
            <div class="table-wrap animate-fade-in-up">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Options</th>
                        <th>Unit price</th>
                        <th>Quantity</th>
                        <th class="text-right">Line total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($cart->items as $item)
                        <tr>
                            <td class="font-medium text-stone-800">{{ $item->product_name }}</td>
                            <td class="text-stone-500">{{ $item->variant_label ?: '-' }}</td>
                            <td>${{ $item->unit_price }}</td>
                            <td>
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="input w-20 text-center">
                                    <button type="submit" class="btn-sm btn-outline shrink-0">Update</button>
                                </form>
                            </td>
                            <td class="text-right font-bold text-emerald-700">${{ $item->line_total }}</td>
                            <td>
                                <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm btn-ghost text-red-600 hover:bg-red-50 hover:text-red-700" title="Remove">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary --}}
            <aside class="animate-fade-in-up [animation-delay:0.1s]">
                <div class="card sticky top-24 p-6">
                    <h2 class="text-lg font-bold text-stone-900">Order summary</h2>
                    <div class="mt-5 space-y-3 border-b border-dashed border-stone-200 pb-5 text-sm">
                        <div class="flex justify-between text-stone-600">
                            <span>Items ({{ $cart->items->count() }})</span>
                            <span>${{ $cart->total() }}</span>
                        </div>
                        <div class="flex justify-between text-stone-600">
                            <span>Shipping</span>
                            <span class="badge-green">Calculated at checkout</span>
                        </div>
                    </div>
                    <div class="mt-5 flex items-baseline justify-between">
                        <span class="font-semibold text-stone-900">Total</span>
                        <span class="text-2xl font-bold text-emerald-700">${{ $cart->total() }}</span>
                    </div>
                    <a href="{{ route('checkout.create') }}" class="btn-amber mt-6 w-full">
                        Proceed to checkout
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('shop.index') }}" class="btn-ghost mt-2 w-full">Continue shopping</a>
                </div>
            </aside>
        </div>
    @endif
@endsection