@extends('base')

@section('title', 'Categories - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Categories</h1>
            <p class="page-subtitle">Organize your catalog into browsable categories.</p>
        </div>
        @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::CREATE_CATEGORIES]))
        <a href="{{ route('categories.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create category
        </a>
        @endif
    </div>

    <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.1s]">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Products</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>
                        <span class="font-medium text-stone-900">{!! str_repeat('&nbsp;&nbsp;', $category->depth) !!}{{ $category->depth > 0 ? '↳ ' : '' }}{{ $category->name }}</span>
                    </td>
                    <td><span class="badge-stone">{{ $category->products_count }}</span></td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_CATEGORIES]))
                                <a href="{{ route('categories.edit', $category) }}" class="btn-outline btn-sm">Edit</a>
                            @endif
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::DELETE_CATEGORIES]))
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection