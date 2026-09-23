@extends('base')

@section('title', 'Brands - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Brands</h1>
            <p class="page-subtitle">Manage the brands behind your products.</p>
        </div>
        @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::CREATE_BRANDS]))
        <a href="{{ route('brands.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create brand
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
            @foreach ($brands as $brand)
                <tr>
                    <td class="font-medium text-stone-900">{{ $brand->name }}</td>
                    <td><span class="badge-stone">{{ $brand->products_count }}</span></td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_BRANDS]))
                                <a href="{{ route('brands.edit', $brand) }}" class="btn-outline btn-sm">Edit</a>
                            @endif
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::DELETE_BRANDS]))
                                <form action="{{ route('brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Delete this brand?');">
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