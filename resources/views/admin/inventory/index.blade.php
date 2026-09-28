@extends('base')

@section('title', 'Inventory - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Inventory</h1>
            <p class="page-subtitle">Monitor availability and adjust tracked stock from one place.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-outline">Manage products</a>
    </div>

    @if (session('success'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="card p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Stock records</p>
            <p class="mt-2 text-2xl font-bold text-stone-900">{{ $summary['total'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Tracked</p>
            <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $summary['tracked'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Low stock</p>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ $summary['low'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Out of stock</p>
            <p class="mt-2 text-2xl font-bold text-red-600">{{ $summary['out'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Not tracked</p>
            <p class="mt-2 text-2xl font-bold text-stone-600">{{ $summary['untracked'] }}</p>
        </div>
    </div>

    <form action="{{ route('admin.inventory.index') }}" method="GET" class="card mt-6 p-4 sm:p-5">
        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_12rem_auto_auto] sm:items-end">
            <div>
                <label for="search" class="label">Search</label>
                <input id="search" name="search" value="{{ $search }}" class="input" placeholder="Search by product or variant">
            </div>
            <div>
                <label for="status" class="label">Availability</label>
                <select id="status" name="status" class="select">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                    <option value="low" {{ $status === 'low' ? 'selected' : '' }}>Low stock</option>
                    <option value="out" {{ $status === 'out' ? 'selected' : '' }}>Out of stock</option>
                    <option value="untracked" {{ $status === 'untracked' ? 'selected' : '' }}>Not tracked</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if ($search !== '' || $status !== 'all')
                <a href="{{ route('admin.inventory.index') }}" class="btn-ghost">Clear</a>
            @endif
        </div>
    </form>

    @if ($rows->isEmpty())
        <div class="card mt-8 flex flex-col items-center gap-3 px-6 py-16 text-center">
            <h2 class="text-lg font-bold text-stone-900">No stock records found</h2>
            <p class="max-w-sm text-sm text-stone-500">Try a different search or availability filter.</p>
        </div>
    @else
        <div class="table-wrap mt-8">
            <table class="table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Availability</th>
                    <th>Stock</th>
                    <th class="text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>
                            <p class="font-medium text-stone-900">{{ $row['name'] }}</p>
                            @if ($row['variant_label'] !== 'Base product')
                                <p class="mt-0.5 text-xs text-stone-500">{{ $row['variant_label'] }}</p>
                            @endif
                        </td>
                        <td><span class="badge-stone">{{ $row['type'] === 'variant' ? 'Variant' : 'Product' }}</span></td>
                        <td>
                            @if (! $row['tracked'])
                                <span class="badge-stone">Not tracked</span>
                            @elseif ($row['stock'] === 0)
                                <span class="badge-red">Out of stock</span>
                            @elseif ($row['stock'] <= 5)
                                <span class="badge-amber">Low stock</span>
                            @else
                                <span class="badge-green">In stock</span>
                            @endif
                        </td>
                        <td class="font-semibold text-stone-900">
                            {{ $row['tracked'] ? $row['stock'] : 'Unlimited' }}
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_PRODUCTS]))
                                    <a href="{{ $row['edit_url'] }}" class="btn-outline btn-sm">Edit</a>
                                @endif
                                @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_PRODUCTS]))
                                    <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="stockable_type" value="{{ $row['type'] }}">
                                        <input type="hidden" name="stockable_id" value="{{ $row['id'] }}">
                                        <label for="adjustment-{{ $row['type'] }}-{{ $row['id'] }}" class="sr-only">Adjustment for {{ $row['name'] }}</label>
                                        <input id="adjustment-{{ $row['type'] }}-{{ $row['id'] }}" name="adjustment" type="number" step="1" min="-999999" max="999999" required class="input h-9 w-24 px-2 py-1 text-sm" placeholder="+/-">
                                        <button type="submit" class="btn-primary btn-sm">Save</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
