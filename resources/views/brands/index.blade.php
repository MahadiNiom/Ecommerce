@extends('base')

@section('content')
    <h1>Brands</h1>
    <p><a href="{{ route('brands.create') }}">Create Brand</a></p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Products</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($brands as $brand)
            <tr>
                <td>{{ $brand->name }}</td>
                <td>{{ $brand->products_count }}</td>
                <td>
                    <a href="{{ route('brands.edit', $brand) }}">Edit</a>
                    <form action="{{ route('brands.destroy', $brand) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this brand?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection