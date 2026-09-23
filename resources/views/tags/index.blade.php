@extends('base')

@section('content')
    <h1>Tags</h1>
    <p><a href="{{ route('tags.create') }}">Create Tag</a></p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Products</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tags as $tag)
            <tr>
                <td>{{ $tag->name }}</td>
                <td>{{ $tag->products_count }}</td>
                <td>
                    <a href="{{ route('tags.edit', $tag) }}">Edit</a>
                    <form action="{{ route('tags.destroy', $tag) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this tag?');">
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