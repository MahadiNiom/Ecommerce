@extends('base')

@section('title', 'Options for ' . $variant->name)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">Options for {{ $variant->name }}</p>
            <h1 class="page-title">Variant options</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.variants.show', [$variant->product_id, $variant]) }}" class="btn-ghost">Back to variant</a>
            <a href="{{ route('variants.variant-options.create', $variant) }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add option
            </a>
        </div>
    </div>

    <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.1s]">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variantOptions as $variantOption)
                <tr>
                    <td>
                        <a href="{{ route('variants.variant-options.show', [$variant, $variantOption]) }}" class="font-medium text-stone-900 transition-colors hover:text-emerald-700">{{ $variantOption->name }}</a>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('variants.variant-options.show', [$variant, $variantOption]) }}" class="btn-outline btn-sm">View</a>
                            <a href="{{ route('variants.variant-options.edit', [$variant, $variantOption]) }}" class="btn-outline btn-sm">Edit</a>
                            <form action="{{ route('variants.variant-options.destroy', [$variant, $variantOption]) }}" method="POST" onsubmit="return confirm('Delete this option?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection