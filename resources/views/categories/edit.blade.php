@extends('base')

@section('content')
    <h1>Edit Category: {{ $category->name }}</h1>
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="parent_id">Parent Category</label>
            <select id="parent_id" name="parent_id">
                <option value="">-- None (top level) --</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ str_repeat('&nbsp;&nbsp;', $parent->depth) }}{{ $parent->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <p>
            <button type="submit">Save</button>
            <a href="{{ route('categories.index') }}">Cancel</a>
        </p>
    </form>
@endsection