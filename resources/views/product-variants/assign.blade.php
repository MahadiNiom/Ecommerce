@extends('base')

@section('title', 'Assign Product Variants - ' . config('app.name'))

@section('content')
    <a href="{{ route('products.show', $product) }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to product
    </a>

    <div class="mt-6 animate-fade-in-up">
        <h1 class="page-title">Assign product variants</h1>
        <p class="page-subtitle">Generate pricing and stock for every combination of {{ $product->name }}.</p>
    </div>

    @if (count($combinations) === 0)
        <div class="card mt-8 flex flex-col items-center gap-4 px-6 py-16 text-center animate-fade-in-up">
            <span class="text-5xl" aria-hidden="true">&#10060;</span>
            <h2 class="text-lg font-bold text-stone-900">No combinations available</h2>
            <p class="max-w-sm text-sm text-stone-500">Add a variant with options first to generate combinations.</p>
            <a href="{{ route('products.variants.create', $product) }}" class="btn-primary">Add a variant</a>
        </div>
    @else
        <form action="{{ route('products.product-variants.assign.store', $product) }}" method="POST" class="card mt-8 p-6 animate-fade-in-up [animation-delay:0.1s]">
            @csrf
            <div class="table-wrap border-0 shadow-none">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="w-14">Assign</th>
                        <th>Combination</th>
                        <th class="w-40">Price</th>
                        <th class="w-40">Stock</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($combinations as $combination)
                        @php
                            $key = $combination['key'];
                            $existingPv = $existing->get($key);
                            $checked = old("combinations.{$key}.selected", $existingPv ? '1' : '0');
                        @endphp
                        <tr>
                            <td>
                                <input type="hidden" name="combinations[{{ $key }}][option_ids]" value="{{ implode(',', $combination['option_ids']) }}">
                                <input type="hidden" name="combinations[{{ $key }}][selected]" value="0">
                                <input type="checkbox" name="combinations[{{ $key }}][selected]" value="1" class="checkbox" @checked($checked === '1')>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($combination['pairs'] as $pair)
                                        <span class="badge-blue">{{ $pair['variant_name'] }}: {{ $pair['option_name'] }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <input type="number" name="combinations[{{ $key }}][price]" step="0.01" min="0" value="{{ old("combinations.{$key}.price", $existingPv?->price ?? '') }}" class="input">
                                @error("combinations.{$key}.price")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                            <td>
                                <input type="number" name="combinations[{{ $key }}][stock]" min="0" value="{{ old("combinations.{$key}.stock", $existingPv?->stock ?? '') }}" class="input">
                                @error("combinations.{$key}.stock")
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('products.show', $product) }}" class="btn-ghost">Cancel</a>
                <button type="submit" class="btn-primary">Save assignments</button>
            </div>
        </form>
    @endif
@endsection