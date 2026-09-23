@extends('base')

@section('content')
    <h1>Shop</h1>

    <form action="{{ route('shop.index') }}" method="GET">
        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">All</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ str_repeat('&nbsp;&nbsp;', $category->depth) }}{{ $category->name }}
                </option>
            @endforeach
        </select>

        <label for="brand">Brand</label>
        <select id="brand" name="brand">
            <option value="">All</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>

        <label for="tag">Tag</label>
        <select id="tag" name="tag">
            <option value="">All</option>
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                    {{ $tag->name }}
                </option>
            @endforeach
        </select>

        <button type="submit">Filter</button>
        <a href="{{ route('shop.index') }}">Clear</a>
    </form>

    @if ($products->isEmpty())
        <p>No products found.</p>
    @else
        <ul>
            @foreach($products as $product)
                <li>
                    <a href="{{ route('shop.show', $product) }}">{{ $product->name }}</a>
                    @if ($product->category)
                        <span>[{{ $product->category->name }}]</span>
                    @endif
                    @if ($product->brand)
                        <span>[{{ $product->brand->name }}]</span>
                    @endif
                    @if ($product->productVariants->isEmpty())
                        @if ($product->price !== null)
                            <span>${{ $product->price }}</span>
                        @else
                            <span>Unavailable</span>
                        @endif
                    @else
                        <span>From ${{ $product->productVariants->min('price') }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection