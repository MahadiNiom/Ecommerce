@extends("base")

@section("content")
    <h1>Assign Product Variants by Combination — {{ $product->name }}</h1>
    <p><a href="{{ route('products.show', $product) }}">Back to product</a></p>

    @if(count($combinations) === 0)
        <p>No combinations available. <a href="{{ route('products.variants.create', $product) }}">Add a variant</a> with options first.</p>
    @else
        <form action="{{ route('products.product-variants.assign.store', $product) }}" method="POST">
            @csrf
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                <tr>
                    <th>Assign</th>
                    <th>Combination</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
                </thead>
                <tbody>
                @foreach($combinations as $combination)
                    @php
                        $key = $combination['key'];
                        $existingPv = $existing->get($key);
                        $checked = old("combinations.{$key}.selected", $existingPv ? '1' : '0');
                    @endphp
                    <tr>
                        <td>
                            <input type="hidden" name="combinations[{{ $key }}][option_ids]" value="{{ implode(",", $combination['option_ids']) }}">
                            <input type="hidden" name="combinations[{{ $key }}][selected]" value="0">
                            <input type="checkbox" name="combinations[{{ $key }}][selected]" value="1" @checked($checked === '1')>
                        </td>
                        <td>
                            @foreach($combination['pairs'] as $pair)
                                {{ $pair['variant_name'] }}: {{ $pair['option_name'] }}@if(!$loop->last) / @endif
                            @endforeach
                        </td>
                        <td>
                            <input type="number" name="combinations[{{ $key }}][price]" step="0.01" min="0" value="{{ old("combinations.{$key}.price", $existingPv?->price ?? '') }}">
                            @error("combinations.{$key}.price")
                                <div>{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="number" name="combinations[{{ $key }}][stock]" min="0" value="{{ old("combinations.{$key}.stock", $existingPv?->stock ?? '') }}">
                            @error("combinations.{$key}.stock")
                                <div>{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button type="submit">Save Assignments</button>
        </form>
    @endif
@endsection