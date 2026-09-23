@extends('base')

@section('content')
    <h1>Categories</h1>
    <p><a href="{{ route('categories.create') }}">Create Category</a></p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Products</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ str_repeat('&nbsp;&nbsp;', $category->depth) }}{{ $category->depth > 0 ? '└ ' : '' }}{{ $category->name }}</td>
                <td>{{ $category->products_count }}</td>
                <td>
                    <a href="{{ route('categories.edit', $category) }}">Edit</a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?');">
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