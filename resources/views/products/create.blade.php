@extends('base')

@section('title', 'Create Product - ' . config('app.name'))

@section('content')
    <a href="{{ route('products.index') }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to products
    </a>

    <div class="mt-6 max-w-2xl animate-fade-in-up">
        <h1 class="page-title">Create product</h1>
        <div class="card mt-6 p-6 sm:p-8">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="field sm:col-span-2">
                        <label for="name" class="label">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="input" placeholder="Heirloom tomatoes">
                        @error('name')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="price" class="label">Price</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" class="input" placeholder="0.00">
                        @error('price')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="stock" class="label">Stock</label>
                        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock') }}" class="input" placeholder="0">
                        @error('stock')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <p class="mb-5 -mt-2 text-xs text-stone-500">Price and stock apply only to products without variants.</p>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="field">
                        <label for="category_id" class="label">Category</label>
                        <select id="category_id" name="category_id" class="select">
                            <option value="">-- None --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="brand_id" class="label">Brand</label>
                        <select id="brand_id" name="brand_id" class="select">
                            <option value="">-- None --</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="description" class="label">Description</label>
                    <textarea id="description" name="description" rows="4" class="input" placeholder="Tell customers about this product...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <fieldset class="fieldset">
                    <legend class="legend">Tags</legend>
                    @forelse ($tags as $tag)
                        <label class="mb-2 flex items-center gap-2 text-sm text-stone-700 last:mb-0">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="checkbox"
                                   {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                            {{ $tag->name }}
                        </label>
                    @empty
                        <p class="text-sm text-stone-500">No tags exist yet. <a href="{{ route('tags.create') }}" class="link">Create one</a>.</p>
                    @endforelse
                    @error('tags')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </fieldset>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Create product</button>
                </div>
            </form>
        </div>
    </div>
@endsection